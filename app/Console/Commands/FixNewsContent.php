<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;

class FixNewsContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fix-content {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix news content formatting and remove duplicated excerpts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        
        if ($id) {
            $this->fixSingleNews($id);
        } else {
            $this->fixAllNews();
        }
    }
    
    private function fixSingleNews($id)
    {
        $news = News::find($id);
        
        if (!$news) {
            $this->error("News with ID {$id} not found.");
            return;
        }
        
        $this->info("Fixing news: {$news->title}");
        $this->info("Current excerpt: " . ($news->excerpt ?? 'NULL'));
        $this->info("Content length: " . strlen($news->content) . " chars");
        $this->info("Content preview: " . substr(strip_tags($news->content), 0, 100) . "...");
        
        // Check if content itself has duplicate paragraphs
        $content = $news->content;
        $paragraphs = explode('<p>', $content);
        $uniqueParagraphs = array_unique($paragraphs);
        
        if (count($paragraphs) !== count($uniqueParagraphs)) {
            $this->info("Found duplicate paragraphs in content. Removing duplicates...");
            $content = implode('<p>', $uniqueParagraphs);
            $news->content = $content;
        }
        
        // Check if excerpt and content are similar
        if ($news->excerpt) {
            $contentText = strip_tags($news->content);
            $similar = similar_text($contentText, $news->excerpt, $percent);
            
            if ($percent > 80) {
                $this->info("Excerpt and content are {$percent}% similar. Clearing excerpt...");
                $news->excerpt = null;
            }
        }
        
        // Clean up content - remove duplicate text patterns
        $originalContent = $news->content;
        $cleanContent = $originalContent;
        
        // Remove excessive line breaks
        $cleanContent = preg_replace('/(<br\s*\/?>){3,}/', '<br><br>', $cleanContent);
        $cleanContent = preg_replace('/(\n\s*){3,}/', "\n\n", $cleanContent);
        
        // Clean up empty paragraphs
        $cleanContent = preg_replace('/<p>\s*<\/p>/', '', $cleanContent);
        
        // Remove duplicate consecutive paragraphs with same content
        $cleanContent = preg_replace('/(<p>.*?<\/p>)\s*\1/', '$1', $cleanContent);
        
        // Fix spacing around paragraphs
        $cleanContent = trim($cleanContent);
        
        if ($cleanContent !== $originalContent) {
            $this->info("Cleaning content...");
            $news->content = $cleanContent;
        }
        
        $news->save();
        $this->info("News updated successfully!");
        $this->info("Final content length: " . strlen($news->content) . " chars");
    }
    
    private function fixAllNews()
    {
        $allNews = News::all();
        
        foreach ($allNews as $news) {
            $this->fixSingleNews($news->id);
        }
        
        $this->info("All news processed!");
    }
}
