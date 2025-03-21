@extends('layouts.app')

@section('content')
<style>
/* Report Callout Styles */
html body section.report-callout {
    padding: 0 !important;
    margin: 0 !important;
    position: relative !important;
    overflow: hidden !important;
}

html body section.report-callout .report-callout-wrapper {
    padding: 40px 0 !important;
    background: linear-gradient(135deg, #28266c 0%, #3498db 100%) !important;
    position: relative !important;
    overflow: hidden !important;
}

html body section.report-callout .report-callout-wrapper::before {
    content: '';
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E") !important;
    opacity: 0.6 !important;
    z-index: 1 !important;
}

html body section.report-callout .report-card {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border-radius: 15px !important;
    padding: 30px !important;
    position: relative !important;
    overflow: hidden !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    margin: 0 auto !important;
    max-width: 1000px !important;
    z-index: 2 !important;
}

html body section.report-callout .report-content {
    display: flex !important;
    align-items: center !important;
    gap: 25px !important;
    position: relative !important;
    z-index: 2 !important;
}

html body section.report-callout .report-icon {
    flex-shrink: 0 !important;
    width: 70px !important;
    height: 70px !important;
    background: rgba(255, 255, 255, 0.15) !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

html body section.report-callout .report-icon i {
    font-size: 35px !important;
    color: #ffffff !important;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2)) !important;
}

html body section.report-callout .report-text {
    flex-grow: 1 !important;
}

html body section.report-callout .report-text h3 {
    color: #ffffff !important;
    margin: 0 0 10px 0 !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

html body section.report-callout .report-text p {
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0 !important;
    font-size: 16px !important;
    line-height: 1.5 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
}

html body section.report-callout .report-action {
    flex-shrink: 0 !important;
}

html body section.report-callout .report-button {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #28266c !important;
    color: #ffffff !important;
    padding: 12px 25px !important;
    border-radius: 50px !important;
    font-weight: 600 !important;
    font-size: 15px !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15) !important;
    border: 2px solid transparent !important;
}

html body section.report-callout .report-button:hover {
    transform: translateY(-2px) !important;
    background: transparent !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2) !important;
}

html body section.report-callout .report-button i {
    font-size: 18px !important;
    transition: transform 0.3s ease !important;
}

html body section.report-callout .report-button:hover i {
    transform: rotate(90deg) !important;
}

html body section.report-callout .wave-decoration {
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    width: 100% !important;
    line-height: 0 !important;
    transform: rotate(180deg) !important;
    z-index: 1 !important;
}

html body section.report-callout .wave-decoration svg {
    position: relative !important;
    display: block !important;
    width: 100% !important;
    height: 50px !important;
}

html body section.report-callout .wave-decoration path {
    animation: wave 20s linear infinite !important;
}

@keyframes wave {
    0% {
        d: path("M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z");
    }
    50% {
        d: path("M0,128L48,144C96,160,192,192,288,192C384,192,480,160,576,144C672,128,768,128,864,144C960,160,1056,192,1152,192C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z");
    }
    100% {
        d: path("M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z");
    }
}

@keyframes pulse {
    0% {
    
        transform: scale(1) !important;
        opacity: 1 !important;
    }
    50% {
        transform: scale(1.1) !important;
        opacity: 0.8 !important;
    }
    100% {
        transform: scale(1) !important;
        opacity: 1 !important;
    }
}

html body section.report-callout .pulse {
    animation: pulse 2s infinite !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    html body section.report-callout .report-callout-wrapper {
        padding: 30px 0 !important;
    }

    html body section.report-callout .report-content {
        flex-direction: column !important;
        text-align: center !important;
        gap: 20px !important;
    }

    html body section.report-callout .report-icon {
        margin: 0 auto !important;
        width: 60px !important;
        height: 60px !important;
    }

    html body section.report-callout .report-icon i {
        font-size: 30px !important;
    }

    html body section.report-callout .report-text {
        margin-bottom: 15px !important;
    }

    html body section.report-callout .report-text h3 {
        font-size: 22px !important;
    }

    html body section.report-callout .report-text p {
        font-size: 15px !important;
        padding: 0 10px !important;
    }

    html body section.report-callout .report-card {
        padding: 25px 15px !important;
        margin: 0 15px !important;
    }

    html body section.report-callout .report-button {
        width: 100% !important;
        justify-content: center !important;
        padding: 10px 20px !important;
    }
}

@media (max-width: 480px) {
    html body section.report-callout .report-callout-wrapper {
        padding: 25px 0 !important;
    }

    html body section.report-callout .report-text h3 {
        font-size: 20px !important;
    }

    html body section.report-callout .report-text p {
        font-size: 14px !important;
    }

    html body section.report-callout .report-card {
        padding: 20px 15px !important;
    }
}

/* Enhanced Article Section Styles */
.article-carousel {
    position: relative !important;
    overflow: hidden !important;
    padding: 20px 0 !important;
}

.article-slide {
    display: none !important;
    animation: fadeEffect 0.5s ease-in-out !important;
}

.article-slide.active {
    display: block !important;
}

.article-card {
    background: #ffffff !important;
    border-radius: 15px !important;
    overflow: hidden !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    margin: 0 auto !important;
    max-width: 800px !important;
    transition: transform 0.3s ease !important;
}

.article-card:hover {
    transform: translateY(-5px) !important;
}

.article-image {
    position: relative !important;
    height: 400px !important;
    overflow: hidden !important;
}

.article-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.3s ease !important;
}

