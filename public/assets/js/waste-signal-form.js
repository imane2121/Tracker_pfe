// Waste Signal Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form elements
    const form = document.querySelector('.waste-signal-form');
    const mediaContainer = document.getElementById('mediaContainer');
    const locationInput = document.getElementById('location');
    const validateLocationBtn = document.getElementById('validateLocationBtn');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const darkModeToggle = document.createElement('button');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Dark Mode Toggle
    darkModeToggle.className = 'btn btn-dark position-fixed bottom-0 end-0 m-3';
    darkModeToggle.innerHTML = '<i class="bi bi-moon"></i> Dark Mode';
    darkModeToggle.onclick = () => {
        document.body.classList.toggle('dark-mode');
        darkModeToggle.innerHTML = document.body.classList.contains('dark-mode')
            ? '<i class="bi bi-sun"></i> Light Mode'
            : '<i class="bi bi-moon"></i> Dark Mode';
    };
    document.body.appendChild(darkModeToggle);

    // Drag and Drop for Media Upload
    if (mediaContainer) {
        mediaContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            mediaContainer.classList.add('dragover');
        });

        mediaContainer.addEventListener('dragleave', () => {
            mediaContainer.classList.remove('dragover');
        });

        mediaContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            mediaContainer.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFiles(files);
            }
        });
    }

    // Handle File Uploads
    function handleFiles(files) {
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.innerHTML = file.type.startsWith('image/') 
                    ? `<img src="${e.target.result}" alt="Preview">`
                    : `<video src="${e.target.result}" controls></video>`;
                mediaContainer.appendChild(preview);
            };
            reader.readAsDataURL(file);
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