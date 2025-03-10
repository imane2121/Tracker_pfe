@extends('layouts.app')

@section('content')
<div class="article-page">
    <!-- Article Header -->
    <div class="article-hero" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ $article->image_url ?? asset('assets/img/articles/default.jpg') }}')">
        <div class="container">
            <div class="article-hero-content" data-aos="fade-up">
                <div class="category-badge {{ $article->category }}">
                    {{ ucfirst($article->category) }}
                </div>
                <h1>{{ $article->title }}</h1>
                <div class="article-meta">
                    <div class="author">
                        <img src="{{ $article->author->profile_picture ?? asset('assets/img/default-avatar.png') }}" 
                             alt="{{ $article->author->name }}" 
                             class="author-avatar">
                        <div class="author-info">
                            <span class="author-name">{{ $article->author->name }}</span>
                            <span class="publish-date">
                                Published {{ $article->published_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="article-stats">
                        <span class="views">
                            <i class="far fa-eye"></i> {{ $article->view_count }} views
                        </span>
                        <span class="reading-time">
                            <i class="far fa-clock"></i> {{ ceil(str_word_count($article->content) / 200) }} min read
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <div class="article-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Main Content -->
                    <article class="main-content" data-aos="fade-up">
                        <div class="content-body">
                            {!! nl2br(e($article->content)) !!}
                        </div>

                        <!-- Tags -->
                        <div class="article-tags">
                            @foreach($article->tags as $tag)
                                <a href="{{ route('articles.tag', $tag->slug) }}" class="tag">
                                    # {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>

                        <!-- Share Buttons -->
                        <div class="share-buttons">
                            <span class="share-label">Share this article:</span>
                            <div class="share-icons">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" 
                                   target="_blank" 
                                   class="share-icon twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                                   target="_blank" 
                                   class="share-icon facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($article->title) }}" 
                                   target="_blank" 
                                   class="share-icon linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                    <!-- Author Bio -->
                    <div class="author-bio" data-aos="fade-up">
                        <img src="{{ $article->author->profile_picture ?? asset('assets/img/default-avatar.png') }}" 
                             alt="{{ $article->author->name }}" 
                             class="author-avatar">
                        <div class="author-info">
                            <h3>About {{ $article->author->name }}</h3>
                            <p>{{ $article->author->bio ?? 'Environmental advocate and content creator.' }}</p>
                            <div class="author-social">
                                @if($article->author->twitter)
                                    <a href="{{ $article->author->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                @endif
                                @if($article->author->linkedin)
                                    <a href="{{ $article->author->linkedin }}" target="_blank"><i class="fab fa-linkedin"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar">
                        <!-- Related Articles -->
                        <div class="sidebar-widget related-articles" data-aos="fade-left">
                            <h3>Related Articles</h3>
                            @foreach($relatedArticles as $relatedArticle)
                                <div class="related-article">
                                    <img src="{{ $relatedArticle->image_url ?? asset('assets/img/articles/default.jpg') }}" 
                                         alt="{{ $relatedArticle->title }}">
                                    <div class="related-article-info">
                                        <span class="category-badge small {{ $relatedArticle->category }}">
                                            {{ ucfirst($relatedArticle->category) }}
                                        </span>
                                        <h4>
                                            <a href="{{ route('articles.show', $relatedArticle->id) }}">
                                                {{ $relatedArticle->title }}
                                            </a>
                                        </h4>
                                        <span class="date">
                                            {{ $relatedArticle->published_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Popular Tags -->
                        <div class="sidebar-widget tags-widget" data-aos="fade-left">
                            <h3>Popular Tags</h3>
                            <div class="tags-cloud">
                                @foreach($popularTags as $tag)
                                    <a href="{{ route('articles.tag', $tag->slug) }}" class="tag">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.article-page {
    background-color: #f8f9fa;
}

/* Hero Section */
.article-hero {
    padding: 120px 0 60px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    color: #fff;
    position: relative;
}

.article-hero-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.article-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 20px 0;
    line-height: 1.3;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.category-badge {
    display: inline-block;
    padding: 6px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

/* Category Colors */
.category-badge.news { background-color: #3498db; }
.category-badge.educational { background-color: #2ecc71; }
.category-badge.awareness { background-color: #e74c3c; }
.category-badge.best_practices { background-color: #9b59b6; }
.category-badge.initiative { background-color: #f1c40f; }
.category-badge.report { background-color: #34495e; }
.category-badge.event { background-color: #e67e22; }

.article-meta {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    margin-top: 30px;
}

.author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

.author-info {
    text-align: left;
}

.author-name {
    display: block;
    font-weight: 600;
    font-size: 1.1rem;
}

.publish-date {
    font-size: 0.9rem;
    opacity: 0.9;
}

.article-stats {
    display: flex;
    gap: 20px;
    font-size: 0.9rem;
}

/* Content Section */
.article-content-wrapper {
    padding: 60px 0;
}

.main-content {
    background: #fff;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.content-body {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #2c3e50;
    margin-bottom: 30px;
}

.article-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
}

.tag {
    background: #f8f9fa;
    color: #6c757d;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.tag:hover {
    background: #e9ecef;
    color: #495057;
}

.share-buttons {
    display: flex;
    align-items: center;
    gap: 20px;
    padding-top: 30px;
    border-top: 1px solid #eee;
}

.share-label {
    font-weight: 600;
    color: #495057;
}

.share-icons {
    display: flex;
    gap: 15px;
}

.share-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: transform 0.3s ease;
}

.share-icon:hover {
    transform: translateY(-3px);
}

.share-icon.twitter { background: #1DA1F2; }
.share-icon.facebook { background: #4267B2; }
.share-icon.linkedin { background: #0077B5; }

/* Author Bio */
.author-bio {
    background: #fff;
    border-radius: 15px;
    padding: 30px;
    display: flex;
    gap: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.author-bio .author-avatar {
    width: 100px;
    height: 100px;
}

.author-bio .author-info h3 {
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.author-social {
    margin-top: 15px;
}

.author-social a {
    color: #6c757d;
    margin-right: 15px;
    font-size: 1.2rem;
    transition: color 0.3s ease;
}

.author-social a:hover {
    color: #3498db;
}

/* Sidebar */
.sidebar-widget {
    background: #fff;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.sidebar-widget h3 {
    margin-bottom: 20px;
    font-size: 1.3rem;
    color: #2c3e50;
}

.related-article {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.related-article:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.related-article img {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
}

.related-article-info h4 {
    font-size: 1rem;
    margin: 5px 0;
}

.related-article-info h4 a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-article-info h4 a:hover {
    color: #3498db;
}

.related-article-info .date {
    font-size: 0.8rem;
    color: #6c757d;
}

.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

/* Responsive Adjustments */
@media (max-width: 991px) {
    .article-hero h1 {
        font-size: 2rem;
    }

    .article-meta {
        flex-direction: column;
        gap: 20px;
    }

    .main-content {
        padding: 30px;
    }
}

@media (max-width: 768px) {
    .article-hero {
        padding: 100px 0 40px;
    }

    .article-hero h1 {
        font-size: 1.8rem;
    }

    .content-body {
        font-size: 1rem;
    }

    .author-bio {
        flex-direction: column;
        text-align: center;
    }

    .author-bio .author-avatar {
        margin: 0 auto;
    }

    .share-buttons {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

@media (max-width: 576px) {
    .article-hero h1 {
        font-size: 1.5rem;
    }

    .main-content {
        padding: 20px;
    }

    .article-stats {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
}
</style>
@endsection 