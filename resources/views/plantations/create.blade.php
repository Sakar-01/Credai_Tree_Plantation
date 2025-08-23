@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-seedling"></i> Create Plantation Drive</h4>
                    <small class="text-muted">Plant multiple trees in one location</small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('plantations.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Location Description -->
                        <div class="mb-3">
                            <label for="location_description" class="form-label">Location Description <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control @error('location_description') is-invalid @enderror" 
                                       id="location_description" 
                                       name="location_description" 
                                       value="{{ old('location_description') }}" 
                                       placeholder="Search for locations in Jalgaon..." 
                                       autocomplete="off"
                                       required>
                                <div id="search_loading" class="position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                    <i class="fas fa-spinner fa-spin text-primary"></i>
                                </div>
                            </div>
                            <small class="form-text text-muted">Start typing to search for locations - this will auto-update coordinates and landmark</small>
                            @error('location_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Landmark -->
                        <div class="mb-3">
                            <label for="landmark" class="form-label">Nearest Landmark</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control @error('landmark') is-invalid @enderror" 
                                       id="landmark" 
                                       name="landmark" 
                                       value="{{ old('landmark') }}">
                                <div id="landmark_update_indicator" class="position-absolute top-50 end-0 translate-middle-y me-2" style="display: none;">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                            <small class="form-text text-muted">Auto-updates when you search for location</small>
                            @error('landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Interactive Map Section -->
                        <div class="mb-3">
                            <label class="form-label">Location Coordinates <span class="text-danger">*</span></label>
                            <div id="map" style="height: 300px; width: 100%; border-radius: 8px;"></div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude') }}" 
                                           placeholder="Latitude" required readonly>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude') }}" 
                                           placeholder="Longitude" required readonly>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="form-text text-muted">Click on the map to select the location or search above</small>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()">
                                <i class="fas fa-location-arrow"></i> Use Current Location
                            </button>
                            <small class="form-text text-muted d-block mt-1">
                                Click to use your current GPS location for the plantation drive.
                            </small>
                        </div>

                        <!-- Plantation Date and Next Inspection Date -->
                        <div class="row mb-3">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <label for="next_inspection_date" class="form-label">Next Inspection Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                       id="next_inspection_date" 
                                       name="next_inspection_date" 
                                       value="{{ old('next_inspection_date') }}" 
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('next_inspection_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Additional details about this plantation drive</div>
                        </div>

                        <!-- Multiple Images -->
                        <div class="mb-3">
                            <label for="images" class="form-label">Location Images</label>
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
                            <small class="form-text text-muted">Upload multiple images of the plantation location (optional - JPEG, PNG, JPG - Max 100MB each)</small>
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
                            <a href="{{ route('trees.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
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
window.initGoogleMapsPlantation = function() {
    setTimeout(function() {
        if (typeof initMapPlantation === 'function') {
            try {
                initMapPlantation();
            } catch (error) {
                console.error('Error initializing plantation map:', error);
            }
        } else {
            console.warn('initMapPlantation function not found, retrying...');
            setTimeout(function() {
                if (typeof initMapPlantation === 'function') {
                    try {
                        initMapPlantation();
                    } catch (error) {
                        console.error('Error initializing plantation map on retry:', error);
                    }
                } else {
                    console.error('initMapPlantation function still not found after retry');
                }
            }, 100);
        }
    }, 50);
};
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initGoogleMapsPlantation"></script>

<script>
let map;
let marker;
let autocomplete;

// Ensure function is globally available
window.initMapPlantation = function initMapPlantation() {
    // Default to Jalgaon coordinates
    const jalgaon = { lat: 21.0077, lng: 75.5626 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: jalgaon,
    });

    // Initialize marker
    marker = new google.maps.Marker({
        position: jalgaon,
        map: map,
        draggable: true,
        title: 'Plantation Location'
    });

    // Set initial coordinates
    document.getElementById('latitude').value = jalgaon.lat;
    document.getElementById('longitude').value = jalgaon.lng;

    // Add click listener to map
    map.addListener('click', function(e) {
        placeMarker(e.latLng);
    });

    // Add drag listener to marker
    marker.addListener('dragend', function() {
        updateCoordinates(marker.getPosition().lat(), marker.getPosition().lng());
    });

    // Initialize Places Autocomplete for location description
    const input = document.getElementById('location_description');
    autocomplete = new google.maps.places.Autocomplete(input, {
        bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(20.5, 75.0), // Southwest corner of Jalgaon region
            new google.maps.LatLng(21.5, 76.0)  // Northeast corner of Jalgaon region
        ),
        strictBounds: true,
        types: ['establishment', 'geocode']
    });

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        console.log('Autocomplete place selected:', place);
        
        if (place.geometry && place.geometry.location) {
            updateLocationFromPlace(place);
        } else {
            console.warn('Selected place has no geometry:', place);
            if (place.name) {
                searchForPlace(place.name);
            }
        }
    });

    // Handle manual input in description field
    const descriptionInput = document.getElementById('location_description');
    let searchTimeout;
    
    descriptionInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(function() {
                searchForPlace(query);
            }, 300);
        } else {
            document.getElementById('search_loading').style.display = 'none';
            if (query.length === 0) {
                document.getElementById('landmark').value = '';
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
            }
        }
    });

    // Set marker if old values exist
    @if(old('latitude') && old('longitude'))
        const oldLocation = new google.maps.LatLng({{ old('latitude') }}, {{ old('longitude') }});
        placeMarker(oldLocation);
    @endif
}