.article-card:hover .article-image img {
    transform: scale(1.05) !important;
}

.article-category {
    position: absolute !important;
    top: 20px !important;
    right: 20px !important;
    background: rgba(40, 38, 108, 0.9) !important;
    color: #fff !important;
    padding: 8px 15px !important;
    border-radius: 25px !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
}

.article-content {
    padding: 30px !important;
}

.article-content h3 {
    color: #28266c !important;
    font-size: 1.8rem !important;
    margin-bottom: 15px !important;
    font-weight: 700 !important;
}

.article-content p {
    color: #666 !important;
    font-size: 1.1rem !important;
    line-height: 1.6 !important;
    margin-bottom: 20px !important;
}

.article-meta {
    display: flex !important;
    gap: 20px !important;
    margin-bottom: 20px !important;
    color: #888 !important;
    font-size: 0.9rem !important;
}

.article-meta span {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
}

.read-more {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    color: #28266c !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: gap 0.3s ease !important;
}

.read-more:hover {
    gap: 12px !important;
}

.article-navigation {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 20px !important;
    margin-top: 30px !important;
}

.prev-article,
.next-article {
    background: #28266c !important;
    color: #fff !important;
    border: none !important;
    width: 40px !important;
    height: 40px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: transform 0.3s ease !important;
}

.prev-article:hover,
.next-article:hover {
    transform: scale(1.1) !important;
}

.article-dots {
    display: flex !important;
    gap: 8px !important;
}

.dot {
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: #ddd !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
}

.dot.active {
    background: #28266c !important;
    transform: scale(1.2) !important;
}

@keyframes fadeEffect {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .article-image {
        height: 300px !important;
    }

    .article-content h3 {
        font-size: 1.5rem !important;
    }

    .article-content p {
        font-size: 1rem !important;
    }

    .article-category {
        font-size: 0.8rem !important;
    }
}

@media (max-width: 480px) {
    .article-image {
        height: 200px !important;
    }

    .article-content {
        padding: 20px !important;
    }

    .article-content h3 {
        font-size: 1.3rem !important;
    }

    .article-meta {
        flex-direction: column !important;
        gap: 10px !important;
    }
}

/* Add or update these styles in the <style> section */
#hero {
    margin-top: -24px !important; /* This removes the gap by offsetting the default margin */
    padding-top: 0 !important;
}

.hero.section {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

#hero img {
    margin-top: 0 !important;
    padding-top: 0 !important;
    display: block !important; /* Removes any potential inline spacing */
}
</style>

    <!-- Hero Section -->
    <section id="hero" class="hero section accent-background">
    <img src="{{ asset('assets/img/hero-bg.jpg') }}" alt="" data-aos="fade-in">
      <div class="container text-center" data-aos="fade-up" data-aos-delay="100">
        <h9>Clean Seas Project</h9>
        <p>Protect Our Seas—Together, Let's Fight Marine Pollution!</p>
        <a href="#articles" class="btn-scroll" title="Scroll Down"><i class="bi bi-chevron-down"></i></a>
      </div>
