@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Add New Location</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('locations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Location Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Central Park, School Garden">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Location Description *</label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                       id="description" name="description" value="{{ old('description') }}" 
                                       placeholder="Search for locations in Jalgaon..." required autocomplete="off">
                                <div id="location_suggestions" class="dropdown-menu w-100" style="display: none;"></div>
                            </div>
                            <small class="form-text text-muted">Start typing to search for locations in Jalgaon</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location Coordinates *</label>
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
                            <small class="form-text text-muted">Click on the map to select the location</small>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()">
                                <i class="fas fa-location-arrow"></i> Use Current Location
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Location Images *</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple required>
                            <small class="form-text text-muted">Upload multiple images of the location (JPEG, PNG, JPG - Max 2MB each)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="image_preview" class="mb-3" style="display: none;">
                            <label class="form-label">Image Preview:</label>
                            <div id="preview_container" class="row"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('trees.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Location & Plant Tree</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"></script>

<script>
let map;
let marker;
let autocomplete;

function initMap() {
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
        title: 'Selected Location'
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

    // Initialize Places Autocomplete
    const input = document.getElementById('description');
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
        if (place.geometry && place.geometry.location) {
            const location = place.geometry.location;
            map.setCenter(location);
            map.setZoom(17);
            placeMarker(location);
            
            // Update name if not already filled
            if (!document.getElementById('name').value) {
                document.getElementById('name').value = place.name || place.formatted_address;
            }
        }
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
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const location = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            map.setCenter(location);
            map.setZoom(17);
            placeMarker(location);
        }, function(error) {
            alert('Error getting location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('preview_container');
    const imagePreview = document.getElementById('image_preview');
    
    previewContainer.innerHTML = '';
    
    if (files.length > 0) {
        imagePreview.style.display = 'block';
        
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        imagePreview.style.display = 'none';
    }
});
</script>
@endsection