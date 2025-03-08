@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section id="hero" class="hero section accent-background">
    <img src="{{ asset('assets/img/hero-bg.jpg') }}" alt="" data-aos="fade-in">
    <div class="container text-center" data-aos="fade-up" data-aos-delay="100">
        <h2>Clean Seas Project</h2>
        <p>Protect Our Seas—Together, Let's Fight Marine Pollution!</p>
        <a href="#articles" class="btn-scroll" title="Scroll Down"><i class="bi bi-chevron-down"></i></a>
    </div>
</section>

<!-- Upcoming Collectes Section -->
<section class="collectes-container">
    <div class="container section-title" data-aos="fade-up">
        <h2>Upcoming Collectes</h2>
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
                                        <span>{{ $collecte->contributors->count() }} / {{ $collecte->nbrContributors }} Volunteers</span>
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
                                        <a href="{{ route('login') }}" class="volunteer-button">Login to Volunteer</a>
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
                                    <p>{{ $collecte->contributors->count() }} / {{ $collecte->nbrContributors }} Volunteers</p>
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
                                        <p>{{ $collecte->signal->volume }} kg</p>
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
                        <a href="{{ route('login') }}" class="volunteer-button">Login to Volunteer</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Articles Section -->
<section id="articles" class="portfolio section">
    <div class="container section-title" data-aos="fade-up">
        <h2>Latest Articles</h2>
        <p>Stay informed about marine life, ocean pollution, and conservation efforts.</p>
    </div>

    <div class="container">
        <div class="row gy-4">
            @foreach ($articles as $article)
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="{{ $article->image_url ?? asset('assets/img/articles/default.jpg') }}" class="img-fluid" alt="{{ $article->title }}">
                        <div class="portfolio-info">
                            <h4>{{ $article->title }}</h4>
                            <p>{{ Str::limit($article->content, 100) }}</p>
                            <a href="{{ $article->image_url ?? asset('assets/img/articles/default.jpg') }}" 
                               title="{{ $article->title }}" 
                               data-gallery="portfolio-gallery" 
                               class="glightbox preview-link">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('articles.show', $article->id) }}" 
                               class="details-link" 
                               title="More Details">
                                <i class="fas fa-link"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
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
    padding: 10px;
}

.map-popup h5 {
    margin-bottom: 10px;
    color: #333;
    font-weight: 600;
}

.map-popup p {
    margin: 5px 0;
    font-size: 14px;
}

/* Custom marker styles */
.custom-marker {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custom-marker i {
    font-size: 30px;
    filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.3));
}

.high-volume i {
    color: #dc3545; /* red */
}

.medium-volume i {
    color: #ffc107; /* yellow */
}

.low-volume i {
    color: #28a745; /* green */
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
    background-color: #6c757d;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    color: white;
    transition: background-color 0.2s ease;
}

.btn-secondary:hover {
    background-color: #5a6268;
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
            html: '<i class="fas fa-map-marker-alt"></i>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        }),
        medium: L.divIcon({
            className: 'custom-marker medium-volume',
            html: '<i class="fas fa-map-marker-alt"></i>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        }),
        low: L.divIcon({
            className: 'custom-marker low-volume',
            html: '<i class="fas fa-map-marker-alt"></i>',
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

    // Function to safely get waste types text
    function getWasteTypesText(signal) {
        try {
            if (!signal || !signal.wasteTypes) return 'N/A';
            return signal.wasteTypes.map(wt => wt.name || 'Unknown').join(', ') || 'N/A';
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
                                <h5>${collecte.signal.location || 'Unknown Location'}</h5>
                                <p><strong>Coordinates:</strong> ${lat}, ${lng}</p>
                                <p><strong>Volume:</strong> ${collecte.signal.volume || 0} kg (${getVolumeCategory(collecte.signal.volume)})</p>
                                <p><strong>Status:</strong> ${collecte.status || 'Unknown'}</p>
                                <p><strong>Date:</strong> ${collecte.starting_date ? new Date(collecte.starting_date).toLocaleDateString() : 'N/A'}</p>
                                <p><strong>Waste Types:</strong> ${getWasteTypesText(collecte.signal)}</p>
                                <p><strong>Volunteers:</strong> ${collecte.current_contributors || 0} / ${collecte.nbrContributors || 0}</p>
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
});
</script>
@endpush

@endsection