</section>

<!-- Report Callout Section -->
<section class="report-callout">
    <div class="report-callout-wrapper">
  <div class="container">
            <div class="report-card" data-aos="fade-up">
                <div class="report-content">
                    <div class="report-icon">
                        <i class="fas fa-exclamation-circle pulse"></i>
                    </div>
                    <div class="report-text">
                        <h3>Spotted Marine Waste?</h3>
                        <p>Help protect our oceans by reporting marine waste. Your report can make a difference!</p>
          </div>
                    <div class="report-action">
                        @auth
                            <a href="{{ route('signal.create') }}" class="report-button">
                                <i class="fas fa-plus-circle"></i>
                                Report Now
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="report-button">
                                <i class="fas fa-sign-in-alt"></i>
                                Sign In to Report
                            </a>
                        @endauth
          </div>
          </div>
                <div class="wave-decoration">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255, 255, 255, 0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                    </svg>
          </div>
          </div>
          </div>
  </div>
</section>

<!-- Upcoming Collectes Section -->
<section class="collectes-container">
    <div class="container section-title" data-aos="fade-up">
        <h2>Upcoming Collections</h2>
        <p>Stay tuned! Volunteer now to clean our beaches and protect marine life. Every effort counts—be part of the change!</p>
    </div>

      <div class="container">
        <div class="swiper collectesSwiper">
            <div class="swiper-wrapper">
                @foreach ($upcomingCollectes as $collecte)
                    <div class="swiper-slide">
                        <div class="collecte-card">
                            <div class="collecte-image">
                                <img src="{{ $collecte->image_url ?? asset('assets/img/collectes/default.png') }}" alt="Collecte Location">
                                <div class="icon-buttons">
                                    <button class="icon-button expand-button" title="See More" data-bs-toggle="modal" data-bs-target="#collecteModal{{ $collecte->id }}">
                                        <i class="fas fa-expand-alt"></i>
                                    </button>
                                    <div class="share-container">
                                        <button class="icon-button share-button" title="Share">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <div class="share-popup">
                                            <a href="#" class="share-icon facebook" title="Share on Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                            <a href="#" class="share-icon twitter" title="Share on Twitter">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        </div>
            </div>
        </div>
          </div>
                            <div class="collecte-info">
                                <h2 class="collecte-location">{{ $collecte->signal->location ?? 'Location Not Available' }}</h2>
                                <p class="collecte-description">
                                    {{ Str::limit($collecte->description, 100) }}
                                    @if (strlen($collecte->description) > 100)
                                        <a href="#" class="see-more-link" data-bs-toggle="modal" data-bs-target="#collecteModal{{ $collecte->id }}">See more</a>
                                    @endif
                                </p>
                                <div class="collecte-stats">
                                    <div class="stat">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $collecte->current_contributors }} / {{ $collecte->nbrContributors }} Volunteers</span>
        </div>
                                    <div class="stat">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $collecte->starting_date->format('F j, Y') }}</span>
      </div>
      </div>    
                                <div class="collecte-actions">
                                    @auth
                                        <form action="{{ route('collecte.join', $collecte->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="volunteer-button">Volunteer</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="volunteer-button">Sign Up to Volunteer</a>
                                    @endauth
      </div>
            </div>
            </div>
            </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Collecte Modals -->