function placeMarker(location) {
    marker.setPosition(location);
    map.setCenter(location);
    map.setZoom(17);
    updateCoordinates(location.lat(), location.lng());
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
}

function searchForPlace(query) {
    if (!window.google || !window.google.maps) {
        console.warn('Google Maps not loaded yet');
        return;
    }

    document.getElementById('search_loading').style.display = 'block';
    console.log('Searching for place:', query);

    const service = new google.maps.places.PlacesService(map);
    
    const textSearchRequest = {
        query: query + ' Jalgaon Maharashtra',
        bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(20.5, 75.0),
            new google.maps.LatLng(21.5, 76.0)
        ),
        fields: ['name', 'formatted_address', 'geometry', 'place_id', 'vicinity']
    };
    
    service.textSearch(textSearchRequest, function(results, status) {
        console.log('Text search status:', status, 'Results:', results);
        
        if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
            const place = results[0];
            updateLocationFromPlace(place);
        } else {
            console.log('Text search failed, trying nearby search...');
            tryNearbySearch(query);
        }
    });
}

function tryNearbySearch(query) {
    const service = new google.maps.places.PlacesService(map);
    const jalgaonCenter = new google.maps.LatLng(21.0077, 75.5626);
    
    const nearbyRequest = {
        location: jalgaonCenter,
        radius: 50000,
        keyword: query,
        fields: ['name', 'formatted_address', 'geometry', 'place_id', 'vicinity']
    };
    
    service.nearbySearch(nearbyRequest, function(results, status) {
        document.getElementById('search_loading').style.display = 'none';
        
        console.log('Nearby search status:', status, 'Results:', results);
        
        if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
            const place = results[0];
            updateLocationFromPlace(place);
        } else {
            console.log('Both text and nearby search failed for:', query);
        }
    });
}

function updateLocationFromPlace(place) {
    document.getElementById('search_loading').style.display = 'none';
    
    console.log('Updating location from place:', place);
    
    if (place.geometry && place.geometry.location) {
        const location = place.geometry.location;
        
        // Update map
        map.setCenter(location);
        map.setZoom(17);
        placeMarker(location);
        
        // Auto-fill landmark field with place name
        const placeName = place.name || place.vicinity || place.formatted_address || '';
        if (placeName) {
            const landmarkField = document.getElementById('landmark');
            const landmarkIndicator = document.getElementById('landmark_update_indicator');
            
            landmarkField.value = placeName;
            
            // Show success indicator
            landmarkIndicator.style.display = 'block';
            setTimeout(() => {
                landmarkIndicator.style.display = 'none';
            }, 2000);
        }
        
        console.log('Location updated successfully');
    } else {
        console.warn('Place has no geometry/location:', place);
    }
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