<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $article = Article::with(['author', 'tags'])
            ->published()
            ->findOrFail($id);

        // Increment view count
        $article->increment('view_count');

        // Get related articles based on tags
        $relatedArticles = Article::with(['author', 'tags'])
            ->published()
            ->where('id', '!=', $article->id)
            ->whereHas('tags', function($query) use ($article) {
                $query->whereIn('tags.id', $article->tags->pluck('id'));
            })
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('article', compact('article', 'relatedArticles'));
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