@foreach ($upcomingCollectes as $collecte)
    <div class="modal fade" id="collecteModal{{ $collecte->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
                <div class="modal-body">
                    <div class="collecte-modal-image">
                        <img src="{{ $collecte->image_url ?? asset('assets/img/collectes/default.png') }}" alt="Collecte Location">
                        <div class="collecte-modal-title">
                            <h5>{{ $collecte->signal->location ?? 'Location Not Available' }}</h5>
                            @if($collecte->signal && $collecte->signal->wasteTypes->count() > 0)
                                <div class="waste-types">
                                    @foreach($collecte->signal->wasteTypes as $wasteType)
                                        <span class="waste-type-badge">{{ $wasteType->name }}</span>
                                    @endforeach
                                </div>
                            @endif
      </div>
            </div>
                    <div class="collecte-modal-content">
                        <div class="collecte-modal-description">
                            <h6>About This Collecte</h6>
                            <p>{{ $collecte->description }}</p>
            </div>
                        <div class="collecte-modal-details">
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <h6>Volunteers</h6>
                                    <p>{{ $collecte->current_contributors }} / {{ $collecte->nbrContributors }} Volunteers</p>
            </div>
            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <h6>Date & Time</h6>
                                    <p>{{ $collecte->starting_date->format('F j, Y \a\t h:i A') }}</p>
        </div>
      </div>
                            @if($collecte->signal)
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <h6>Location</h6>
                                        <p>{{ $collecte->signal->location }}</p>
                                        <small class="coordinates">
                                            {{ $collecte->signal->latitude }}, {{ $collecte->signal->longitude }}
                                        </small>
            </div>
          </div>
                                <div class="detail-item">
                                    <i class="fas fa-weight"></i>
                                    <div>
                                        <h6>Estimated Volume</h6>
                                        <p>{{ $collecte->signal->volume }} m³</p>
        </div>
      </div>
                            @endif
                        </div>
                  </div>
                </div>
                <div class="modal-footer">
                    @auth
                        <form action="{{ route('collecte.join', $collecte->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="volunteer-button">Volunteer Now</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="volunteer-button">Sign Up to Volunteer</a>
                    @endauth
                </div>
              </div>
        </div>
    </div>
@endforeach

<!-- Articles Section -->
<section id="articles" class="portfolio section">
    <div class="container section-title" data-aos="fade-up">
        <h1>Latest Articles</h1>
        <p>Stay informed about marine life, ocean pollution, and conservation efforts.</p>
    </div>

    <div class="container">
        <div class="article-carousel">
            @foreach ($articles as $article)
                <div class="article-slide">
                    <div class="article-card">
                        <div class="article-image">
                            <img src="{{ $article->image_url ?? asset('assets/img/articles/default.jpg') }}" 
                                 alt="{{ $article->title }}"
                                 class="img-fluid">
                            <div class="article-category">
                                <span>{{ ucfirst($article->category) }}</span>
                            </div>
                        </div>
                        <div class="article-content">
                            <h3>{{ $article->title }}</h3>
                            <p>{{ Str::limit($article->content, 150) }}</p>
                            <div class="article-meta">
                                <span><i class="fas fa-calendar"></i> {{ $article->published_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-user"></i> {{ $article->author->name }}</span>
                            </div>
                            <a href="{{ route('articles.show', $article->id) }}" class="read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="article-navigation">
            <button class="prev-article"><i class="fas fa-chevron-left"></i></button>
            <div class="article-dots"></div>
            <button class="next-article"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</section>

<!-- Map Section -->
<section id="cartography" class="map-section">
    <div class="container section-title" data-aos="fade-up">
        <h2>Cartography</h2>
        <p>Explore marine waste collection points and upcoming cleanup events.</p>
          </div>
    <div class="container">
        <!-- Debug Info -->
        <div id="map-debug" class="alert alert-info mb-3" style="display: none;">
            <strong>Debug Info:</strong>
            <div id="debug-content"></div>
      </div>

        <!-- Map Filters -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="status-filter">Status</label>
                    <select id="status-filter" class="form-control">
                        <option value="all">All Status</option>
                        <option value="planned">Planned</option>
                        <option value="completed">Completed</option>
                        <option value="validated">Validated</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="waste-type-filter">Waste Type</label>
                    <select id="waste-type-filter" class="form-control">
                        <option value="all">All Types</option>
                        @foreach($wasteTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date Range</label>
                    <div class="d-flex">
                        <input type="date" id="date-from" class="form-control me-2">
                        <input type="date" id="date-to" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button id="reset-filters" class="btn btn-secondary w-100">Reset Filters</button>
            </div>
          </div>

        <!-- Map Container -->
        <div class="card">
            <div class="card-body p-0">
                <div id="map" style="height: 600px;"></div>
                </div>
                </div>

        <!-- Map Legend -->
        <div class="map-legend mt-3">
            <div class="d-flex align-items-center justify-content-center">
                <div class="me-4">
                    <i class="fas fa-circle text-danger"></i> High Volume
                </div>
                <div class="me-4">
                    <i class="fas fa-circle text-warning"></i> Medium Volume
                </div>
                <div>
                    <i class="fas fa-circle text-success"></i> Low Volume
                </div>
              </div>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
/* Collectes Section Styles */
html body .collectes-container {
    padding: 60px 0 !important;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    position: relative !important;
    overflow: hidden !important;
    z-index: 1 !important;
}

html body .collectes-container::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    opacity: 1 !important;
    z-index: 0 !important;
}
    @media (min-width: 768px) {
        .collectes-container .collecte-info {
            padding: 20px !important;
        }
    }
    @media (max-width: 768px) {
        .collectes-container .collecte-info {
            padding: 10px !important;
        }
    }

html body .collectes-container .collecte-card {
    background: #ffffff !important;
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    overflow: hidden !important;
    transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    margin: 15px 0 !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    position: relative !important;
    z-index: 2 !important;
}

html body .collectes-container .collecte-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
}

html body .collectes-container .collecte-image {
    position: relative !important;
    height: 200px !important;
    overflow: hidden !important;
}

html body .collectes-container .collecte-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.3s ease !important;
}

