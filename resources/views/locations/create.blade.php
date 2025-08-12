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
                    <!-- Duplicate Location Warning -->
                    <div id="duplicate_warning" class="alert alert-danger" style="display: none;">
                        <h6><i class="fas fa-ban"></i> Location Already Exists!</h6>
                        <p>This location already exists. You cannot create duplicate locations.</p>
                        <div id="existing_location_info"></div>
                        <a id="goto_existing_location" href="#" class="btn btn-sm btn-primary">
                            <i class="fas fa-map-marker-alt"></i> Go to Existing Location
                        </a>
                    </div>

                    <form id="locationForm" action="{{ route('locations.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateLocationForm(event)">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Location Name *</label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="e.g., Central Park, School Garden">
                                <div id="name_update_indicator" class="position-absolute top-50 end-0 translate-middle-y me-2" style="display: none;">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                            <small class="form-text text-muted">Auto-updates when you change the location description</small>
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
                                <div id="search_loading" class="position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                    <i class="fas fa-spinner fa-spin text-primary"></i>
                                </div>
                            </div>
                            <small class="form-text text-muted">Start typing to search for locations in Jalgaon - this will auto-update the name and coordinates</small>
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
                            <small class="form-text text-muted d-block mt-1">
                                Note: Your browser must allow location access and you may need HTTPS for this feature to work.
                            </small>
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

<script>
// Global callback function that Google Maps can always find - MUST be defined before loading Google Maps
window.initGoogleMaps = function() {
    // Add a small delay to ensure all scripts have loaded
    setTimeout(function() {
        if (typeof initMap === 'function') {
            try {
                initMap();
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        } else {
            console.warn('initMap function not found, retrying...');
            // Retry once after a short delay
            setTimeout(function() {
                if (typeof initMap === 'function') {
                    try {
                        initMap();
                    } catch (error) {
                        console.error('Error initializing map on retry:', error);
                    }
                } else {
                    console.error('initMap function still not found after retry');
                }
            }, 100);
        }
    }, 50);
};
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initGoogleMaps"></script>

<script>
let map;
let marker;
let autocomplete;

// Ensure function is globally available
window.initMap = function initMap() {
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
        console.log('Autocomplete place selected:', place);
        
        if (place.geometry && place.geometry.location) {
            updateLocationFromPlace(place);
        } else {
            console.warn('Selected place has no geometry:', place);
            // If autocomplete selection doesn't have geometry, try searching for it
            if (place.name) {
                searchForPlace(place.name);
            }
        }
    });

    // Handle manual input in description field
    const descriptionInput = document.getElementById('description');
    let searchTimeout;
    
    descriptionInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Only search if user has typed at least 3 characters
        if (query.length >= 3) {
            searchTimeout = setTimeout(function() {
                searchForPlace(query);
            }, 300); // Reduced to 300ms for faster response
        } else {
            // Hide loading indicator if query is too short
            document.getElementById('search_loading').style.display = 'none';
            // Clear location name and coordinates if query is too short
            if (query.length === 0) {
                document.getElementById('name').value = '';
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
                hideDuplicateWarning();
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
    
    // Check for duplicate locations
    checkForDuplicateLocation(lat, lng);
}

function searchForPlace(query) {
    if (!window.google || !window.google.maps) {
        console.warn('Google Maps not loaded yet');
        return;
    }

    // Show loading indicator
    document.getElementById('search_loading').style.display = 'block';
    console.log('Searching for place:', query);

    const service = new google.maps.places.PlacesService(map);
    
    // Try text search first
    const textSearchRequest = {
        query: query + ' Jalgaon Maharashtra', // Add location context
        bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(20.5, 75.0), // Southwest corner of Jalgaon region
            new google.maps.LatLng(21.5, 76.0)  // Northeast corner of Jalgaon region
        ),
        fields: ['name', 'formatted_address', 'geometry', 'place_id', 'vicinity']
    };
    
    service.textSearch(textSearchRequest, function(results, status) {
        console.log('Text search status:', status, 'Results:', results);
        
        if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
            const place = results[0];
            updateLocationFromPlace(place);
        } else {
            // If text search fails, try nearby search as fallback
            console.log('Text search failed, trying nearby search...');
            tryNearbySearch(query);
        }
    });
}

