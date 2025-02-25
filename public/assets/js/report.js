/*report waste page */
// Automatic Location Detection 
document.addEventListener('DOMContentLoaded', () => {
    const locationInput = document.getElementById('location');
    const refreshLocationButton = document.getElementById('refresh-location');
  
    const getLocation = () => {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const { latitude, longitude } = position.coords;
            locationInput.value = `Lat: ${latitude}, Long: ${longitude}`;
          },
          (error) => {
            console.error('Error getting location:', error);
            locationInput.value = 'Location not available';
          }
        );
      } else {
        locationInput.value = 'Geolocation is not supported by this browser.';
      }
    };
  
    getLocation(); // Get location on page load
    refreshLocationButton.addEventListener('click', getLocation);
  });
  
  // Dynamic Type Selection Based on Material
  const materialSelect = document.getElementById('material');
  const typeGroup = document.getElementById('type-group');
  const typeSelect = document.getElementById('type');
  const materialTypeList = document.getElementById('material-type-list');
  
  const materialTypeMap = {
    plastiques: ['Plastic bottles', 'Food packaging', 'Plastic bags and sachets', 'Microplastics'],
    metaux: ['Cans and metal boxes', 'Scrap metal fragments', 'Construction materials'],
    verre: ['Glass bottles', 'Broken glass fragments'],
    'dechets-dangereux': ['Batteries', 'Used oils', 'Chemical containers'],
    'materiel-peche': ['Lost fishing nets', 'Ropes and fishing lines', 'Hooks and sinkers'],
    bois: ['Treated wood', 'Wooden objects'],
    organique: ['Food scraps'],
    textiles: ['Clothing', 'Fabric pieces'],
  };
  
  materialSelect.addEventListener('change', () => {
    const selectedMaterial = materialSelect.value;
    const types = materialTypeMap[selectedMaterial] || [];
    typeSelect.innerHTML = types.map(type => `<option value="${type}">${type}</option>`).join('');
    typeGroup.style.display = selectedMaterial ? 'block' : 'none';
  });
  
  // Add Material & Type Button
  document.getElementById('add-material-type').addEventListener('click', () => {
    const selectedMaterial = materialSelect.options[materialSelect.selectedIndex].text;
    const selectedType = typeSelect.value;
  
    if (selectedMaterial && selectedType) {
      const materialTypeItem = document.createElement('div');
      materialTypeItem.className = 'material-type-item';
      materialTypeItem.innerHTML = `
        <span>${selectedMaterial}: ${selectedType}</span>
        <button type="button" class="remove-button">✖</button>
      `;
      materialTypeList.appendChild(materialTypeItem);
  
      // Reset dropdowns
      materialSelect.value = '';
      typeSelect.value = '';
      typeGroup.style.display = 'none';
    }
  });
  
  // Remove Material & Type
  materialTypeList.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-button')) {
      e.target.parentElement.remove();
    }
  });
  
  // Photo Upload Preview
  const photoInput = document.getElementById('photo');
  const photoPreview = document.getElementById('photo-preview');
  photoInput.addEventListener('change', () => {
    photoPreview.innerHTML = '';
    Array.from(photoInput.files).forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement('img');
        img.src = e.target.result;
        photoPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });
  
  // Form Submission
  document.getElementById('trash-report-form').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Report submitted successfully!');
    // Here you can add code to send the form data to a server
  });
  
  // Add Custom Type Button
  document.getElementById('add-custom-type').addEventListener('click', () => {
    const selectedMaterial = materialSelect.options[materialSelect.selectedIndex].text;
    const customType = document.getElementById('manual-type').value.trim();
  
    if (selectedMaterial && customType) {
      const materialTypeItem = document.createElement('div');
      materialTypeItem.className = 'material-type-item';
      materialTypeItem.innerHTML = `
        <span>${selectedMaterial}: ${customType}</span>
        <button type="button" class="remove-button">✖</button>
      `;
      materialTypeList.appendChild(materialTypeItem);
  
      // Reset dropdowns and input
      materialSelect.value = '';
      typeSelect.value = '';
      typeGroup.style.display = 'none';
      document.getElementById('manual-type').value = '';
    }
  });
  