html body .collectes-container .collecte-card:hover .collecte-image img {
    transform: scale(1.05) !important;
}

/* Articles Section Styles */
html body .portfolio.section {
    padding: 60px 0 !important;
    background: #ffffff !important;
    position: relative !important;
    z-index: 1 !important;
}

html body .portfolio.section .portfolio-item {
    border-radius: 15px !important;
    overflow: hidden !important;
    position: relative !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    transition: transform 0.3s ease !important;
    margin-bottom: 30px !important;
}

html body .portfolio.section .portfolio-item:hover {
    transform: translateY(-5px) !important;
}

html body .portfolio.section .portfolio-item img {
    width: 100% !important;
    height: 250px !important;
    object-fit: cover !important;
    transition: transform 0.3s ease !important;
}

html body .portfolio.section .portfolio-item:hover img {
    transform: scale(1.05) !important;
}

html body .portfolio.section .portfolio-info {
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    background: linear-gradient(0deg, rgba(40, 38, 108, 0.95) 0%, rgba(40, 38, 108, 0.8) 100%) !important;
    padding: 25px !important;
    transition: all 0.3s ease !important;
    transform: translateY(0) !important;
    z-index: 2 !important;
}

html body .portfolio.section .portfolio-item:hover .portfolio-info {
    transform: translateY(-10px) !important;
}

html body .portfolio.section .portfolio-info h4 {
    color: #ffffff !important;
    font-size: 1.2rem !important;
    font-weight: 600 !important;
    margin-bottom: 10px !important;
}

html body .portfolio.section .portfolio-info p {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 0.9rem !important;
    line-height: 1.6 !important;
    margin-bottom: 15px !important;
}

/* Section Title Styles */
html body .section-title {
    text-align: center !important;
    position: relative !important;
    z-index: 2 !important;
}

html body .section-title h2 {
    font-weight: 700 !important;
    color: #28266c !important;
    margin-bottom: 15px !important;
    position: relative !important;
    display: inline-block !important;
}
.swiper-pagination{
    position: relative !important;
}
html body .section-title h2::after {
    content: '' !important;
    position: absolute !important;
    left: 50% !important;
    bottom: -8px !important;
    transform: translateX(-50%) !important;
    width: 50px !important;
    height: 3px !important;
    border-radius: 3px !important;
}

html body .section-title p {
    color: #6c757d !important;
    font-size: 1.1rem !important;
    max-width: 700px !important;
    margin: 0 auto !important;
}

