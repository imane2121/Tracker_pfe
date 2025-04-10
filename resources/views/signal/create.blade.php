@extends('layouts.app')

@section('content')
<div class="container my-5 mb-7">
    <!-- Encouraging Message -->
    <div class="text-center mb-5">
        <div class="encouragement-card">
            <div class="wave-animation">
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>
            </div>
            <div class="card-content">
                <h2 class="title">
                    <span class="highlight">Few Steps to</span>
                    <span class="highlight">A Cleaner Ocean</span>
            </h2>
                <p class="message">
                Your report helps us protect marine life
                and keep our waters clean
            </p>
            </div>
        </div>
    </div>

    <form action="{{ route('signal.store') }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
        @csrf
        
        <!-- Waste Types Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Please Select Waste Type</h10>
            </div>
            <div class="card-body">
                <div class="waste-type-selection">
                    <!-- Main Category Dropdown -->
                    <div class="form-group mb-3">
                        <label for="generalWasteType" class="form-label">Main Category</label>
                        <select class="form-select main-category" id="generalWasteType">
                            <option value="">Select a main category</option>
                            @foreach($wasteTypes as $wasteType)
                                <option value="{{ $wasteType->id }}">{{ $wasteType->name }}</option>
                            @endforeach
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Specific Types Container -->
                    <div class="specific-types-container mb-3" style="display: none;">
                        <label class="form-label">Specific Types</label>
                        <div class="specific-types-grid">
                            @foreach($wasteTypes as $wasteType)
                                <div class="specific-type-group" data-parent="{{ $wasteType->id }}" style="display: none;">
                                    @foreach($wasteType->specificWasteTypes as $specificType)
                                        <div class="form-check specific-type-item">
                                            <input type="checkbox" 
                                                class="form-check-input specific-type-checkbox" 
                                                name="waste_types[]" 
                                                value="{{ $specificType->id }}" 
                                                id="type_{{ $specificType->id }}">
                                            <label class="form-check-label" for="type_{{ $specificType->id }}">
                                                {{ $specificType->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Other Type Input -->
                    <div class="form-group other-type-input" style="display: none;">
                        <label for="customType" class="form-label">Specify Other Type</label>
                        <input type="text" class="form-control" id="customType" name="customType" placeholder="Enter waste type">
                    </div>

                    <!-- Selected Types Preview -->
                    <div class="selected-types-preview mt-3" style="display: none;">
                        <label class="form-label">Selected Types:</label>
                        <div class="selected-types-badges"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Location Details</h10>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                        <label for="location" class="form-label">Location Name</label>
                            <div class="mb-2">
                                <input type="text" class="form-control" id="location" name="location" required placeholder="Enter or validate a location">
                            </div>
                                <button type="button" class="btn btn-primary w-100 use-location-btn" id="validateLocationBtn" title="Validate Location">
                                <i class="bi bi-check-circle me-1"></i> Validate Location
                                </button>
                </div>
                        <div class="mb-3 volume-input">
                            <label for="volume" class="form-label">Volume (m³)</label>
                            <input type="number" class="form-control" id="volume" name="volume" min="0" step="0.1" required placeholder="Enter waste volume">
                        </div>
                        <div class="row coordinate-inputs">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" class="form-control" id="latitude" name="latitude" step="any" required placeholder="Latitude">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" class="form-control" id="longitude" name="longitude" step="any" required placeholder="Longitude">
                    </div>
                </div>
                        <button type="button" class="btn btn-primary w-100 use-location-btn" id="useLocationBtn">
                        <i class="bi bi-geo-alt"></i> Use My Location
                    </button>
                    </div>
                    <div class="col-md-6">
                        <div id="locationMap" class="rounded shadow-sm" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Add Photos/Videos</h10>
            </div>
            <div class="card-body">
                <div class="media-upload-section mb-4">
                    <div class="upload-area" id="uploadArea">
                        <i class="bi bi-cloud-upload"></i>
                        <p>Drag & drop files here or click to select</p>
                        <small class="text-muted">Supported formats: Images and Videos (max 10MB)</small>
                        <input type="file" id="fileInput" name="media[]" accept="image/*,video/*" multiple class="d-none">
                            </div>
                    
                    <div class="preview-container position-relative">
                        <div id="mediaContainer" class="row g-3">
                            <!-- Preview items will be added here -->
                        </div>
                        <div class="preview-navigation d-none">
                            <button type="button" class="btn btn-light preview-nav" id="prevPreview">
                                <i class="bi bi-arrow-left-short fs-4"></i>
                            </button>
                            <div id="slideCounter" class="slide-counter">0 / 0</div>
                            <button type="button" class="btn btn-light preview-nav" id="nextPreview">
                                <i class="bi bi-arrow-right-short fs-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="media-actions mt-3 text-center">
                        <button type="button" class="btn btn-primary" id="useCameraBtn">
                            <i class="bi bi-camera"></i> Use My Camera
                        </button>
                    </div>
                </div>
                
                <!-- Camera/Video capture modal -->
                <div class="modal fade" id="captureModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div class="mode-toggle">
                                        <button type="button" class="btn btn-light" id="photoModeBtn">
                                            <i class="bi bi-camera"></i> Photo
                                        </button>
                                        <button type="button" class="btn btn-light" id="videoModeBtn">
                                            <i class="bi bi-camera-video"></i> Video
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-light close-camera" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="modal-body p-0">
                                <div class="camera-preview-container">
                                    <video id="capturePreview" autoplay playsinline></video>
                                    <canvas id="captureCanvas" style="display: none;"></canvas>
                                    <div class="camera-overlay">
                                        <div class="camera-grid"></div>
                                        <div class="camera-focus-point"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="camera-controls">
                                    <button type="button" class="btn btn-light" id="switchCameraBtn">
                                        <i class="bi bi-camera-video"></i>
                                    </button>
                                    <button type="button" class="btn btn-light" id="flashBtn">
                                        <i class="bi bi-lightning"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary capture-btn" id="captureBtn">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                    <button type="button" class="btn btn-light" id="galleryBtn">
                                        <i class="bi bi-images"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Additional Details</h10>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                        placeholder="Add any additional information about the waste..."></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex gap-3 justify-content-end w-100 align-items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg cancel-btn">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
            <button type="submit" class="btn btn-success btn-lg btn btn-primary w-50 use-location-btn ms-auto">
                <i class="bi bi-check-circle"></i> Submit Report
            </button>
        </div>

    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Add spacing utility class */
    .mb-7 {
        margin-bottom: 7rem !important;
    }

    .waste-signal-form .card {
        border: none;
        transition: transform 0.2s;
    }

    .waste-signal-form .card:hover {
        transform: translateY(-2px);
    }

    .waste-signal-form .card-header {
        border-bottom: none;
        padding: 1rem;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
    }

    .wsf-btn-option {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        margin: 0.25rem;
        border: none;
        border-radius: 25px;
        background-color: #e9ecef;
        color: #495057;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        width: calc(50% - 0.5rem); /* Make buttons take up half the width on mobile */
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .wsf-btn-option:hover {
        background-color: #dee2e6;
        transform: translateY(-1px);
    }

    .wsf-btn-option.active {
        background-color: #198754;
        color: white;
    }

    .wsf-btn-option.has-selected {
        background-color: #198754;
        color: white;
    }

    .wsf-btn-option.has-selected::after {
        content: '';
    }

    .wsf-type-group {
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        position: relative;
        width: 100%;
    }

    .wsf-back-button {
        position: fixed;
        top: 1rem;
        left: 1rem;
        padding: 0.5rem;
        background: #198754;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        transition: all 0.3s ease;
        z-index: 1000;
        width: 40px;
        height: 40px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .wsf-back-button:hover {
        transform: scale(1.1);
        background: #157347;
    }

    .wsf-back-button.show {
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    .wsf-subtypes {
        display: none;
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 15px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .wsf-subtypes.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    .wsf-subtype-item {
        margin: 0.5rem 0;
        width: 100%;
    }

    .wsf-specific-type {
        width: 100% !important;
        text-align: left !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.95em;
        background-color: white !important;
        border: 1px solid #dee2e6 !important;
    }

    .wsf-specific-type:hover {
        background-color: #f8f9fa !important;
        border-color: #198754 !important;
    }

    .wsf-specific-type.active {
        background-color: #198754 !important;
        color: white !important;
        border-color: #198754 !important;
    }

    .image-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-container:hover {
        border-color: #198754;
    }

    .image-preview {
        width: 100%;
        height: 100%;
        background-color: #f8f9fa;
    }

    .image-preview img,
    .image-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .wsf-hidden {
        display: none !important;
    }

    @media (max-width: 768px) {
        .wsf-buttons-container {
            flex-direction: column;
            gap: 0.5rem;
        }

        .wsf-btn-option {
            width: 100%;
            margin: 0.25rem 0;
        }

        .wsf-back-button {
            top: 0.5rem;
            left: 0.5rem;
        }

        .wsf-subtypes {
            margin-left: 0;
            padding: 0.75rem;
        }

        .waste-signal-form .image-container {
            width: 120px;
            height: 120px;
        }

        .location-input-group .form-control {
            font-size: 0.95rem;
        }

        .use-location-btn {
            margin-top: 1rem;
        }
    }

    #locationMap {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    #locationMap:hover {
        border-color: var(--primary-color);
    }

    .location-marker {
        animation: markerPulse 1.5s infinite;
    }

    @keyframes markerPulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .map-tooltip {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Location Input Styles */
    .location-input-group {
        position: relative;
    }

    .location-input-group .form-control {
        padding-right: 45px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .location-input-group .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    .location-input-group .btn {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 0 8px 8px 0;
    }

    .location-input-group .btn:hover {
        color: var(--primary-gradient-start);
        background-color: rgba(32, 84, 144, 0.1);
    }

    .location-input-group .btn.btn-success {
        color: #fff;
        background-color: #198754;
    }

    .location-input-group .btn.btn-success:hover {
        background-color: #157347;
    }

    .location-input-group .btn i {
            font-size: 1.1rem;
        }

    /* Volume Input Styles */
    .volume-input {
        position: relative;
    }

    .volume-input .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .volume-input .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    /* Coordinate Inputs */
    .coordinate-inputs .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .coordinate-inputs .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }
    /* Use Location Button */
    .use-location-btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        border: none;
    }

    .use-location-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .use-location-btn:active {
        transform: translateY(0);
    }

    .use-location-btn i {
        margin-right: 0.5rem;
    }

    /* Map Container */
    #locationMap {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Media Upload Styles */
    .upload-container {
        width: 100%;
    }

    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-area:hover, .upload-area.dragover {
        border-color: #0e346a;
        background-color: rgba(14, 52, 106, 0.05);
    }

    .media-upload-section {
        width: 100%;
    }

    .media-actions {
        margin-top: 1rem;
        text-align: center;
        width: 100%;
    }

    .media-actions .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .media-upload-section {
            padding: 0.5rem;
        }

        .upload-area {
            padding: 1rem;
        }

        .media-actions {
            margin-top: 0.5rem;
        }

        .media-actions .btn {
            width: 100%;
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
        }

        .preview-item {
            height: 150px;
        }
    }

    .media-preview-container {
        position: relative;
        min-height: 100px;
    }

    .preview-item {
        position: relative;
        width: 100%;
        height: 200px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .preview-item img, 
    .preview-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-item .remove-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(130, 50, 50, 0.9);
        border: none;
        border-radius: 50%;
        width: 30px !important;
        height: 30px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .preview-item .remove-btn:hover {
        background: #fff;
        transform: scale(1.1);
    }

    .preview-navigation {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        z-index: 10;
        pointer-events: none;
    }

    .preview-nav {
        pointer-events: auto;
        background: rgba(0, 0, 0, 0.5) !important;
        color: white !important;
        border: none !important;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        margin: 0 0.5rem;
        transition: all 0.2s ease;
        padding: 0 !important;
    }

    .preview-nav i {
        font-size: 1.5rem;
        line-height: 1;
    }

    .preview-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .preview-nav:not(:disabled):hover {
        background: rgba(0, 0, 0, 0.7) !important;
        transform: scale(1.1);
    }

    .slide-counter {
        pointer-events: auto;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .preview-navigation {
            padding: 0 0.5rem;
        }

        .preview-nav {
            width: 35px;
            height: 35px;
            margin: 0 0.25rem;
        }

        .preview-nav i {
            font-size: 1.25rem;
        }

        .slide-counter {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            min-width: 60px;
            text-align: center;
        }

        .media-upload-section {
            padding: 0.5rem;
        }

        .upload-area {
            padding: 1rem;
        }

        .media-actions {
            margin-top: 0.5rem;
        }

        .media-actions .btn {
            width: 100%;
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
        }

        .preview-item {
            height: 150px;
        }
    }

    .capture-mode-toggle .btn-group {
        width: 100%;
        max-width: 300px;
    }

    .capture-mode-toggle .btn {
        width: 50%;
    }

    /* AI Feedback Styles */
    .ai-feedback {
        margin-top: 1rem;
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .ai-feedback h5 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }

    .ai-feedback p {
        margin-bottom: 0.5rem;
    }

    .ai-feedback ul {
        margin-bottom: 0.5rem;
        padding-left: 1.25rem;
    }

    .ai-feedback small {
        display: block;
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .ai-feedback {
            margin-top: 0.75rem;
            padding: 0.75rem;
        }

        .ai-feedback h5 {
            font-size: 1rem;
        }

        .ai-feedback p,
        .ai-feedback ul {
            font-size: 0.9rem;
        }
    }

    /* Waste Type Selection Styles */
    .waste-type-selection {
        max-width: 800px;
        margin: 0 auto;
    }

    .main-category {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .main-category:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    .specific-types-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .specific-type-item {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .specific-type-item:hover {
        border-color: var(--primary-gradient-start);
        background-color: #f8f9fa;
    }

    .form-check-input:checked + .form-check-label {
        color: var(--primary-gradient-start);
        font-weight: 500;
    }

    .selected-types-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .selected-type-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        gap: 0.5rem;
    }

    .remove-type {
        background: none;
        border: none;
        color: white;
        padding: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .remove-type:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 768px) {
        .specific-types-grid {
            grid-template-columns: 1fr;
        }

        .specific-type-item {
            margin-bottom: 0.5rem;
        }
    }

    /* Submit Button Styles */
    .cancel-btn {
        width: auto;
        min-width: 120px;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .cancel-btn:hover {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    @media (max-width: 768px) {
        .cancel-btn {
            min-width: 100px;
            padding: 0.5rem 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Waste Type Selection Handling
    const generalSelect = document.getElementById('generalWasteType');
    const specificTypesContainer = document.querySelector('.specific-types-container');
    const otherTypeInput = document.querySelector('.other-type-input');
    const selectedTypesPreview = document.querySelector('.selected-types-preview');
    const selectedTypesBadges = document.querySelector('.selected-types-badges');
    const specificTypeGroups = document.querySelectorAll('.specific-type-group');
    const checkboxes = document.querySelectorAll('.specific-type-checkbox');

    // Handle main category selection
    generalSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        
        // Hide all specific type groups first
        specificTypeGroups.forEach(group => group.style.display = 'none');
        
        if (selectedValue === 'other') {
            specificTypesContainer.style.display = 'none';
            otherTypeInput.style.display = 'block';
        } else if (selectedValue) {
            specificTypesContainer.style.display = 'block';
            otherTypeInput.style.display = 'none';
            
            // Show specific types for selected category
            const selectedGroup = document.querySelector(`.specific-type-group[data-parent="${selectedValue}"]`);
            if (selectedGroup) {
                selectedGroup.style.display = 'block';
            }
        } else {
            specificTypesContainer.style.display = 'none';
            otherTypeInput.style.display = 'none';
        }
        
        updateSelectedTypesPreview();
    });

    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTypesPreview);
    });

    function updateSelectedTypesPreview() {
        const selectedTypes = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => ({
                id: cb.value,
                name: cb.nextElementSibling.textContent.trim()
            }));

        if (selectedTypes.length > 0) {
            selectedTypesPreview.style.display = 'block';
            selectedTypesBadges.innerHTML = selectedTypes.map(type => `
                <div class="selected-type-badge">
                    ${type.name}
                    <button type="button" class="remove-type" data-id="${type.id}">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `).join('');

            // Add click handlers for remove buttons
            document.querySelectorAll('.remove-type').forEach(btn => {
                btn.addEventListener('click', function() {
                    const typeId = this.dataset.id;
                    const checkbox = document.querySelector(`input[value="${typeId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        updateSelectedTypesPreview();
                    }
                });
            });
        } else {
            selectedTypesPreview.style.display = 'none';
        }
    }

    // Initialize map with Morocco center coordinates
    var map = L.map('locationMap').setView([31.7917, -7.0926], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker;
    
    // Define Morocco's coastal boundaries (rough approximation)
    const coastalBoundaries = [
        // Atlantic Coast (North to South)
        { lat: 35.9191, lng: -5.8659 },  // Tangier
        { lat: 34.0531, lng: -6.7988 },  // Rabat
        { lat: 33.5992, lng: -7.6338 },  // Casablanca
        { lat: 32.2994, lng: -9.2372 },  // El Jadida
        { lat: 31.5085, lng: -9.7595 },  // Essaouira
        { lat: 30.4278, lng: -9.5981 },  // Agadir
        { lat: 28.4520, lng: -11.1514 }, // Sidi Ifni
        { lat: 27.9397, lng: -12.9264 }, // Laayoune
        { lat: 23.7141, lng: -15.9369 }, // Dakhla
        
        // Mediterranean Coast (East to West)
        { lat: 35.1736, lng: -2.9287 },  // Nador
        { lat: 35.2540, lng: -3.9375 },  // Al Hoceima
        { lat: 35.5689, lng: -5.3565 }   // Tetouan
    ];

    // Maximum distance from coast in kilometers
    const MAX_DISTANCE_FROM_COAST = 5;

    // Function to calculate distance between two points using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in kilometers
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Function to check if location is near the coast
    function isNearCoast(lat, lng) {
        let minDistance = Infinity;
        let nearestPoint = null;

        // Convert coordinates to numbers to ensure proper comparison
        lat = parseFloat(lat);
        lng = parseFloat(lng);

        coastalBoundaries.forEach(point => {
            const distance = calculateDistance(lat, lng, point.lat, point.lng);
            if (distance < minDistance) {
                minDistance = distance;
                nearestPoint = point;
            }
        });

        return {
            isValid: minDistance <= MAX_DISTANCE_FROM_COAST,
            distance: minDistance,
            nearestPoint: nearestPoint
        };
    }

    // Function to update marker position with coastal validation
    function updateMarkerPosition(lat, lng, shouldZoom = true) {
        // Convert coordinates to numbers
        lat = parseFloat(lat);
        lng = parseFloat(lng);
        
        const coastalCheck = isNearCoast(lat, lng);
        
        if (!coastalCheck.isValid) {
            Swal.fire({
                title: 'Invalid Location',
                html: `This location is too far from the coast.<br>
                      Please select a location within ${MAX_DISTANCE_FROM_COAST}km of the coastline.<br>
                      Current distance: ${coastalCheck.distance.toFixed(2)}km`,
                icon: 'error'
            });
            return false;
        }

        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);
        if (shouldZoom) {
            map.setView([lat, lng], 13);
        }

        // Update form inputs
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Reverse geocode to get location name
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                const locationName = data.display_name;
                document.getElementById('location').value = locationName;
            })
            .catch(error => {
                console.error('Error getting location name:', error);
            });

        return true;
    }

    // Add coastal boundaries visualization
    const coastalLine = L.polyline(coastalBoundaries.map(point => [point.lat, point.lng]), {
        color: '#0e346a',
        weight: 3,
        opacity: 0.7,
        dashArray: '5, 10'
    }).addTo(map);

    // Add buffer zone visualization
    coastalBoundaries.forEach(point => {
        L.circle([point.lat, point.lng], {
            radius: MAX_DISTANCE_FROM_COAST * 1000, // Convert km to meters
            color: '#0e346a',
            fillColor: '#0e346a',
            fillOpacity: 0.1,
            weight: 1
        }).addTo(map);
    });

    // Handle map clicks with validation
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        const coastalCheck = isNearCoast(lat, lng);
        if (!coastalCheck.isValid) {
            Swal.fire({
                title: 'Invalid Location',
                html: `This location is too far from the coast.<br>
                      Please select a location within ${MAX_DISTANCE_FROM_COAST}km of the coastline.<br>
                      Current distance: ${coastalCheck.distance.toFixed(2)}km`,
                icon: 'error'
            });
            return;
        }
        
        updateMarkerPosition(lat, lng);
    });

    // Update location validation in the existing click handler
    document.getElementById('useLocationBtn').addEventListener('click', function() {
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
                
                if (updateMarkerPosition(lat, lng)) {
                    // Show success message only if location is valid
                    Swal.fire({
                        title: 'Success',
                        text: 'Your location has been found',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
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

    // Update location validation button handler
    document.getElementById('validateLocationBtn').addEventListener('click', function() {
        const locationInput = document.getElementById('location').value;
        if (!locationInput) {
            Swal.fire({
                title: 'Error',
                text: 'Please enter a location to validate',
                icon: 'error'
            });
            return;
        }

        // Show loading state
        const button = this;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-arrow-repeat"></i> Validating...';

        // Use Nominatim to geocode the location
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationInput)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const location = data[0];
                    if (updateMarkerPosition(location.lat, location.lon)) {
                        // Show success message only if location is valid
                        Swal.fire({
                            title: 'Success',
                            text: 'Location validated successfully',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    throw new Error('Location not found');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Could not find the specified location',
                    icon: 'error'
                });
            })
            .finally(() => {
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            });
    });

    // Media Upload and Camera Functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const mediaContainer = document.getElementById('mediaContainer');
    const useCameraBtn = document.getElementById('useCameraBtn');
    const captureModal = new bootstrap.Modal(document.getElementById('captureModal'));
    const videoPreview = document.getElementById('capturePreview');
    const captureCanvas = document.createElement('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const photoModeBtn = document.getElementById('photoModeBtn');
    const videoModeBtn = document.getElementById('videoModeBtn');

    let mediaRecorder;
    let recordedChunks = [];
    let isRecording = false;
    let currentMode = 'photo';
    let stream;

    // File Upload Handling
    uploadArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFiles);

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles({ target: { files } });
    });

    function handleFiles(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                Swal.fire({
                    title: 'Error',
                    text: 'File size should not exceed 10MB',
                    icon: 'error'
                });
                return;
            }

            if (!file.type.match('image.*') && !file.type.match('video.*')) {
                Swal.fire({
                    title: 'Error',
                    text: 'Only image and video files are allowed',
                    icon: 'error'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => createPreviewItem(e.target.result, file.type);
            reader.readAsDataURL(file);
        });
    }

    function createPreviewItem(src, type) {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-3';
        
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';

        const media = type.startsWith('image/') ? 
            document.createElement('img') : 
            document.createElement('video');

        media.src = src;
        if (type.startsWith('video/')) {
            media.controls = true;
        }
        previewItem.appendChild(media);

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = () => col.remove();

        previewItem.appendChild(removeBtn);
        col.appendChild(previewItem);
        mediaContainer.appendChild(col);
    }

    // Camera Functionality
    useCameraBtn.addEventListener('click', async () => {
        try {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' }
                },
                audio: currentMode === 'video'
            });

            videoPreview.srcObject = stream;
            await videoPreview.play();
            captureModal.show();
        } catch (err) {
            Swal.fire({
                title: 'Error',
                text: 'Could not access camera: ' + err.message,
                icon: 'error'
            });
        }
    });

    // Photo/Video Mode Toggle
    photoModeBtn.addEventListener('click', () => switchMode('photo'));
    videoModeBtn.addEventListener('click', () => switchMode('video'));

    function switchMode(mode) {
        currentMode = mode;
        photoModeBtn.classList.toggle('active', mode === 'photo');
        videoModeBtn.classList.toggle('active', mode === 'video');
        captureBtn.innerHTML = mode === 'photo' ? 
            '<i class="bi bi-camera"></i> Capture' : 
            '<i class="bi bi-record-circle"></i> Record';

        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            initCamera();
        }
    }

    // Capture Button Handler
    captureBtn.addEventListener('click', () => {
        if (currentMode === 'photo') {
            takePhoto();
        } else {
            if (!isRecording) {
                startRecording();
            } else {
                stopRecording();
            }
        }
    });

    function takePhoto() {
        captureCanvas.width = videoPreview.videoWidth;
        captureCanvas.height = videoPreview.videoHeight;
        captureCanvas.getContext('2d').drawImage(videoPreview, 0, 0);
        
        captureCanvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            createPreviewItem(url, 'image/jpeg');
            captureModal.hide();
        }, 'image/jpeg');
    }

    function startRecording() {
        recordedChunks = [];
        mediaRecorder = new MediaRecorder(stream);
        
        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) {
                recordedChunks.push(e.data);
            }
        };

        mediaRecorder.onstop = () => {
            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            const url = URL.createObjectURL(blob);
            createPreviewItem(url, 'video/webm');
            captureModal.hide();
        };

        mediaRecorder.start();
        isRecording = true;
        captureBtn.innerHTML = '<i class="bi bi-stop-circle"></i> Stop';
    }

    function stopRecording() {
        mediaRecorder.stop();
        isRecording = false;
        captureBtn.innerHTML = '<i class="bi bi-record-circle"></i> Record';
    }

    // Cleanup on modal close
    document.getElementById('captureModal').addEventListener('hidden.bs.modal', () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        if (isRecording) {
            stopRecording();
        }
    });

    // Form submission with validation
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate location before submission
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;

        if (!lat || !lng) {
            Swal.fire({
                title: 'Invalid Location',
                text: 'Please select a valid location on the map',
                icon: 'error'
            });
            return;
        }

        const coastalCheck = isNearCoast(lat, lng);
        if (!coastalCheck.isValid) {
            Swal.fire({
                title: 'Invalid Location',
                html: `This location is too far from the coast.<br>
                      Please select a location within ${MAX_DISTANCE_FROM_COAST}km of the coastline.<br>
                      Current distance: ${coastalCheck.distance.toFixed(2)}km`,
                icon: 'error'
            });
            return;
        }
        
        const formData = new FormData(this);
        
        // Remove any disabled inputs from the form data
        const disabledInputs = document.querySelectorAll('input[disabled]');
        disabledInputs.forEach(input => {
            formData.delete(input.name);
        });
        
        // Add media files
        const mediaItems = mediaContainer.querySelectorAll('img, video');
        mediaItems.forEach((media, index) => {
            fetch(media.src)
                .then(res => res.blob())
                .then(blob => {
                    const ext = media.tagName === 'IMG' ? 'jpg' : 'webm';
                    formData.append('media[]', blob, `file${index}.${ext}`);
                    console.log('Added media file to form data:', `file${index}.${ext}`);
                })
                .catch(error => {
                    console.error('Error adding media file to form data:', error);
                });
        });

        // Log form data contents
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        // Submit the form
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'An error occurred',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'An error occurred while submitting the form',
                icon: 'error'
            });
        });
    });
});
</script>
@endpush
@endsection