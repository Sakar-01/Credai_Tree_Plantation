@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Plant New Tree</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('trees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="species" class="form-label">Tree Species *</label>
                                <input type="text" class="form-control @error('species') is-invalid @enderror" 
                                       id="species" name="species" value="{{ old('species') }}" required>
                                @error('species')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="plantation_date" class="form-label">Plantation Date *</label>
                                <input type="date" class="form-control @error('plantation_date') is-invalid @enderror" 
                                       id="plantation_date" name="plantation_date" value="{{ old('plantation_date', date('Y-m-d')) }}" required>
                                @error('plantation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location_description" class="form-label">Location Description *</label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('location_description') is-invalid @enderror" 
                                       id="location_description" name="location_description" value="{{ old('location_description') }}" 
                                       placeholder="Start typing location name..." required autocomplete="off">
                                <div id="location_suggestions" class="dropdown-menu w-100" style="display: none;"></div>
                            </div>
                            @error('location_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="landmark" class="form-label">Nearby Landmark</label>
                            <input type="text" class="form-control @error('landmark') is-invalid @enderror" 
                                   id="landmark" name="landmark" value="{{ old('landmark') }}" 
                                   placeholder="e.g., Near Main Gate, Behind School Building">
                            @error('landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude *</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude *</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()">
                                Get Current Location
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Tree Photo *</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*" required>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="next_inspection_date" class="form-label">Next Inspection Date *</label>
                            <input type="date" class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                   id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date') }}" required>
                            @error('next_inspection_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <label for="plantation_survey_file" class="form-label">Plantation File</label>
                            <input type="file" class="form-control @error('plantation_survey_file') is-invalid @enderror" 
                                   id="plantation_survey_file" name="plantation_survey_file">
                            @error('plantation_survey_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('trees.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Plant Tree</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        }, function(error) {
            alert('Error getting location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Set default next inspection date to 30 days from plantation date
document.getElementById('plantation_date').addEventListener('change', function() {
    const plantationDate = new Date(this.value);
    const nextInspectionDate = new Date(plantationDate);
    nextInspectionDate.setDate(nextInspectionDate.getDate() + 30);
    
    document.getElementById('next_inspection_date').value = nextInspectionDate.toISOString().split('T')[0];
});

// Location autocomplete functionality
let debounceTimer;
const locationInput = document.getElementById('location_description');
const suggestionsContainer = document.getElementById('location_suggestions');

locationInput.addEventListener('input', function() {
    const query = this.value.trim();
    
    clearTimeout(debounceTimer);
    
    if (query.length < 2) {
        suggestionsContainer.style.display = 'none';
        return;
    }
    
    debounceTimer = setTimeout(() => {
        fetch(`/api/location-suggestions?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(suggestions => {
                showSuggestions(suggestions);
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                suggestionsContainer.style.display = 'none';
            });
    }, 300);
});

function showSuggestions(suggestions) {
    if (suggestions.length === 0) {
        suggestionsContainer.style.display = 'none';
        return;
    }
    
    suggestionsContainer.innerHTML = '';
    
    suggestions.forEach(suggestion => {
        const suggestionItem = document.createElement('a');
        suggestionItem.className = 'dropdown-item d-flex justify-content-between align-items-center';
        suggestionItem.href = '#';
        suggestionItem.innerHTML = `
            <span>${suggestion.location}</span>
            <small class="text-muted">${suggestion.tree_count} trees</small>
        `;
        
        suggestionItem.addEventListener('click', function(e) {
            e.preventDefault();
            locationInput.value = suggestion.location;
            suggestionsContainer.style.display = 'none';
        });
        
        suggestionsContainer.appendChild(suggestionItem);
    });
    
    suggestionsContainer.style.display = 'block';
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(event) {
    if (!locationInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
        suggestionsContainer.style.display = 'none';
    }
});

// Hide suggestions on escape key
locationInput.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        suggestionsContainer.style.display = 'none';
    }
});
</script>
@endsection