/* Responsive Adjustments */
@media (max-width: 991px) {
    .collecte-image {
        height: 180px;
    }

    .portfolio-item img {
        height: 220px;
    }

    .section-title h2 {
        font-size: 1.75rem;
    }

    .section-title p {
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    .collectes-container,
    .portfolio.section {
        padding: 40px 0;
    }

    .collecte-info {
        padding: 20px;
    }

    .collecte-location {
        font-size: 1.1rem;
    }

    .collecte-description {
        font-size: 0.9rem;
    }

    .stat {
        font-size: 0.85rem;
    }

    .portfolio-info {
        padding: 20px;
    }

    .portfolio-info h4 {
        font-size: 1.1rem;
    }

    .portfolio-info p {
        font-size: 0.85rem;
    }

   

    .section-title h2 {
        font-size: 1.5rem;
    }

    .section-title p {
        font-size: 0.95rem;
    }
}

@media (max-width: 480px) {
    .collecte-image {
        height: 160px;
    }

    .icon-buttons {
        top: 10px;
        right: 10px;
    }

    .icon-button {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }

    .collecte-info {
        padding: 15px;
    }

    .collecte-stats {
        gap: 15px;
    }

    .volunteer-button {
        width: 100%;
        text-align: center;
        padding: 8px 20px;
    }

    .portfolio-item img {
        height: 200px;
    }

    .portfolio-info {
        padding: 15px;
    }

    .section-title h2 {
        font-size: 1.35rem;
    }
}

/* Map Section Styles */
.map-section {
    padding: 60px 0;
    background: #fff;
}

#map {
    width: 100%;
    height: 600px;
    border-radius: 8px;
}

.map-legend {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-top: 20px;
}

.leaflet-popup-content {
    margin: 13px;
    min-width: 200px;
}

.map-popup {
    padding: 15px;
    min-width: 250px;
}

.popup-title {
    margin-bottom: 15px;
    color: #333;
    font-weight: 600;
    border-bottom: 2px solid #eee;
    padding-bottom: 8px;
}

.popup-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.popup-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.popup-item i {
    width: 20px;
    text-align: center;
}

.custom-marker {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
    width: 30px !important;
    height: 30px !important;
}

.custom-marker:hover {
    transform: scale(1.2);
}

.custom-marker i {
    font-size: 30px;
    filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
}

.high-volume {
    color: #dc3545 !important;
}

.medium-volume {
    color: #ffc107 !important;
}

.low-volume {
    color: #28a745 !important;
}

.high-volume i {
    font-size: 35px;
}

.medium-volume i {
    font-size: 32px;
}

.low-volume i {
    font-size: 30px;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 3px 14px rgba(0,0,0,0.2);
}

.leaflet-popup-content {
    margin: 0;
    line-height: 1.4;
}

/* Filter styles */
.form-group {
    margin-bottom: 1rem;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 0.5rem;
}

