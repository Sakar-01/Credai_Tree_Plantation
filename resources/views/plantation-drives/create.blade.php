@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Create Plantation Drive</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('plantation-drives.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Drive Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location_name" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location_name') is-invalid @enderror" 
                                       id="location_name" name="location_name" value="{{ old('location_name') }}" 
                                       placeholder="Type location name..." autocomplete="off" required>
                                <div id="locationSuggestions" class="dropdown-menu" style="display: none; width: 100%;"></div>
                                <div class="form-text">Type to search existing locations or enter a new location name.</div>
                                @error('location_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="landmark" class="form-label">Landmark (Optional)</label>
                                <input type="text" class="form-control @error('landmark') is-invalid @enderror" 
                                       id="landmark" name="landmark" value="{{ old('landmark') }}" 
                                       placeholder="e.g., Near School, Community Center">
                                @error('landmark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="number_of_trees" class="form-label">Number of Trees</label>
                            <input type="number" class="form-control @error('number_of_trees') is-invalid @enderror" 
                                   id="number_of_trees" name="number_of_trees" value="{{ old('number_of_trees') }}" min="1" required>
                            <div class="form-text">Individual tree species can be specified when editing each tree.</div>
                            @error('number_of_trees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Drive Photos</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*" required>
                            <div class="form-text">You can select multiple images. Max 2MB per image.</div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="plantation_date" class="form-label">Plantation Date</label>
                                <input type="date" class="form-control @error('plantation_date') is-invalid @enderror" 
                                       id="plantation_date" name="plantation_date" value="{{ old('plantation_date') }}" required>
                                @error('plantation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="next_inspection_date" class="form-label">Next Inspection Date</label>
                                <input type="date" class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                       id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date') }}" required>
                                @error('next_inspection_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude (Optional)</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="e.g., 26.9124">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude (Optional)</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="e.g., 75.7873">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" id="getCurrentLocation">
                                <i class="fas fa-crosshairs"></i> Get My Current Location
                            </button>
                            <small class="text-muted d-block mt-1">This will use your device's GPS to get current coordinates.</small>
                        </div>

                        <div class="mb-3">
                            <label for="plantation_survey_file" class="form-label">Survey File (Optional)</label>
                            <input type="file" class="form-control @error('plantation_survey_file') is-invalid @enderror" 
                                   id="plantation_survey_file" name="plantation_survey_file">
                            <div class="form-text">Upload any survey or documentation file. Max 5MB.</div>
                            @error('plantation_survey_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('plantation-drives.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Plantation Drive</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
    const locationInput = document.getElementById('location_name');
    const landmarkInput = document.getElementById('landmark');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const suggestionsDiv = document.getElementById('locationSuggestions');

    // Location data from server
    const locations = {!! json_encode($locations->map(function($location) {
        return [
            'id' => $location->id,
            'name' => $location->name,
            'landmark' => $location->landmark,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ];
    })->values()) !!};

    // Handle location input for autocomplete
    locationInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        if (query.length < 2) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        const matches = locations.filter(location => 
            location.name.toLowerCase().includes(query) ||
            (location.landmark && location.landmark.toLowerCase().includes(query))
        );

        if (matches.length > 0) {
            let html = '';
            matches.forEach(location => {
                html += `<a href="#" class="dropdown-item location-suggestion" data-location='${JSON.stringify(location)}'>
                    <strong>${location.name}</strong>
                    ${location.landmark ? '<br><small class="text-muted">' + location.landmark + '</small>' : ''}
                </a>`;
            });
            suggestionsDiv.innerHTML = html;
            suggestionsDiv.style.display = 'block';
        } else {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Handle clicking on suggestions
    document.addEventListener('click', function(e) {
        if (e.target.closest('.location-suggestion')) {
            e.preventDefault();
            const locationData = JSON.parse(e.target.closest('.location-suggestion').dataset.location);
            
            locationInput.value = locationData.name;
            if (locationData.landmark) {
                landmarkInput.value = locationData.landmark;
            }
            
            suggestionsDiv.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#location_name') && !e.target.closest('#locationSuggestions')) {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Get current location using GPS
    getCurrentLocationBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }

        // Show loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting Location...';
        this.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Success
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                latitudeInput.value = latitude.toFixed(8);
                longitudeInput.value = longitude.toFixed(8);
                
                // Add visual feedback
                latitudeInput.classList.add('border-success');
                longitudeInput.classList.add('border-success');
                
                setTimeout(() => {
                    latitudeInput.classList.remove('border-success');
                    longitudeInput.classList.remove('border-success');
                }, 3000);

                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
                successMsg.innerHTML = `
                    <i class="fas fa-check-circle"></i> Location obtained successfully! 
                    <small>(Accuracy: ${Math.round(accuracy)}m)</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                getCurrentLocationBtn.parentNode.appendChild(successMsg);

                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    if (successMsg.parentNode) {
                        successMsg.remove();
                    }
                }, 5000);

                // Reset button
                getCurrentLocationBtn.innerHTML = originalText;
                getCurrentLocationBtn.disabled = false;
            },
            function(error) {
                // Error
                let errorMessage = 'Unable to get your location. ';
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

                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show mt-2';
                errorDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle"></i> ${errorMessage}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                getCurrentLocationBtn.parentNode.appendChild(errorDiv);

                // Auto-dismiss after 7 seconds
                setTimeout(() => {
                    if (errorDiv.parentNode) {
                        errorDiv.remove();
                    }
                }, 7000);

                // Reset button
                getCurrentLocationBtn.innerHTML = originalText;
                getCurrentLocationBtn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    });
});
</script>
@endsection