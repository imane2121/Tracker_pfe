@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="collecte-header mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Signal Groups</h1>
                    <p class="mb-0 mt-2">Signals within 1km of each other are grouped together. Select a group to create a collection.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Map Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div id="map" style="height: 600px;" class="rounded-4"></div>
                </div>
            </div>
        </div>

        <!-- Signal Groups List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Available Groups</h5>
                    <div id="groups-list" class="groups-list">
                        <!-- Groups will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .group-item {
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .group-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .group-item.active {
        border-color: #0d6efd;
        background-color: #f8f9ff;
    }
    .group-marker {
        background: none !important;
        border: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('map').setView([31.7917, -7.0926], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const signals = @json($signals);
    const markers = {};
    const groups = [];
    let polygons = [];

    // Define marker colors for different statuses
    const statusColors = {
        'validated': '#28a745', // green for validated
        'pending': '#ffc107',   // yellow for pending
        'rejected': '#dc3545',  // red for rejected
        'default': '#6c757d'    // grey for any other status
    };

    // Function to calculate distance between two points in kilometers
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                 Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                 Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Group signals that are within 1km of each other
    function groupSignals(signals) {
        const groups = [];
        const processed = new Set();

        signals.forEach(signal => {
            if (processed.has(signal.id)) return;

            const group = {
                signals: [signal],
                center: { lat: signal.latitude, lng: signal.longitude },
                bounds: []
            };
            processed.add(signal.id);

            signals.forEach(otherSignal => {
                if (signal.id === otherSignal.id || processed.has(otherSignal.id)) return;

                const distance = calculateDistance(
                    signal.latitude, signal.longitude,
                    otherSignal.latitude, otherSignal.longitude
                );

                if (distance <= 1) { // 1km radius
                    group.signals.push(otherSignal);
                    processed.add(otherSignal.id);
                }
            });

            if (group.signals.length >= 5) { // Only add groups with 5 or more signals
                // Calculate center point
                const latSum = group.signals.reduce((sum, s) => sum + parseFloat(s.latitude), 0);
                const lngSum = group.signals.reduce((sum, s) => sum + parseFloat(s.longitude), 0);
                group.center = {
                    lat: latSum / group.signals.length,
                    lng: lngSum / group.signals.length
                };
                group.bounds = group.signals.map(s => [s.latitude, s.longitude]);
                groups.push(group);
            }
        });

        return groups;
    }

    // Create markers and groups
    const signalGroups = groupSignals(signals);
    
    // Function to create custom marker icon
    function createMarkerIcon(status) {
        const color = statusColors[status] || statusColors.default;
        return L.divIcon({
            html: `<div style="
                background-color: ${color};
                width: 12px;
                height: 12px;
                border-radius: 50%;
                border: 2px solid white;
                box-shadow: 0 0 4px rgba(0,0,0,0.3);
            "></div>`,
            className: 'custom-marker',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });
    }

    // Create markers for all signals with different colors
    signals.forEach(signal => {
        const marker = L.marker([signal.latitude, signal.longitude], {
            icon: createMarkerIcon(signal.status)
        })
        .bindPopup(`
            <strong>${signal.location}</strong><br>
            Volume: ${signal.volume}m³<br>
            Status: <span class="badge" style="background-color: ${statusColors[signal.status] || statusColors.default}">
                ${signal.status}
            </span><br>
            Created: ${new Date(signal.created_at).toLocaleDateString()}
        `)
        .addTo(map);
        markers[signal.id] = marker;
    });

    // Add legend to the map
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'info legend');
        div.style.backgroundColor = 'white';
        div.style.padding = '10px';
        div.style.borderRadius = '5px';
        div.style.boxShadow = '0 0 5px rgba(0,0,0,0.2)';
        
        div.innerHTML = '<strong>Signal Status</strong><br>';
        Object.entries(statusColors).forEach(([status, color]) => {
            if (status !== 'default') {
                div.innerHTML += `
                    <div style="margin-top: 5px;">
                        <span style="
                            display: inline-block;
                            width: 12px;
                            height: 12px;
                            background-color: ${color};
                            border-radius: 50%;
                            margin-right: 5px;
                        "></span>
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </div>
                `;
            }
        });
        return div;
    };
    legend.addTo(map);

    // Function to get location name from coordinates
    async function getLocationName(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10`);
            const data = await response.json();
            
            // Extract the most relevant location name
            // Priority: city/town/village > county > state
            const address = data.address;
            return address.city || address.town || address.village || 
                   address.county || address.state || 'Unknown Location';
        } catch (error) {
            console.error('Error fetching location name:', error);
            return 'Unknown Location';
        }
    }

    // Modified group visualization code
    async function visualizeGroups() {
        for (const group of signalGroups) {
            // Get location name for group center
            const groupName = await getLocationName(group.center.lat, group.center.lng);
            
            // Count validated and pending signals in the group
            const validatedSignals = group.signals.filter(s => s.status === 'validated');
            const pendingSignals = group.signals.filter(s => s.status === 'pending');
            
            // Check if group meets creation criteria
            const canCreateCollection = validatedSignals.length >= 1 || pendingSignals.length >= 5;

            // Add circle to show the group boundary
            const circle = L.circle([group.center.lat, group.center.lng], {
                color: '#0d6efd',
                fillColor: '#0d6efd',
                fillOpacity: 0.1,
                weight: 2,
                radius: 1000 // 1km in meters
            }).addTo(map);

            // Add click handler to the circle
            const signalIds = group.signals.map(s => s.id).join(',');
            const popupContent = canCreateCollection 
                ? `
                    <div class="text-center">
                        <strong>${group.signals.length} signals in ${groupName}</strong><br>
                        <small class="text-muted">
                            ${validatedSignals.length} validated, ${pendingSignals.length} pending
                        </small><br>
                        <a href="{{ route('collecte.create') }}?signals=${signalIds}&lat=${group.center.lat}&lng=${group.center.lng}" 
                           class="btn btn-primary btn-sm mt-2">Create Collection</a>
                    </div>
                `
                : `
                    <div class="text-center">
                        <strong>${group.signals.length} signals in ${groupName}</strong><br>
                        <small class="text-muted">
                            ${validatedSignals.length} validated, ${pendingSignals.length} pending
                        </small><br>
                        <small class="text-danger">
                            Requires 1 validated or 5 pending signals
                        </small>
                    </div>
                `;
            
            circle.bindPopup(popupContent);

            // Add group to the sidebar only if it meets creation criteria
            if (canCreateCollection) {
                const groupElement = document.createElement('div');
                groupElement.className = 'group-item';
                groupElement.innerHTML = `
                    <h6 class="mb-2">${groupName}</h6>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-geo-alt"></i> ${validatedSignals.length} validated, ${pendingSignals.length} pending
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">${group.signals.length} signals</span>
                        <a href="{{ route('collecte.create') }}?signals=${signalIds}&lat=${group.center.lat}&lng=${group.center.lng}" 
                           class="btn btn-primary btn-sm">Create Collection</a>
                    </div>
                `;
                document.getElementById('groups-list').appendChild(groupElement);

                // Handle group selection
                groupElement.addEventListener('click', () => {
                    map.fitBounds(group.bounds, { padding: [50, 50] });
                    document.querySelectorAll('.group-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    groupElement.classList.add('active');
                });
            }
        }

        // If no valid groups found, show message
        if (!document.getElementById('groups-list').children.length) {
            document.getElementById('groups-list').innerHTML = `
                <div class="text-center py-4">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-emoji-neutral text-muted"></i>
                    </div>
                    <p class="text-muted mb-0">No eligible groups found<br>(Requires 1 validated or 5 pending signals)</p>
                </div>
            `;
        }
    }

    // Call the async function to visualize groups
    visualizeGroups();

    // Add click event handler to the map
    map.on('click', function(e) {
        const clickedLat = e.latlng.lat;
        const clickedLng = e.latlng.lng;
        
        // Find signals within 1km of click
        const nearbySignals = signals.filter(signal => {
            const distance = calculateDistance(
                clickedLat, clickedLng,
                signal.latitude, signal.longitude
            );
            return distance <= 1;
        });

        if (nearbySignals.length > 0) {
            // Count validated and pending signals
            const validatedSignals = nearbySignals.filter(s => s.status === 'validated');
            const pendingSignals = nearbySignals.filter(s => s.status === 'pending');
            const canCreateCollection = validatedSignals.length >= 1 || pendingSignals.length >= 5;

            // Create popup content based on validation conditions
            const signalIds = nearbySignals.map(s => s.id).join(',');
            const popupContent = canCreateCollection
                ? `
                    <div class="text-center">
                        <strong>${nearbySignals.length} signals found</strong><br>
                        <small class="text-muted">
                            ${validatedSignals.length} validated, ${pendingSignals.length} pending
                        </small><br>
                        <a href="{{ route('collecte.create') }}?signals=${signalIds}&lat=${clickedLat}&lng=${clickedLng}" 
                           class="btn btn-primary btn-sm mt-2">Create Collection</a>
                    </div>
                `
                : `
                    <div class="text-center">
                        <strong>${nearbySignals.length} signals found</strong><br>
                        <small class="text-muted">
                            ${validatedSignals.length} validated, ${pendingSignals.length} pending
                        </small><br>
                        <small class="text-danger">
                            Requires 1 validated or 5 pending signals
                        </small>
                    </div>
                `;

            const popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);
        }
    });
});
</script>
@endpush
@endsection 