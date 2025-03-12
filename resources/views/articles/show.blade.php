@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/article.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="article-page">
    <!-- Hero Section -->
    @if($article->image)
    <div class="article-hero" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('{{ Storage::url($article->image) }}')">
    @else
    <div class="article-hero default-hero">
    @endif
        <div class="container">
            <div class="hero-content">
                <div class="category-badge">{{ ucfirst($article->category) }}</div>
                <h1>{{ $article->title }}</h1>
                
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Article Content -->
                <article class="article-content">
                    {!! $article->content !!}
                </article>

                <!-- Tags -->
                @if($article->tags->count() > 0)
                <div class="article-tags">
                    @foreach($article->tags as $tag)
                        <a href="{{ route('articles.tag', $tag->slug) }}" class="tag">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
                @endif

                <!-- Share Section -->
                <div class="share-section">
                    <h5>Share this article</h5>
                    <div class="share-buttons">
                        <a href="https://www.instagram.com/sharer.php?u=http%3A%2F%2F127.0.0.1%3A8000%2Farticles%2F3" target="_blank" class="share-btn instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text=Effective+Coastal+Cleanup+Strategies%20-%20http%3A%2F%2F127.0.0.1%3A8000%2Farticles%2F3" target="_blank" class="share-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" 
                           target="_blank" class="share-btn twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                           target="_blank" class="share-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(request()->url()) }}&title={{ urlencode($article->title) }}" 
                           target="_blank" class="share-btn linkedin">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Articles Section -->
    @if($relatedArticles->count() > 0)
    <div class="related-articles-section">
        <div class="container">
            <h3 class="section-title">Related Articles</h3>
            <div class="row">
                @foreach($relatedArticles as $related)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="article-card">
                        @if($related->image)
                        <div class="article-image" style="background-image: url('{{ Storage::url($related->image) }}')">
                        @else
                        <div class="article-image default-image">
                        @endif
                            <div class="category-badge">{{ ucfirst($related->category) }}</div>
                        </div>
                        <div class="article-card-content">
                            <h4><a href="{{ route('articles.show', $related->id) }}">{{ $related->title }}</a></h4>
                            <div class="article-meta">
                                <span><i class="far fa-calendar"></i> {{ $related->published_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 