.btn-secondary {
    background-color: #28266c;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    color: white;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background-color: #3498db;
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug logging
    console.log('Full collectes data:', @json($mapCollectes));

    // Initialize map centered on Morocco
    var map = L.map('map').setView([31.7917, -7.0926], 6);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Custom icons for different volumes
    var icons = {
        high: L.divIcon({
            className: 'custom-marker high-volume',
            html: '<i class="fas fa-map-marker-alt" style="color: #dc3545;"></i>',
            iconSize: [35, 35],
            iconAnchor: [17, 35]
        }),
        medium: L.divIcon({
            className: 'custom-marker medium-volume',
            html: '<i class="fas fa-map-marker-alt" style="color: #ffc107;"></i>',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        }),
        low: L.divIcon({
            className: 'custom-marker low-volume',
            html: '<i class="fas fa-map-marker-alt" style="color: #28a745;"></i>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        })
    };

    // Get collectes data
    var collectes = @json($mapCollectes);
    console.log('Number of collectes:', collectes ? collectes.length : 0);
    
    // Function to get icon based on volume
    function getVolumeIcon(volume) {
        if (!volume) return icons.low;
        if (volume >= 100) return icons.high;
        if (volume >= 50) return icons.medium;
        return icons.low;
    }

    // Function to get volume category text
    function getVolumeCategory(volume) {
        if (!volume) return 'Low Volume';
        if (volume >= 100) return 'High Volume';
        if (volume >= 50) return 'Medium Volume';
        return 'Low Volume';
    }

    // Function to get volume color class
    function getVolumeColorClass(volume) {
        if (!volume) return 'text-success';
        if (volume >= 100) return 'text-danger';
        if (volume >= 50) return 'text-warning';
        return 'text-success';
    }

    // Function to safely get waste types text
    function getWasteTypesText(signal) {
        try {
            if (!signal || !signal.waste_types) {
                return signal && signal.wasteTypes && signal.wasteTypes.length > 0
                    ? signal.wasteTypes.map(wt => wt.name).join(', ')
                    : 'N/A';
            }
            return signal.waste_types.join(', ') || 'N/A';
        } catch (error) {
            console.error('Error getting waste types:', error);
            return 'N/A';
        }
    }

    var markers = [];
    var heatLayer = null;

    function addMarkersToMap(collectes) {
        try {
            console.log('Adding markers for collectes:', collectes);
            
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Add new markers
            collectes.forEach(function(collecte) {
                try {
                    if (collecte && collecte.signal) {
                        // Debug log for coordinates
                        console.log('Processing coordinates for collecte:', {
                            id: collecte.id,
                            location: collecte.signal.location,
                            rawLat: collecte.signal.latitude,
                            rawLng: collecte.signal.longitude
                        });
                        
                        // Parse coordinates and swap if needed
                        let lat = parseFloat(collecte.signal.latitude);
                        let lng = parseFloat(collecte.signal.longitude);
                        
                        // Validate coordinates
                        if (isNaN(lat) || isNaN(lng)) {
                            console.error('Invalid coordinates for collecte:', collecte.id);
                            return;
                        }

                        // Ensure coordinates are within valid ranges
                        if (lat > 90 || lat < -90 || lng > 180 || lng < -180) {
                            console.error('Coordinates out of range for collecte:', collecte.id);
                            return;
                        }

                        // Create marker
                        var marker = L.marker(
                            [lat, lng],
                            { icon: getVolumeIcon(collecte.signal.volume) }
                        ).bindPopup(`
                            <div class="map-popup">
                                <h5 class="popup-title">${collecte.signal.location || 'Unknown Location'}</h5>
                                <div class="popup-content">
                                    <div class="popup-item">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <span>${lat.toFixed(4)}, ${lng.toFixed(4)}</span>
          </div>
                                    <div class="popup-item">
                                        <i class="fas fa-weight-hanging ${getVolumeColorClass(collecte.signal.volume)}"></i>
                                        <span>${collecte.status === 'completed' ? collecte.actual_volume : (collecte.signal.volume || 0)} m³ (${getVolumeCategory(collecte.status === 'completed' ? collecte.actual_volume : (collecte.signal.volume || 0))})</span>
          </div>
                                    <div class="popup-item">
                                        <i class="fas fa-info-circle text-info"></i>
                                        <span>${collecte.status ? collecte.status.charAt(0).toUpperCase() + collecte.status.slice(1) : 'Unknown'}</span>
        </div>
                                    <div class="popup-item">
                                        <i class="fas fa-calendar-alt text-warning"></i>
                                        <span>${collecte.starting_date ? new Date(collecte.starting_date).toLocaleDateString() : 'N/A'}</span>
        </div>
                                    <div class="popup-item">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span>${collecte.signal && collecte.signal.wasteTypes 
                                            ? collecte.signal.wasteTypes.map(wt => wt.name).join(', ') 
                                            : 'N/A'}</span>
        </div>
                                    <div class="popup-item">
                                        <i class="fas fa-users text-success"></i>
                                        <span>${collecte.current_contributors || 0} / ${collecte.nbrContributors || 0} volunteers</span>
                                    </div>
                                    ${collecte.status === 'planned' && collecte.current_contributors < collecte.nbrContributors ? `
                                    <div class="popup-item mt-3">
                                        <form action="{{ route('collecte.join', '') }}${collecte.id}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-user-plus"></i> Volunteer
                                            </button>
                                        </form>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `);
                        marker.addTo(map);
                        markers.push(marker);
                    }
                } catch (error) {
                    console.error('Error processing collecte:', error, collecte);
                }
            });

            // Update heatmap with the same coordinate processing
            if (heatLayer) {
                map.removeLayer(heatLayer);
            }

            if (collectes.length > 0) {
                var heatData = collectes
                    .filter(col => col && col.signal && !isNaN(parseFloat(col.signal.latitude)) && !isNaN(parseFloat(col.signal.longitude)))
                    .map(col => {
                        let lat = parseFloat(col.signal.latitude);
                        let lng = parseFloat(col.signal.longitude);
                        return [lat, lng, parseFloat(col.signal.volume) / 100 || 0.5];
                    });

                if (heatData.length > 0) {
                    heatLayer = L.heatLayer(heatData, {
                        radius: 25,
                        blur: 15,
                        maxZoom: 10,
                        gradient: {
                            0.4: 'blue',
                            0.6: 'cyan',
                            0.7: 'lime',
                            0.8: 'yellow',
                            1.0: 'red'
                        }
                    }).addTo(map);
                }
            }

            // Fit bounds if we have markers
            if (markers.length > 0) {
                var group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        } catch (error) {
            console.error('Error in addMarkersToMap:', error);
        }
    }

    function updateMap() {
        try {
            var status = document.getElementById('status-filter').value;
            var wasteType = document.getElementById('waste-type-filter').value;
            var dateFrom = document.getElementById('date-from').value;
            var dateTo = document.getElementById('date-to').value;

            console.log('Filtering with:', { status, wasteType, dateFrom, dateTo });

            // Filter collectes
            var filteredCollectes = collectes.filter(function(collecte) {
                if (!collecte) return false;

                // Status filtering
                var matchStatus = status === 'all' || collecte.status.toLowerCase() === status.toLowerCase();

                // Waste type filtering
                var matchWasteType = wasteType === 'all';
                if (!matchWasteType && collecte.signal && collecte.signal.wasteTypes) {
                    matchWasteType = collecte.signal.wasteTypes.some(wt => wt.id === parseInt(wasteType));
                }

                // Date filtering
                var matchDate = true;
                if (dateFrom && dateTo) {
                    var collecteDate = new Date(collecte.starting_date);
                    var fromDate = new Date(dateFrom);
                    var toDate = new Date(dateTo);
                    toDate.setHours(23, 59, 59); // Include the entire end day
                    matchDate = collecteDate >= fromDate && collecteDate <= toDate;
                }

                return matchStatus && matchWasteType && matchDate;
            });

            console.log('Filtered collectes:', filteredCollectes);
            addMarkersToMap(filteredCollectes);
        } catch (error) {
            console.error('Error in updateMap:', error);
        }
    }

    // Initial map population
    if (collectes && collectes.length > 0) {
        console.log('Initializing map with collectes:', collectes);
        
        // Set default filter values
        document.getElementById('status-filter').value = 'all';
        document.getElementById('waste-type-filter').value = 'all';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        
        updateMap();
    } else {
        console.log('No collectes available');
    }

    // Add filter event listeners
    document.getElementById('status-filter').addEventListener('change', updateMap);
    document.getElementById('waste-type-filter').addEventListener('change', updateMap);
    document.getElementById('date-from').addEventListener('change', updateMap);
    document.getElementById('date-to').addEventListener('change', updateMap);
    document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('status-filter').value = 'all';
        document.getElementById('waste-type-filter').value = 'all';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        updateMap();
    });

    const slides = document.querySelectorAll('.article-slide');
    const dotsContainer = document.querySelector('.article-dots');
    const prevButton = document.querySelector('.prev-article');
    const nextButton = document.querySelector('.next-article');
    let currentSlide = 0;

    // Create dots
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });

    // Show first slide
    slides[0].classList.add('active');

    function updateSlides() {
        slides.forEach(slide => slide.classList.remove('active'));
        document.querySelectorAll('.dot').forEach(dot => dot.classList.remove('active'));
        
        slides[currentSlide].classList.add('active');
        document.querySelectorAll('.dot')[currentSlide].classList.add('active');
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlides();
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlides();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlides();
    }

    // Event listeners
    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);

    // Auto advance slides every 5 seconds
    setInterval(nextSlide, 5000);
});
</script>
@endpush

@endsection