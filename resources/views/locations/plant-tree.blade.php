@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Plant New Tree in {{ $location->name }}</h4>
                        <a href="{{ route('trees.location', $location->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye"></i> View Location
                        </a>
                    </div>
                    <small class="text-muted">{{ $location->description }}</small>
                </div>
                <div class="card-body">
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('locations.plant-tree.store', $location->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="species" class="form-label">Tree Species (Optional)</label>
                                <input type="text" class="form-control @error('species') is-invalid @enderror" 
                                       id="species" name="species" value="{{ old('species') }}"
                                       placeholder="e.g., Mango, Neem, Peepal">
                                @error('species')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="height" class="form-label">Plant Height (cm)</label>
                                <input type="number" step="0.1" class="form-control @error('height') is-invalid @enderror" 
                                       id="height" name="height" value="{{ old('height') }}" 
                                       placeholder="e.g., 50, 120, 200" min="0" max="99999">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Exact Planting Location *</label>
                            <div id="map" style="height: 250px; width: 100%; border-radius: 8px;"></div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" 
                                           placeholder="Latitude" required readonly>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" 
                                           placeholder="Longitude" required readonly>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="form-text text-muted">Click on the map to select the exact tree planting spot</small>
                        </div>

                        <div class="mb-3">
                            <label for="landmark" class="form-label">Nearby Landmark</label>
                            <input type="text" class="form-control @error('landmark') is-invalid @enderror" 
                                   id="landmark" name="landmark" value="{{ old('landmark') }}" 
                                   placeholder="e.g., Near the main gate, Behind the bench">
                            @if($location->landmarks->count() > 0)
                                <small class="form-text text-muted">
                                    Existing landmarks: 
                                    @foreach($location->landmarks as $landmark)
                                        <span class="badge bg-light text-dark">{{ $landmark->name }}</span>
                                    @endforeach
                                </small>
                            @endif
                            @error('landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="plantation_date" class="form-label">Plantation Date *</label>
                                <input type="date" class="form-control @error('plantation_date') is-invalid @enderror" 
                                       id="plantation_date" name="plantation_date" value="{{ old('plantation_date', date('Y-m-d')) }}" required>
                                @error('plantation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="next_inspection_date" class="form-label">Next Inspection Date *</label>
                                <input type="date" class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                       id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date') }}" required>
                                @error('next_inspection_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Tree Images *</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="selectFromGallery()">
                                        <i class="fas fa-folder-open"></i> Choose from Gallery
                                    </button>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-outline-success w-100" onclick="captureFromCamera()">
                                        <i class="fas fa-camera"></i> Take Photo
                                    </button>
                                </div>
                            </div>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror d-none" 
                                   id="images" name="images[]" accept="image/*" multiple required>
                            <input type="file" class="d-none" 
                                   id="camera_input" accept="image/*" capture="environment" capture="camera">
                            <small class="form-text text-muted">Upload multiple images of the planted tree (JPEG, PNG, JPG - Max 5MB each)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="image_preview" class="mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Image Preview:</label>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllImages()">
                                    <i class="fas fa-times"></i> Clear All Images
                                </button>
                            </div>
                            <div id="preview_container" class="row"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Additional notes about the tree...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('trees.location', $location->id) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-seedling"></i> Plant Tree
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global callback function that Google Maps can always find - MUST be defined before loading Google Maps
window.initGoogleMaps = function() {
    if (typeof initMap === 'function') {
        initMap();
    } else {
        console.warn('initMap function not found');
    }
};
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initGoogleMaps"></script>

<script>
let map;
let marker;

// Ensure function is globally available
window.initMap = function initMap() {
    const locationCenter = { lat: {{ $location->latitude }}, lng: {{ $location->longitude }} };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 18,
        center: locationCenter,
    });

    // Show location boundary circle
    const locationCircle = new google.maps.Circle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.1,
        map: map,
        center: locationCenter,
        radius: 50 // 50 meter radius
    });

    // Initialize marker at current coordinates
    const currentCoords = {
        lat: parseFloat(document.getElementById('latitude').value),
        lng: parseFloat(document.getElementById('longitude').value)
    };
    
    marker = new google.maps.Marker({
        position: currentCoords,
        map: map,
        draggable: true,
        title: 'Tree Planting Location',
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="%23DC3545"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
            scaledSize: new google.maps.Size(32, 32),
            anchor: new google.maps.Point(16, 32)
        }
    });

    // Add click listener to map
    map.addListener('click', function(e) {
        placeMarker(e.latLng);
    });

    // Add drag listener to marker
    marker.addListener('dragend', function() {
        updateCoordinates(marker.getPosition().lat(), marker.getPosition().lng());
    });
}

function placeMarker(location) {
    marker.setPosition(location);
    updateCoordinates(location.lat(), location.lng());
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
}

// Set default next inspection date to 30 days from plantation date
document.getElementById('plantation_date').addEventListener('change', function() {
    const plantationDate = new Date(this.value);
    const nextInspectionDate = new Date(plantationDate);
    nextInspectionDate.setDate(nextInspectionDate.getDate() + 30);
    
    document.getElementById('next_inspection_date').value = nextInspectionDate.toISOString().split('T')[0];
});

// Trigger the change event on page load to set initial inspection date
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('plantation_date').dispatchEvent(new Event('change'));
});

// Store selected files globally for manipulation
let selectedFiles = [];

// Image selection functions
function selectFromGallery() {
    document.getElementById('images').click();
}

function captureFromCamera() {
    const cameraInput = document.getElementById('camera_input');
    
    // Enhanced Android compatibility
    if (navigator.userAgent.match(/Android/i)) {
        // For Android, try multiple approaches
        cameraInput.setAttribute('capture', 'camera');
        cameraInput.setAttribute('accept', 'image/*;capture=camera');
        
        // Some Android browsers respond better to this
        setTimeout(() => {
            cameraInput.click();
        }, 100);
    } else {
        // iOS and other browsers
        cameraInput.click();
    }
}

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    addFilesToSelection(files);
});

document.getElementById('camera_input').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    addFilesToSelection(files);
});

function addFilesToSelection(newFiles) {
    selectedFiles = [...selectedFiles, ...newFiles];
    updateImagePreview();
}

function updateImagePreview() {
    const previewContainer = document.getElementById('preview_container');
    const imagePreview = document.getElementById('image_preview');
    
    previewContainer.innerHTML = '';
    
    if (selectedFiles.length > 0) {
        imagePreview.style.display = 'block';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                onclick="removeImage(${index})" style="border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-times" style="font-size: 14px; color: white;"></i>
                        </button>
                        <small class="d-block text-center mt-1 text-muted">${file.name}</small>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        imagePreview.style.display = 'none';
    }
    
    // Update the file input
    updateFileInput();
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    updateImagePreview();
}

function clearAllImages() {
    selectedFiles = [];
    updateImagePreview();
}

function updateFileInput() {
    const fileInput = document.getElementById('images');
    const dt = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    fileInput.files = dt.files;
}
</script>
@endsection