function tryNearbySearch(query) {
    const service = new google.maps.places.PlacesService(map);
    
    // Use Jalgaon center for nearby search
    const jalgaonCenter = new google.maps.LatLng(21.0077, 75.5626);
    
    const nearbyRequest = {
        location: jalgaonCenter,
        radius: 50000, // 50km radius
        keyword: query,
        fields: ['name', 'formatted_address', 'geometry', 'place_id', 'vicinity']
    };
    
    service.nearbySearch(nearbyRequest, function(results, status) {
        // Hide loading indicator
        document.getElementById('search_loading').style.display = 'none';
        
        console.log('Nearby search status:', status, 'Results:', results);
        
        if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
            const place = results[0];
            updateLocationFromPlace(place);
        } else {
            console.log('Both text and nearby search failed for:', query);
            // Clear fields if no results found
            document.getElementById('name').value = query; // At least use the query as name
        }
    });
}

function updateLocationFromPlace(place) {
    // Hide loading indicator
    document.getElementById('search_loading').style.display = 'none';
    
    console.log('Updating location from place:', place);
    
    if (place.geometry && place.geometry.location) {
        const location = place.geometry.location;
        
        // Update map
        map.setCenter(location);
        map.setZoom(17);
        placeMarker(location);
        
        // Update location name with priority: name > vicinity > formatted_address
        const placeName = place.name || place.vicinity || place.formatted_address || 'Unknown Location';
        console.log('Setting location name to:', placeName);
        
        const nameField = document.getElementById('name');
        const nameIndicator = document.getElementById('name_update_indicator');
        
        nameField.value = placeName;
        
        // Show success indicator
        nameIndicator.style.display = 'block';
        setTimeout(() => {
            nameIndicator.style.display = 'none';
        }, 2000); // Hide after 2 seconds
        
        // Update coordinates
        updateCoordinates(location.lat(), location.lng());
        
        console.log('Location updated successfully');
    } else {
        console.warn('Place has no geometry/location:', place);
    }
}

function checkForDuplicateLocation(lat, lng) {
    // Only check if we have valid coordinates
    if (!lat || !lng || lat === 0 || lng === 0) {
        return;
    }
    
    // Create a simple AJAX request to check for nearby locations
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const headers = {
        'Content-Type': 'application/json'
    };
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }
    
    fetch(`/api/locations/check-duplicate?lat=${lat}&lng=${lng}`, {
        method: 'GET',
        headers: headers,
    })
    .then(response => response.json())
    .then(data => {
        if (data.duplicate && data.location) {
            showDuplicateWarning(data.location);
        } else {
            hideDuplicateWarning();
        }
    })
    .catch(error => {
        console.warn('Could not check for duplicate locations:', error);
        hideDuplicateWarning();
    });
}

function showDuplicateWarning(location) {
    const warningDiv = document.getElementById('duplicate_warning');
    const infoDiv = document.getElementById('existing_location_info');
    const linkButton = document.getElementById('goto_existing_location');
    const form = document.getElementById('locationForm');
    
    // Update the warning content
    infoDiv.innerHTML = `
        <strong>${location.name}</strong><br>
        <small class="text-muted">
            ${location.description}<br>
            Coordinates: ${location.latitude}, ${location.longitude}
        </small>
    `;
    
    // Update the link to go to existing location
    linkButton.href = `/location/${location.id}/trees`;
    
    // Disable form submission
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-ban"></i> Location Already Exists';
        submitButton.classList.remove('btn-primary');
        submitButton.classList.add('btn-danger');
    }
    
    // Show the warning
    warningDiv.style.display = 'block';
    
    // Mark that we have a duplicate
    window.hasDuplicateLocation = true;
}

function hideDuplicateWarning() {
    const warningDiv = document.getElementById('duplicate_warning');
    const form = document.getElementById('locationForm');
    
    // Hide the warning
    warningDiv.style.display = 'none';
    
    // Re-enable form submission
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Add Location & Plant Tree';
        submitButton.classList.remove('btn-danger');
        submitButton.classList.add('btn-primary');
    }
    
    // Clear duplicate flag
    window.hasDuplicateLocation = false;
}


function validateLocationForm(event) {
    // Check if we have a duplicate location
    if (window.hasDuplicateLocation) {
        event.preventDefault();
        alert('Cannot create location - this location already exists! Please go to the existing location or select different coordinates.');
        return false;
    }
    return true;
}

function getCurrentLocation() {
    const button = document.querySelector('button[onclick="getCurrentLocation()"]');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
    button.disabled = true;
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Success callback
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const location = new google.maps.LatLng(lat, lng);
                
                // Update map
                map.setCenter(location);
                map.setZoom(17);
                placeMarker(location);
                
                // Ensure coordinates are displayed
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                
                // Reset button
                button.innerHTML = '<i class="fas fa-check"></i> Location detected!';
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
            },
            function(error) {
                // Error callback
                let errorMessage = 'Error getting location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Location access denied by user. Please enable location permissions and try again.';
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
                
                // Reset button
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