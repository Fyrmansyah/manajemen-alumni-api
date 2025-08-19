<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminNewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
        }

        $news = $query->paginate(15)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'required|in:info,achievement,job,event,announcement',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Set author_id to current admin user
        $validatedData['author_id'] = auth('admin')->id();

        // Generate slug
        $validatedData['slug'] = Str::slug($validatedData['title']);
        
        // Make slug unique
        $originalSlug = $validatedData['slug'];
        $counter = 1;
        while (News::where('slug', $validatedData['slug'])->exists()) {
            $validatedData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $validatedData['featured_image'] = $request->file('featured_image')
                ->store('news/images', 'public');
        }

        // Handle published_at
        if (!isset($validatedData['published_at']) && $validatedData['status'] === 'published') {
            $validatedData['published_at'] = now();
        }

        $news = News::create($validatedData);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Berita berhasil dibuat.');
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'required|in:info,achievement,job,event,announcement',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Update slug if title changed
        if ($validatedData['title'] !== $news->title) {
            $validatedData['slug'] = Str::slug($validatedData['title']);
            
            // Make slug unique
            $originalSlug = $validatedData['slug'];
            $counter = 1;
            while (News::where('slug', $validatedData['slug'])->where('id', '!=', $news->id)->exists()) {
                $validatedData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($news->featured_image && Storage::disk('public')->exists($news->featured_image)) {
                Storage::disk('public')->delete($news->featured_image);
            }
            
            $validatedData['featured_image'] = $request->file('featured_image')
                ->store('news/images', 'public');
        }

        // Handle published_at
        if (!isset($validatedData['published_at']) && $validatedData['status'] === 'published' && $news->status !== 'published') {
            $validatedData['published_at'] = now();
        }

        $news->update($validatedData);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        try {
            // Delete featured image if exists
            if ($news->featured_image && Storage::disk('public')->exists($news->featured_image)) {
                Storage::disk('public')->delete($news->featured_image);
            }

            $news->delete();

            return redirect()
                ->route('admin.news.index')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus berita.');
        }
    }
}
