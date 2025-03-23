<div class="message-input-container border-top p-3">
    <form action="{{ route('messaging.messages.store', $chatRoom) }}" method="POST" enctype="multipart/form-data" class="message-form">
        @csrf
        <div class="message-input-container">
            <div class="input-group">
                <input type="text" 
                       name="message_content" 
                       class="form-control" 
                       placeholder="Type your message...">
                
                <input type="file" 
                       name="files[]"
                       id="file-upload" 
                       class="d-none" 
                       accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                       multiple>
                
                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('file-upload').click()">
                    <i class="bi bi-paperclip"></i>
                </button>
                
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i>
                </button>
            </div>
            
            <div id="file-preview" class="mt-2 d-none">
                <div id="preview-grid" class="preview-grid">
                    <!-- Previews will be inserted here -->
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.message-input-container {
    background: #fff;
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.input-group {
    border-radius: 0.75rem;
    overflow: hidden;
}

.input-group .form-control {
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
}

.input-group .btn {
    padding: 0.75rem 1.25rem;
    transition: all 0.2s;
}

.input-group .btn-outline-secondary {
    border-color: #e0e0e0;
}

.input-group .btn-outline-secondary:hover {
    background-color: #f8f9fa;
}

.input-group .btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.input-group .btn-primary:hover {
    background-color: #0b5ed7;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.preview-item {
    position: relative;
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 0.5rem;
    text-align: center;
}

.preview-item img {
    max-width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 0.375rem;
}

.preview-item .file-name {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
    word-break: break-all;
}

.remove-file {
    position: absolute;
    top: -0.5rem;
    right: -0.5rem;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.remove-file:hover {
    background: #bb2d3b;
    transform: scale(1.1);
}

.file-type-icon {
    font-size: 2rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}
</style>

<script>
document.getElementById('file-upload').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const preview = document.getElementById('file-preview');
    const previewGrid = document.getElementById('preview-grid');
    
    if (files.length > 0) {
        preview.classList.remove('d-none');
        previewGrid.innerHTML = ''; // Clear existing previews
        
        files.forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-file';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.onclick = () => removeFile(index);
            
            if (file.type.startsWith('image/')) {
                // Image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <div class="file-name">${file.name}</div>
                    `;
                    previewItem.appendChild(removeBtn);
                };
                reader.readAsDataURL(file);
            } else {
                // File icon for non-images
                let icon = 'bi-file-earmark';
                if (file.type.includes('pdf')) icon = 'bi-file-earmark-pdf';
                else if (file.type.includes('word')) icon = 'bi-file-earmark-word';
                
                previewItem.innerHTML = `
                    <i class="bi ${icon} file-type-icon"></i>
                    <div class="file-name">${file.name}</div>
                `;
                previewItem.appendChild(removeBtn);
            }
            
            previewGrid.appendChild(previewItem);
        });
    } else {
        preview.classList.add('d-none');
    }
});

function removeFile(index) {
    const input = document.getElementById('file-upload');
    const files = Array.from(input.files);
    
    // Create a new FileList without the removed file
    const dt = new DataTransfer();
    files.forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    
    input.files = dt.files;
    
    // Trigger change event to update preview
    input.dispatchEvent(new Event('change'));
}
</script>
