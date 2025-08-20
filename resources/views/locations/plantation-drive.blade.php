@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-seedling"></i> Create Plantation Drive in {{ $location->name }}</h4>
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

                    <form method="POST" action="{{ route('locations.plantation-drive.store', $location->id) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Landmark -->
                        <div class="mb-3">
                            <label for="landmark" class="form-label">Nearest Landmark</label>
                            <input type="text" 
                                   class="form-control @error('landmark') is-invalid @enderror" 
                                   id="landmark" 
                                   name="landmark" 
                                   value="{{ old('landmark') }}"
                                   placeholder="e.g., Near the main gate, Behind the school building">
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

                        <!-- Exact Planting Location Map -->
                        <div class="mb-3">
                            <label class="form-label">Exact Plantation Area <span class="text-danger">*</span></label>
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
                            <small class="form-text text-muted">Click on the map to select the exact plantation area within this location</small>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()">
                                <i class="fas fa-location-arrow"></i> Use Current Location
                            </button>
                            <small class="form-text text-muted d-block mt-1">
                                Click to use your current GPS location for the plantation area.
                            </small>
                        </div>

                        <!-- Plantation Date -->
                        <div class="mb-3">
                            <label for="plantation_date" class="form-label">Plantation Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('plantation_date') is-invalid @enderror" 
                                   id="plantation_date" 
                                   name="plantation_date" 
                                   value="{{ old('plantation_date', date('Y-m-d')) }}" 
                                   required>
                            @error('plantation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Number of Trees -->
                        <div class="mb-3">
                            <label for="tree_count" class="form-label">Number of Trees <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('tree_count') is-invalid @enderror" 
                                   id="tree_count" 
                                   name="tree_count" 
                                   value="{{ old('tree_count', 1) }}" 
                                   min="1" 
                                   max="1000" 
                                   required>
                            @error('tree_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">How many trees will be planted in this drive? (1-1000)</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Additional details about this plantation drive...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Additional details about this plantation drive</div>
                        </div>

                        <!-- Multiple Images -->
                        <div class="mb-3">
                            <label for="images" class="form-label">Plantation Area Images</label>
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
                                   id="images" name="images[]" accept="image/*" multiple>
                            <input type="file" class="d-none" 
                                   id="camera_input" accept="image/*" capture="environment" capture="camera">
                            <small class="form-text text-muted">Upload multiple images of the plantation area (optional - JPEG, PNG, JPG - Max 100MB each)</small>
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

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('trees.location', $location->id) }}" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-seedling"></i> Create Plantation Drive
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global callback function that Google Maps can always find
window.initGoogleMapsPlantationLocation = function() {
    if (typeof initMapPlantationLocation === 'function') {
        initMapPlantationLocation();
    } else {
        console.warn('initMapPlantationLocation function not found');
    }
};
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initGoogleMapsPlantationLocation"></script>

<script>
let map;
let marker;

// Ensure function is globally available
window.initMapPlantationLocation = function initMapPlantationLocation() {
    const locationCenter = { lat: {{ $location->latitude }}, lng: {{ $location->longitude }} };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 18,
        center: locationCenter,
    });

    // Show location boundary circle
    const locationCircle = new google.maps.Circle({
        strokeColor: '#28a745',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#28a745',
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
        title: 'Plantation Area',
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="%2328a745"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
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

function getCurrentLocation() {
    const button = document.querySelector('button[onclick="getCurrentLocation()"]');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
    button.disabled = true;
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const location = new google.maps.LatLng(lat, lng);
                
                map.setCenter(location);
                map.setZoom(18);
                placeMarker(location);
                
                button.innerHTML = '<i class="fas fa-check"></i> Location detected!';
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
            },
            function(error) {
                let errorMessage = 'Error getting location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Location access denied by user.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred.';
                        break;
                }
                
                alert(errorMessage);
                
                button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed to get location';
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 3000);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

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