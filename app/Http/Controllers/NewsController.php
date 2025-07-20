<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('author')->published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $news = $query->latest('published_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function show($slug)
    {
        $article = News::with('author')
                      ->where('slug', $slug)
                      ->published()
                      ->firstOrFail();

        // Increment views
        $article->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => 'in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        $newsData = [
            'title' => $request->title,
            'content' => $request->content,
            'slug' => Str::slug($request->title),
            'status' => $request->status ?? 'draft',
            'author_id' => auth()->id(),
        ];

        if ($request->status === 'published') {
            $newsData['published_at'] = $request->published_at ?? now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('news', $filename, 'public');
            $newsData['featured_image'] = $filename;
        }

        $news = News::create($newsData);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dibuat',
            'data' => $news
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => 'in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        $newsData = [
            'title' => $request->title,
            'content' => $request->content,
            'slug' => Str::slug($request->title),
            'status' => $request->status ?? $news->status,
        ];

        if ($request->status === 'published' && !$news->published_at) {
            $newsData['published_at'] = $request->published_at ?? now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('news', $filename, 'public');
            $newsData['featured_image'] = $filename;
        }

        $news->update($newsData);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui',
            'data' => $news
        ]);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus'
        ]);
    }

    public function latest()
    {
        $latestNews = News::published()
                         ->latest('published_at')
                         ->limit(6)
                         ->get();

        return response()->json([
            'success' => true,
            'data' => $latestNews
        ]);
    }

    // Web view methods
    public function indexWeb(Request $request)
    {
        $query = News::where('status', 'published');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $news = $query->paginate(12)->withQueryString();

        // Get featured news (only for first page)
        $featuredNews = null;
        if ($request->get('page', 1) == 1) {
            $featuredNews = News::where('status', 'published')
                ->where('is_featured', true)
                ->latest()
                ->first();
        }

        // Get filter options
        $categories = News::where('status', 'published')
            ->groupBy('category')
            ->selectRaw('category, count(*) as count')
            ->pluck('count', 'category');

        // Get popular news for sidebar
        $popularNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        // Category options for filter
        $categoryOptions = [
            'job_fair' => 'Job Fair',
            'career_tips' => 'Tips Karir',
            'company_news' => 'Berita Perusahaan',
            'alumni_story' => 'Cerita Alumni',
            'announcement' => 'Pengumuman'
        ];

        return view('news.index', compact(
            'news', 
            'featuredNews', 
            'categories', 
            'popularNews', 
            'categoryOptions'
        ));
    }

    public function showWeb(News $news)
    {
        // Increment view count
        $news->increment('views');

        // Get previous and next news
        $previousNews = News::where('status', 'published')
            ->where('id', '<', $news->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextNews = News::where('status', 'published')
            ->where('id', '>', $news->id)
            ->orderBy('id', 'asc')
            ->first();

        // Get related news (same category)
        $relatedNews = News::where('status', 'published')
            ->where('category', $news->category)
            ->where('id', '!=', $news->id)
            ->latest()
            ->take(4)
            ->get();

        // Get latest news for sidebar
        $latestNews = News::where('status', 'published')
            ->where('id', '!=', $news->id)
            ->latest()
            ->take(5)
            ->get();

        // Get categories with count for sidebar
        $categories = News::where('status', 'published')
            ->groupBy('category')
            ->selectRaw('category, count(*) as count')
            ->pluck('count', 'category');

        return view('news.show', compact(
            'news', 
            'previousNews', 
            'nextNews', 
            'relatedNews', 
            'latestNews', 
            'categories'
        ));
    }
}
