@extends('layouts.app')

@section('content')
<div class="container my-5">
    <form action="{{ route('signal.store') }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
        @csrf
        
        <!-- Waste Types Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Type de Déchet</h4>
            </div>
            <div class="card-body">
                <div class="wsf-buttons-container">
                    @foreach($wasteTypes as $wasteType)
                        <div class="wsf-type-group">
                            <input type="hidden" name="general_waste_type[]" value="" class="wsf-type-input">
                            <button type="button" class="wsf-btn-option wsf-general-type" data-waste-type="{{ $wasteType->id }}">
                                {{ $wasteType->name }}
                            </button>
                            @if($wasteType->specificWasteTypes->isNotEmpty())
                                <div class="wsf-subtypes" id="subTypes_{{ $wasteType->id }}">
                                    @foreach($wasteType->specificWasteTypes as $specificType)
                                        <div class="wsf-subtype-item">
                                            <input type="hidden" class="wsf-specific-input" 
                                                name="waste_types[]" 
                                                value="{{ $specificType->id }}" 
                                                data-parent-id="{{ $wasteType->id }}">
                                            <button type="button" class="wsf-btn-option wsf-specific-type" 
                                                data-specific-id="{{ $specificType->id }}"
                                                data-parent-id="{{ $wasteType->id }}">
                                                {{ $specificType->name }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <!-- Custom Waste Type -->
                    <div class="wsf-type-group">
                        <button type="button" class="wsf-btn-option wsf-general-type" id="autreBtn">Autre</button>
                        <div id="autreInputContainer" class="wsf-hidden">
                            <input type="text" name="customType" id="autreInput" 
                                class="form-control mt-2" placeholder="Entrez un type de déchet">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Localisation</h4>
            </div>
            <div class="card-body">
                <div class="main-box">
                    <div id="map" class="mb-3" style="height: 300px;"></div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="location" id="location">
                    
                    <div class="input-section">
                        <button type="button" id="autoLocationBtn" class="btn btn-primary mb-2">
                            Utiliser ma localisation automatique
                        </button>
                        <p class="text-center mb-2">ou</p>
                        <input type="text" id="manualLocationInput" class="form-control mb-2" 
                            placeholder="Entrez votre adresse manuellement" />
                        <button type="button" id="submitLocationBtn" class="btn btn-success">Valider</button>
                        <p id="locationStatus" class="text-center mt-2"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Upload Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Images et Vidéos</h4>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary control-btn prev-btn">
                            <i class="fas fa-angle-left"></i>
                        </button>
                    </div>
                    <div class="col-8">
                        <div id="images-wrapper" class="d-flex justify-content-center">
                            <div class="image-container">
                                <p>Cliquez ici pour ajouter des médias</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary control-btn next-btn">
                            <i class="fas fa-angle-right"></i>
                        </button>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <input type="file" name="media[]" id="fileInput" accept="image/*,video/*" multiple style="display: none;">
                    <button type="button" class="btn btn-info ajout-img">Ajouter image/vidéo</button>
                </div>
            </div>
        </div>

        <!-- Volume and Description Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Détails Supplémentaires</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="volume" class="form-label">Volume Estimé (en litres)</label>
                    <input type="number" class="form-control" id="volume" name="volume" 
                        required placeholder="Entrez le volume estimé">
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" 
                        rows="4" placeholder="Décrivez le déchet et son emplacement..."></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('signal.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Soumettre le Signalement</button>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide all subtypes initially
        document.querySelectorAll('.wsf-subtypes').forEach(subtype => {
            subtype.classList.remove('show');
        });

        // Initialize map
        let map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        let marker;
        
        // Auto location button
        document.getElementById('autoLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    updateLocation(lat, lng);
                });
            }
        });
        
        // Manual location input
        document.getElementById('submitLocationBtn').addEventListener('click', function() {
            const address = document.getElementById('manualLocationInput').value;
            if (address) {
                geocodeAddress(address);
            }
        });
        
        function updateLocation(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            
            map.setView([lat, lng], 13);
            
            // Reverse geocode to get address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name;
                    document.getElementById('location').value = address;
                    document.getElementById('manualLocationInput').value = address;
                });
        }
        
        function geocodeAddress(address) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        updateLocation(data[0].lat, data[0].lon);
                    }
                });
        }
        
        // Waste type selection logic
        const wasteTypeButtons = document.querySelectorAll('.wsf-btn-option.wsf-general-type');
        const allWasteTypeGroups = document.querySelectorAll('.wsf-type-group');
        
        wasteTypeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const isAutreBtn = btn.id === 'autreBtn';
                const wasteTypeId = btn.dataset.wasteType;
                const currentGroup = btn.closest('.wsf-type-group');
                const subTypesContainer = isAutreBtn ? null : document.getElementById('subTypes_' + wasteTypeId);
                const generalTypeInput = btn.previousElementSibling;
                const isActive = btn.classList.contains('active');
                
                // Handle Autre button differently
                if (isAutreBtn) {
                    const autreContainer = document.getElementById('autreInputContainer');
                    const isHidden = autreContainer.classList.contains('wsf-hidden');
                    
                    // Reset all other buttons and show all groups
                    resetAllTypes();
                    showAllGroups();
                    
                    // Toggle autre button and input
                    btn.classList.toggle('active', isHidden);
                    autreContainer.classList.toggle('wsf-hidden');
                    return;
                }

                if (isActive) {
                    // If already active, deactivate and show all groups
                    btn.classList.remove('active');
                    if (subTypesContainer) {
                        subTypesContainer.classList.remove('show');
                    }
                    generalTypeInput.value = '';
                    showAllGroups();
                } else {
                    // Reset all types first
                    resetAllTypes();
                    
                    // Hide all other groups except current
                    allWasteTypeGroups.forEach(group => {
                        if (group !== currentGroup) {
                            group.style.display = 'none';
                        }
                    });
                    
                    // Activate current button and show its subtypes
                    btn.classList.add('active');
                    generalTypeInput.value = wasteTypeId;
                    if (subTypesContainer) {
                        subTypesContainer.classList.add('show');
                    }
                }
            });
        });

        // Specific type selection logic
        const specificTypeButtons = document.querySelectorAll('.wsf-specific-type');
        specificTypeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const parentId = btn.dataset.parentId;
                const specificId = btn.dataset.specificId;
                const parentButton = document.querySelector(`button[data-waste-type="${parentId}"]`);
                const parentInput = parentButton.previousElementSibling;
                const specificInput = btn.previousElementSibling;
                
                // Toggle selected state
                btn.classList.toggle('selected');
                
                if (btn.classList.contains('selected')) {
                    specificInput.value = specificId;
                    parentButton.classList.add('has-selected');
                    parentInput.value = parentId;
                } else {
                    specificInput.value = '';
                    // Check if any other specific types are selected
                    const anySelected = Array.from(
                        document.querySelectorAll(`.wsf-specific-type[data-parent-id="${parentId}"]`)
                    ).some(btn => btn.classList.contains('selected'));
                    if (!anySelected) {
                        parentButton.classList.remove('has-selected');
                    }
                }
            });
        });

        function resetAllTypes() {
            // Reset all general types
            wasteTypeButtons.forEach(btn => {
                btn.classList.remove('active', 'has-selected');
                const input = btn.previousElementSibling;
                if (input) input.value = '';
            });
            
            // Reset all specific types
            specificTypeButtons.forEach(btn => {
                btn.classList.remove('selected');
                const input = btn.previousElementSibling;
                if (input) input.value = '';
            });
            
            // Hide all subtypes
            document.querySelectorAll('.wsf-subtypes').forEach(subtype => {
                subtype.classList.remove('show');
            });
            
            // Hide autre input
            document.getElementById('autreInputContainer').classList.add('wsf-hidden');
            document.getElementById('autreBtn').classList.remove('active');
        }

        function showAllGroups() {
            allWasteTypeGroups.forEach(group => {
                group.style.display = '';
            });
        }

        // Form validation before submit
        document.querySelector('.waste-signal-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Validate location
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            
            if (!latitude || !longitude) {
                alert('Veuillez sélectionner une localisation.');
                return;
            }
            
            // Get all selected general types
            const selectedGeneralTypes = Array.from(document.querySelectorAll('.wsf-general-type.active'))
                .map(btn => btn.dataset.wasteType)
                .filter(id => id);
            
            // Get all checked specific types
            const selectedSpecificTypes = Array.from(document.querySelectorAll('.wsf-specific-type:checked'))
                .map(cb => cb.value);
            
            // Get custom type if entered
            const autreInput = document.getElementById('autreInput');
            const customType = autreInput.value.trim();
            
            // Validate waste types
            if (selectedGeneralTypes.length === 0 && selectedSpecificTypes.length === 0 && !customType) {
                alert('Veuillez sélectionner au moins un type de déchet ou entrer un type personnalisé.');
                return;
            }
            
            // Clear existing general waste types from FormData
            formData.delete('general_waste_type[]');
            
            // Add selected general types
            selectedGeneralTypes.forEach(typeId => {
                formData.append('general_waste_type[]', typeId);
            });
            
            // If all validations pass, submit the form
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin' // Include cookies (for CSRF token)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response;
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '{{ route("signal.thank-you") }}';
                        }
                    });
                } else {
                    window.location.href = '{{ route("signal.thank-you") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la soumission du formulaire. Veuillez réessayer.');
            });
        });

        // Image upload handling
        const fileInput = document.getElementById('fileInput');
        const imagesWrapper = document.getElementById('images-wrapper');
        
        document.querySelector('.ajout-img').addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function(event) {
            for (const file of event.target.files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.className = 'image-container';
                    
                    const media = file.type.startsWith('image/') 
                        ? document.createElement('img')
                        : document.createElement('video');
                        
                    media.src = e.target.result;
                    if (media.tagName === 'VIDEO') {
                        media.controls = true;
                    }
                    
                    const deleteBtn = document.createElement('span');
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.innerHTML = '×';
                    deleteBtn.onclick = function() {
                        container.remove();
                    };
                    
                    container.appendChild(media);
                    container.appendChild(deleteBtn);
                    imagesWrapper.appendChild(container);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
@endsection