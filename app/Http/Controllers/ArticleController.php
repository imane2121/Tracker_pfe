<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index()
    {
        $articles = Article::with(['author', 'tags'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('articles', compact('articles'));
    }

    /**
     * Display the specified article.
     */
    public function show($id)
    {
        $article = Article::with(['tags'])->findOrFail($id);
        Log::info('Full article:', $article->toArray());
        
        // Get related articles
        $relatedArticles = Article::where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->published()
            ->latest()
            ->take(3)
            ->get();

        // Get popular tags
        $popularTags = Tag::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->take(10)
            ->get();

        // Add view asset
        $cssPath = 'css/article.css';
        if (!file_exists(public_path($cssPath))) {
            $this->createArticleCssFile();
        }

        return view('articles.show', compact('article', 'relatedArticles', 'popularTags'));
    }

    private function createArticleCssFile()
    {
        $css = file_get_contents(resource_path('css/article.css'));
        file_put_contents(public_path('css/article.css'), $css);
    }

    /**
     * Display articles by category.
     */
    public function byCategory($category)
    {
        $articles = Article::with(['author', 'tags'])
            ->published()
            ->byCategory($category)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('articles', compact('articles', 'category'));
    }

    /**
     * Display articles by tag.
     */
    public function byTag($slug)
    {
        $articles = Article::with(['author', 'tags'])
            ->published()
            ->whereHas('tags', function($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $tag = Tag::where('slug', $slug)->firstOrFail();

        return view('articles', compact('articles', 'tag'));
    }

    /**
     * Display featured articles.
     */
    public function featured()
    {
        $articles = Article::with(['author', 'tags'])
            ->published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('articles', [
            'articles' => $articles,
            'title' => 'Featured Articles'
        ]);
    }
} 