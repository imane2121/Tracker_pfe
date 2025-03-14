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
                <h4 class="mb-0">Please Select Waste Type</h4>
            </div>
            <div class="card-body">
                <div class="wsf-buttons-container d-flex flex-wrap gap-2">
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
                        <button type="button" class="wsf-btn-option wsf-general-type" id="autreBtn">Other</button>
                        <div id="autreInputContainer" class="wsf-hidden">
                            <input type="text" name="customType" id="autreInput" 
                                class="form-control mt-2" placeholder="Enter waste type">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Location Details</h4>
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
                <h4 class="mb-0">Add Photos/Videos</h4>
            </div>
            <div class="card-body">
                <div class="media-upload-section mb-4">
                    <div class="upload-area" id="uploadArea">
                        <i class="bi bi-cloud-upload"></i>
                        <p>Drag & drop files here or click to select</p>
                        <small class="text-muted">Supported formats: Images and Videos (max 10MB)</small>
                        <input type="file" id="fileInput" accept="image/*,video/*" multiple class="d-none">
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
                <div class="modal fade" id="captureModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Capture Media</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="capture-container">
                                    <video id="capturePreview" autoplay playsinline></video>
                                    <canvas id="captureCanvas" class="d-none"></canvas>
                                </div>
                                <div class="capture-mode-toggle mt-3 text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary active" id="photoModeBtn">
                                            <i class="bi bi-camera"></i> Photo Mode
                                        </button>
                                        <button type="button" class="btn btn-primary" id="videoModeBtn">
                                            <i class="bi bi-camera-video"></i> Video Mode
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="captureBtn">
                                    <i class="bi bi-camera"></i> Capture
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Additional Details</h4>
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
        <div class="d-flex gap-3 justify-content-end w-100 align-items-center ">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
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
        width: 30px;
        height: 30px;
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
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('/assets/js/waste-signal-form.js') }}"></script>
@endpush
@endsection