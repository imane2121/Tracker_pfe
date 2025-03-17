// Waste Signal Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on the waste signal form page
    const form = document.querySelector('.waste-signal-form');
    if (!form) return;

    // Initialize form elements
    const mediaContainer = document.getElementById('mediaContainer');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const useCameraBtn = document.getElementById('useCameraBtn');
    const captureModal = new bootstrap.Modal(document.getElementById('captureModal'));
    const capturePreview = document.getElementById('capturePreview');
    const captureCanvas = document.getElementById('captureCanvas');
    const captureBtn = document.getElementById('captureBtn');
    const photoModeBtn = document.getElementById('photoModeBtn');
    const videoModeBtn = document.getElementById('videoModeBtn');
    const prevPreviewBtn = document.getElementById('prevPreview');
    const nextPreviewBtn = document.getElementById('nextPreview');
    const locationInput = document.getElementById('location');
    const validateLocationBtn = document.getElementById('validateLocationBtn');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const wasteTypeButtons = document.querySelectorAll('.wsf-btn-option.wsf-general-type');
    const allWasteTypeGroups = document.querySelectorAll('.wsf-type-group');
    
    let currentCaptureMode = 'photo';
    let mediaStream = null;
    let isRecording = false;
    let mediaRecorder = null;
    let recordedChunks = [];
    const maxFileSize = 10 * 1024 * 1024; // 10MB
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Dark Mode Toggle
    const darkModeToggle = document.createElement('button');
    darkModeToggle.className = 'btn btn-dark position-fixed bottom-0 end-0 m-3';
    darkModeToggle.innerHTML = '<i class="bi bi-moon"></i> Dark Mode';
    darkModeToggle.onclick = () => {
        document.body.classList.toggle('dark-mode');
        darkModeToggle.innerHTML = document.body.classList.contains('dark-mode')
            ? '<i class="bi bi-sun"></i> Light Mode'
            : '<i class="bi bi-moon"></i> Dark Mode';
    };
    document.body.appendChild(darkModeToggle);

    // Initialize map if the container exists
    const mapContainer = document.getElementById('locationMap');
    if (mapContainer) {
        const map = L.map('locationMap').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;

        function updateMarker(lat, lng) {
            if (marker) {
                marker.remove();
            }
            marker = L.marker([lat, lng], {
                title: 'Selected Location',
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = e.target.getLatLng();
                updateLocationInputs(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
            });

            map.setView([lat, lng], 15);
        }

        // Map click handler
        map.on('click', function(e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
            updateLocationInputs(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // Geolocation functions
        function updateLocationInputs(lat, lng) {
            latitudeInput.value = lat;
            longitudeInput.value = lng;
        }

        function reverseGeocode(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    locationInput.value = data.display_name;
                })
                .catch(error => {
                    console.error('Error getting location name:', error);
                });
        }

        // Use My Location button handler
        const useLocationBtn = document.getElementById('useLocationBtn');
        if (useLocationBtn) {
            useLocationBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Geolocation is not supported by your browser',
                        icon: 'error'
                    });
                    return;
                }

                // Show loading state
                const button = this;
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-arrow-repeat"></i> Getting Location...';

                navigator.geolocation.getCurrentPosition(
                    // Success callback
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        updateMarker(lat, lng);
                        updateLocationInputs(lat, lng);
                        reverseGeocode(lat, lng);
                        
                        // Reset button
                        button.disabled = false;
                        button.innerHTML = originalText;

                        // Show success message
                        Swal.fire({
                            title: 'Success',
                            text: 'Your location has been found',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    // Error callback
                    function(error) {
                        // Reset button
                        button.disabled = false;
                        button.innerHTML = originalText;

                        let errorMessage = 'An error occurred while getting your location';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Please allow location access to use this feature';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Location information is unavailable';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Location request timed out';
                                break;
                        }

                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error'
                        });
                    },
                    // Options
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }
    }

    // Media Upload Handling
    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles(Array.from(files));
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(Array.from(e.target.files));
    });

    function handleFiles(files) {
        files.forEach(file => {
            if (file.size > maxFileSize) {
                alert(`File ${file.name} is too large. Maximum size is 10MB.`);
                return;
            }

            if (!file.type.match(/^(image|video)\//)) {
                alert(`File ${file.name} is not a valid image or video.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                addPreviewItem(e.target.result, file.type);
                
                // If it's an image, send it for AI detection
                if (file.type.startsWith('image/')) {
                    validateWasteTypesWithAI(file);
                }
            };
            reader.readAsDataURL(file);
        });
    }

    function addPreviewItem(src, type) {
        const col = document.createElement('div');
        col.className = 'col-12 preview-slide';
        
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        
        const media = type.startsWith('image/') 
            ? document.createElement('img')
            : document.createElement('video');
            
        media.src = src;
        if (type.startsWith('video/')) {
            media.controls = true;
        }
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = () => {
            col.remove();
            updateNavigationVisibility();
            showCurrentSlide(); // Show the next available slide
        };
        
        previewItem.appendChild(media);
        previewItem.appendChild(removeBtn);
        col.appendChild(previewItem);
        mediaContainer.appendChild(col);
        
        updateNavigationVisibility();
        showSlide(mediaContainer.children.length - 1); // Show the newly added slide
    }

    // Preview Navigation
    let currentSlideIndex = 0;

    function updateNavigationVisibility() {
        const items = mediaContainer.children;
        const navigation = document.querySelector('.preview-navigation');
        const slideCounter = document.getElementById('slideCounter');
        
        if (items.length > 0) {
            navigation.classList.remove('d-none');
            if (slideCounter) {
                slideCounter.textContent = `${currentSlideIndex + 1} / ${items.length}`;
            }
        } else {
            navigation.classList.add('d-none');
        }

        // Update navigation button states
        if (prevPreviewBtn) {
            prevPreviewBtn.disabled = currentSlideIndex === 0;
        }
        if (nextPreviewBtn) {
            nextPreviewBtn.disabled = currentSlideIndex >= items.length - 1;
        }
    }

    function showSlide(index) {
        const slides = mediaContainer.children;
        if (slides.length === 0) return;

        // Ensure index is within bounds
        index = Math.max(0, Math.min(index, slides.length - 1));
        currentSlideIndex = index;

        // Hide all slides
        Array.from(slides).forEach((slide, i) => {
            slide.style.display = i === index ? 'block' : 'none';
        });

        updateNavigationVisibility();
    }

    function showCurrentSlide() {
        const slides = mediaContainer.children;
        if (slides.length === 0) {
            currentSlideIndex = 0;
            return;
        }
        // If current slide was removed, show the previous one
        if (currentSlideIndex >= slides.length) {
            currentSlideIndex = slides.length - 1;
        }
        showSlide(currentSlideIndex);
    }

    prevPreviewBtn.addEventListener('click', () => {
        showSlide(currentSlideIndex - 1);
    });

    nextPreviewBtn.addEventListener('click', () => {
        showSlide(currentSlideIndex + 1);
    });

    // Camera/Video Capture
    let currentFacingMode = 'environment';
    let flashEnabled = false;
    const galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
    const galleryGrid = document.getElementById('galleryGrid');
    const switchCameraBtn = document.getElementById('switchCameraBtn');
    const flashBtn = document.getElementById('flashBtn');
    const galleryBtn = document.getElementById('galleryBtn');

    useCameraBtn.addEventListener('click', () => {
        startMediaCapture('photo');
    });

    photoModeBtn.addEventListener('click', () => {
        currentCaptureMode = 'photo';
        photoModeBtn.classList.add('active');
        videoModeBtn.classList.remove('active');
        captureBtn.innerHTML = '<i class="bi bi-camera"></i>';
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        resetPreview();
    });

    videoModeBtn.addEventListener('click', () => {
        currentCaptureMode = 'video';
        videoModeBtn.classList.add('active');
        photoModeBtn.classList.remove('active');
        setupVideoRecording();
        captureBtn.innerHTML = '<i class="bi bi-record-circle"></i>';
        resetPreview();
    });

    switchCameraBtn.addEventListener('click', () => {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        switchCameraBtn.innerHTML = `<i class="bi bi-camera-video-${currentFacingMode === 'environment' ? 'fill' : ''}"></i>`;
        restartCamera();
    });

    flashBtn.addEventListener('click', () => {
        flashEnabled = !flashEnabled;
        flashBtn.classList.toggle('active');
        flashBtn.innerHTML = `<i class="bi bi-lightning-${flashEnabled ? 'fill' : ''}"></i>`;
        if (mediaStream) {
            const track = mediaStream.getVideoTracks()[0];
            const capabilities = track.getCapabilities();
            if (capabilities.torch) {
                track.applyConstraints({
                    advanced: [{ torch: flashEnabled }]
                });
            }
        }
    });

    galleryBtn.addEventListener('click', () => {
        updateGallery();
        galleryModal.show();
    });

    function resetPreview() {
        if (capturePreview) {
            capturePreview.srcObject = null;
            capturePreview.style.display = 'block';
        }
    }

    async function startMediaCapture(mode) {
        try {
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            
            const constraints = {
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: isMobile ? 1920 : 1280 },
                    height: { ideal: isMobile ? 1080 : 720 }
                },
                audio: mode === 'video'
            };
            
            mediaStream = await navigator.mediaDevices.getUserMedia(constraints);
            capturePreview.srcObject = mediaStream;
            
            // Set preview dimensions
            if (capturePreview) {
                capturePreview.style.width = '100%';
                capturePreview.style.height = '100%';
                capturePreview.style.objectFit = 'cover';
            }
            
            if (mode === 'video') {
                setupVideoRecording();
            }
            
            // Show modal
            const modal = document.getElementById('captureModal');
            if (modal) {
                modal.classList.add('mobile-capture-modal');
                captureModal.show();
            }

            // Initialize camera features
            initializeCameraFeatures();
        } catch (err) {
            Swal.fire({
                title: 'Camera Error',
                text: 'Unable to access camera: ' + err.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    function initializeCameraFeatures() {
        if (mediaStream) {
            const track = mediaStream.getVideoTracks()[0];
            const capabilities = track.getCapabilities();
            
            // Check if device has flash/torch
            if (capabilities.torch) {
                flashBtn.style.display = 'flex';
            } else {
                flashBtn.style.display = 'none';
            }
            
            // Check if device has multiple cameras
            if (capabilities.facingMode && capabilities.facingMode.length > 1) {
                switchCameraBtn.style.display = 'flex';
            } else {
                switchCameraBtn.style.display = 'none';
            }
        }
    }

    function restartCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            startMediaCapture(currentCaptureMode);
        }
    }

    function setupVideoRecording() {
        mediaRecorder = new MediaRecorder(mediaStream, {
            mimeType: 'video/webm;codecs=vp8,opus'
        });
        
        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) {
                recordedChunks.push(e.data);
            }
        };
        
        mediaRecorder.onstop = () => {
            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            addPreviewItem(URL.createObjectURL(blob), 'video/webm');
            recordedChunks = [];
            captureBtn.classList.remove('recording');
        };
        
        captureBtn.innerHTML = '<i class="bi bi-record-circle"></i>';
        isRecording = false;
    }

    captureBtn.addEventListener('click', () => {
        if (currentCaptureMode === 'video') {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                captureBtn.innerHTML = '<i class="bi bi-record-circle"></i>';
                captureBtn.classList.remove('recording');
                isRecording = false;
            } else if (mediaRecorder) {
                mediaRecorder.start();
                captureBtn.innerHTML = '<i class="bi bi-stop-circle"></i>';
                captureBtn.classList.add('recording');
                isRecording = true;
            }
        } else {
            // Add capture animation
            captureBtn.classList.add('capturing');
            
            const context = captureCanvas.getContext('2d');
            captureCanvas.width = capturePreview.videoWidth;
            captureCanvas.height = capturePreview.videoHeight;
            context.drawImage(capturePreview, 0, 0);
            
            // Add flash effect
            if (flashEnabled) {
                context.fillStyle = 'rgba(255, 255, 255, 0.3)';
                context.fillRect(0, 0, captureCanvas.width, captureCanvas.height);
            }
            
            captureCanvas.toBlob((blob) => {
                addPreviewItem(URL.createObjectURL(blob), 'image/jpeg');
                captureBtn.classList.remove('capturing');
                captureModal.hide();
            }, 'image/jpeg', 0.9);
        }
    });

    function updateGallery() {
        galleryGrid.innerHTML = '';
        const previewItems = document.querySelectorAll('.preview-item img, .preview-item video');
        
        previewItems.forEach((item, index) => {
            const galleryItem = document.createElement('div');
            galleryItem.className = 'gallery-item';
            
            const media = item.tagName === 'IMG' 
                ? document.createElement('img')
                : document.createElement('video');
            
            media.src = item.src;
            if (item.tagName === 'VIDEO') {
                media.controls = true;
            }
            
            const overlay = document.createElement('div');
            overlay.className = 'gallery-item-overlay';
            overlay.innerHTML = '<i class="bi bi-trash"></i>';
            overlay.onclick = () => {
                const previewSlide = item.closest('.preview-slide');
                if (previewSlide) {
                    previewSlide.remove();
                    updateNavigationVisibility();
                    showCurrentSlide();
                    updateGallery();
                }
            };
            
            galleryItem.appendChild(media);
            galleryItem.appendChild(overlay);
            galleryGrid.appendChild(galleryItem);
        });
    }

    // Handle modal events
    document.getElementById('captureModal').addEventListener('hidden.bs.modal', () => {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        resetPreview();
        mediaStream = null;
        mediaRecorder = null;
        recordedChunks = [];
    });

    // Add orientation change handler for mobile
    window.addEventListener('orientationchange', () => {
        if (mediaStream) {
            restartCamera();
        }
    });

    // Add touch events for mobile
    if (capturePreview) {
        capturePreview.addEventListener('touchstart', (e) => {
            e.preventDefault();
            if (currentCaptureMode === 'photo') {
                captureBtn.click();
            }
        });
    }

    // Location Validation with Map
    if (validateLocationBtn && locationInput) {
        validateLocationBtn.addEventListener('click', function() {
            const location = locationInput.value.trim();
            if (!location) {
                alert('Please enter a location name.');
                return;
            }

            validateLocationBtn.disabled = true;
            validateLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Validating...';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        latitudeInput.value = data[0].lat;
                        longitudeInput.value = data[0].lon;
                        locationInput.value = data[0].display_name;
                        validateLocationBtn.innerHTML = '<i class="bi bi-check-circle"></i> Location Validated';
                        validateLocationBtn.classList.add('btn-success');
                        validateLocationBtn.classList.remove('btn-outline-primary');

                        // Show location on map
                        if (window.map) {
                            window.map.setView([data[0].lat, data[0].lon], 13);
                            L.marker([data[0].lat, data[0].lon]).addTo(window.map);
                        }
                    } else {
                        alert('Location not found. Please try a different location name.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error validating location. Please try again.');
                })
                .finally(() => {
                    validateLocationBtn.disabled = false;
                    if (!validateLocationBtn.classList.contains('btn-success')) {
                        validateLocationBtn.innerHTML = '<i class="bi bi-check-circle"></i> Validate Location';
                    }
                });
        });
    }

    // Initially disable all specific type inputs
    document.querySelectorAll('.wsf-specific-input').forEach(input => {
        input.disabled = true;
    });

    // Waste type selection functions
    function showAllGeneralTypes() {
        allWasteTypeGroups.forEach(group => {
            group.style.display = 'block';
        });
        // Hide all back buttons when showing all types
        document.querySelectorAll('.wsf-back-button').forEach(btn => {
            btn.classList.remove('show');
        });
        // Hide all subtypes when showing all general types
        document.querySelectorAll('.wsf-subtypes').forEach(subtype => {
            subtype.classList.remove('show');
        });
    }

    function hideOtherGeneralTypes(currentGroup) {
        allWasteTypeGroups.forEach(group => {
            if (group !== currentGroup) {
                group.style.display = 'none';
            }
        });
    }

    function createBackButton(currentGroup, btn) {
        let backButton = document.createElement('button');
        backButton.type = 'button';
        backButton.className = 'wsf-back-button';
        backButton.innerHTML = '<i class="bi bi-arrow-left"></i>';
        
        backButton.addEventListener('click', function(e) {
            e.stopPropagation();
            showAllGeneralTypes();
            
            // Keep the active state of the general type button
            if (btn.classList.contains('active')) {
                btn.classList.add('has-selected');
            }
            
            // If it's the "Other" button
            if (btn.id === 'autreBtn') {
                const autreContainer = document.getElementById('autreInputContainer');
                if (!btn.classList.contains('active')) {
                    autreContainer.classList.add('wsf-hidden');
                }
            }
        });
        
        currentGroup.insertBefore(backButton, currentGroup.firstChild);
        backButton.classList.add('show');
        return backButton;
    }

    // Handle general waste type selection
    wasteTypeButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isAutreBtn = btn.id === 'autreBtn';
            
            if (isAutreBtn) {
                // Handle "Other" button click
                const autreContainer = document.getElementById('autreInputContainer');
                const currentGroup = btn.closest('.wsf-type-group');
                
                if (autreContainer.classList.contains('wsf-hidden')) {
                    // Show custom input
                    autreContainer.classList.remove('wsf-hidden');
                    hideOtherGeneralTypes(currentGroup);
                    createBackButton(currentGroup, btn);
                    btn.classList.add('active');
                } else {
                    // Hide custom input
                    autreContainer.classList.add('wsf-hidden');
                    btn.classList.remove('active');
                    // Show all general types if no other type is selected
                    const activeButtons = document.querySelectorAll('.wsf-btn-option.wsf-general-type:not(#autreBtn).active');
                    if (activeButtons.length === 0) {
                        showAllGeneralTypes();
                    }
                }
                return;
            }
            
            // Handle regular waste type buttons
            const wasteTypeId = btn.dataset.wasteType;
            const currentGroup = btn.closest('.wsf-type-group');
            const subTypesContainer = document.getElementById('subTypes_' + wasteTypeId);
            const generalTypeInput = currentGroup.querySelector('.wsf-type-input');
            
            // Toggle active state
            btn.classList.toggle('active');
            
            if (btn.classList.contains('active')) {
                // Button is being activated
                generalTypeInput.value = wasteTypeId;
                hideOtherGeneralTypes(currentGroup);
                createBackButton(currentGroup, btn);
                
                if (subTypesContainer) {
                    subTypesContainer.classList.add('show');
                    // Enable specific type inputs for this group
                    subTypesContainer.querySelectorAll('.wsf-specific-input').forEach(input => {
                        input.disabled = false;
                    });
                }
            } else {
                // Button is being deactivated
                generalTypeInput.value = '';
                btn.classList.remove('has-selected');
                
                if (subTypesContainer) {
                    // Disable and deselect all specific type inputs in this group
                    subTypesContainer.querySelectorAll('.wsf-specific-input').forEach(input => {
                        input.disabled = true;
                        input.parentElement.querySelector('.wsf-specific-type').classList.remove('active');
                    });
                }
                
                // Show all general types if no other type is selected
                const activeButtons = document.querySelectorAll('.wsf-btn-option.wsf-general-type:not(#autreBtn).active');
                if (activeButtons.length === 0) {
                    showAllGeneralTypes();
                }
            }
        });
    });

    // Handle specific type selection
    document.querySelectorAll('.wsf-btn-option.wsf-specific-type').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const specificId = btn.dataset.specificId;
            const parentId = btn.dataset.parentId;
            const input = btn.parentElement.querySelector('.wsf-specific-input');
            const generalTypeBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
            
            btn.classList.toggle('active');
            if (btn.classList.contains('active')) {
                input.disabled = false;
                generalTypeBtn.classList.add('has-selected');
            } else {
                input.disabled = true;
                // Check if any other specific types are selected under this general type
                const hasOtherSelected = Array.from(btn.closest('.wsf-subtypes').querySelectorAll('.wsf-specific-type'))
                    .some(specificBtn => specificBtn !== btn && specificBtn.classList.contains('active'));
                if (!hasOtherSelected) {
                    generalTypeBtn.classList.remove('has-selected');
                }
            }
        });
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        // Disable all unselected specific waste type inputs before form submission
        document.querySelectorAll('.wsf-specific-input').forEach(input => {
            const button = input.parentElement.querySelector('.wsf-specific-type');
            if (!button.classList.contains('active')) {
                input.disabled = true;
            }
        });
        
        // Validate custom type if the input is visible
        const autreContainer = document.getElementById('autreInputContainer');
        if (!autreContainer.classList.contains('wsf-hidden')) {
            const autreInput = document.getElementById('autreInput');
            if (!autreInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a waste type description for "Other"');
                return;
            }
        }
        
        // Check if at least one waste type is selected (either regular or custom)
        const hasSelectedTypes = document.querySelectorAll('.wsf-specific-type.active').length > 0;
        const hasCustomType = !autreContainer.classList.contains('wsf-hidden') && document.getElementById('autreInput').value.trim();
        
        if (!hasSelectedTypes && !hasCustomType) {
            e.preventDefault();
            alert('Please select at least one waste type or specify a custom type');
        }
    });

    // Form Submission with Confetti
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
            
            // Validate form data
            const formData = new FormData(this);
            const latitude = formData.get('latitude');
            const longitude = formData.get('longitude');
            
            if (!latitude || !longitude) {
                alert('Please select a location.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                return;
            }
            
            // Get all selected general types
            const selectedGeneralTypes = Array.from(document.querySelectorAll('.wsf-general-type.active'))
                .map(btn => btn.dataset.wasteType)
                .filter(id => id);
            
            // Get all checked specific types
            const selectedSpecificTypes = Array.from(document.querySelectorAll('.wsf-specific-type.active'))
                .map(btn => btn.dataset.specificId);
            
            // Get custom type if entered
            const autreInput = document.getElementById('autreInput');
            const customType = autreInput ? autreInput.value.trim() : '';
            
            // Validate waste types
            if (selectedGeneralTypes.length === 0 && selectedSpecificTypes.length === 0 && !customType) {
                alert('Please select at least one waste type or enter a custom type.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
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
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData,
                credentials: 'same-origin'
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
                            window.location.href = '/signal/thank-you';
                        }
                    });
                } else {
                    window.location.href = '/signal/thank-you';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the form. Please try again.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    }

    // Initialize Leaflet Map
    if (document.getElementById('map')) {
        window.map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.map);
    }
});

async function validateWasteTypesWithAI(file) {
    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch('http://localhost:5000/detect', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('AI validation failed');
        }

        const data = await response.json();
        
        if (data.success && data.detections) {
            // Get all selected waste types
            const selectedTypes = document.querySelectorAll('.waste-type-btn.active');
            const selectedValues = Array.from(selectedTypes).map(btn => btn.getAttribute('data-waste-type').toLowerCase());
            
            // Get AI detected types
            const detectedTypes = data.detections.map(d => d.class.toLowerCase());
            
            // Check if there's any mismatch
            let mismatchFound = false;
            let suggestedTypes = [];
            
            detectedTypes.forEach(type => {
                if (!selectedValues.includes(type)) {
                    mismatchFound = true;
                    suggestedTypes.push(type);
                }
            });
            
            if (mismatchFound) {
                // Create a suggestion alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
                alertDiv.innerHTML = `
                    <strong>AI Suggestion:</strong> The uploaded image appears to contain the following waste types: 
                    ${suggestedTypes.join(', ')}. Consider updating your selection.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                // Add the alert before the form
                const form = document.querySelector('#waste-signal-form');
                form.parentNode.insertBefore(alertDiv, form);
            }
        }
    } catch (error) {
        console.error('Error during AI validation:', error);
    }
}

async function validateWasteTypeWithAI(imageFile) {
    const formData = new FormData();
    formData.append('image', imageFile);

    try {
        const response = await fetch('http://127.0.0.1:5000/detect', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('AI detection failed');
        }

        const data = await response.json();
        
        if (data.success && data.detections.length > 0) {
            // Get currently selected waste types
            const selectedTypes = getSelectedWasteTypes();
            
            // Compare with AI detections
            const aiSuggestions = data.detections.map(d => d.class.toLowerCase());
            const matches = selectedTypes.filter(type => 
                aiSuggestions.some(suggestion => 
                    suggestion.includes(type.toLowerCase()) || 
                    type.toLowerCase().includes(suggestion)
                )
            );
            
            // Show feedback to user
            showAIFeedback(matches, data.detections, selectedTypes);
        }
    } catch (error) {
        console.error('AI Detection Error:', error);
    }
}

function getSelectedWasteTypes() {
    const selectedTypes = [];
    
    // Get general waste types
    document.querySelectorAll('.wsf-general-type.active').forEach(btn => {
        const typeName = btn.textContent.trim();
        if (typeName !== 'Other') {
            selectedTypes.push(typeName);
        }
    });
    
    // Get specific waste types
    document.querySelectorAll('.wsf-specific-type.active').forEach(btn => {
        selectedTypes.push(btn.textContent.trim());
    });
    
    // Get custom type if any
    const customType = document.getElementById('autreInput')?.value;
    if (customType) {
        selectedTypes.push(customType);
    }
    
    return selectedTypes;
}

function showAIFeedback(matches, detections, selectedTypes) {
    // Create or get feedback container
    let feedbackContainer = document.querySelector('.ai-feedback');
    if (!feedbackContainer) {
        feedbackContainer = document.createElement('div');
        feedbackContainer.className = 'ai-feedback alert mt-3';
        document.querySelector('.media-upload-section').appendChild(feedbackContainer);
    }
    
    // Determine feedback type and message
    if (matches.length > 0) {
        feedbackContainer.className = 'ai-feedback alert alert-success mt-3';
        feedbackContainer.innerHTML = `
            <h5><i class="bi bi-check-circle"></i> AI Validation Result</h5>
            <p>The AI model confirms your waste type selection!</p>
            <small>Detected types: ${detections.map(d => 
                `${d.class} (${d.confidence}% confidence)`).join(', ')}</small>
        `;
    } else {
        feedbackContainer.className = 'ai-feedback alert alert-warning mt-3';
        feedbackContainer.innerHTML = `
            <h5><i class="bi bi-exclamation-triangle"></i> AI Validation Result</h5>
            <p>The AI model detected different waste types than selected:</p>
            <ul>
                <li>Your selection: ${selectedTypes.join(', ')}</li>
                <li>AI detection: ${detections.map(d => 
                    `${d.class} (${d.confidence}% confidence)`).join(', ')}</li>
            </ul>
            <p>Please review your selection or add more specific details in the description.</p>
        `;
    }
} 