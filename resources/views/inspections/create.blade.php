@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Inspect Tree: {{ $tree->tree_id }}</h4>
                    <small class="text-muted">{{ $tree->species }} - {{ $tree->location_description }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tree_id" value="{{ $tree->id }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inspection_date" class="form-label">Inspection Date *</label>
                                <input type="date" class="form-control @error('inspection_date') is-invalid @enderror" 
                                       id="inspection_date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}" required>
                                @error('inspection_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tree_health" class="form-label">Tree Health *</label>
                                <select class="form-select @error('tree_health') is-invalid @enderror" 
                                        id="tree_health" name="tree_health" required>
                                    <option value="">Select Health Status</option>
                                    <option value="good" {{ old('tree_health') === 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="average" {{ old('tree_health') === 'average' ? 'selected' : '' }}>Average</option>
                                    <option value="poor" {{ old('tree_health') === 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                @error('tree_health')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude *</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude', $tree->latitude) }}" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude *</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude', $tree->longitude) }}" required>
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
                            <label for="photo" class="form-label">Current Tree Photo *</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*" required>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tree_height_cm" class="form-label">Tree Height (cm)</label>
                            <input type="number" class="form-control @error('tree_height_cm') is-invalid @enderror" 
                                   id="tree_height_cm" name="tree_height_cm" value="{{ old('tree_height_cm') }}" min="1">
                            @error('tree_height_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="observation_notes" class="form-label">Observation Notes</label>
                            <textarea class="form-control @error('observation_notes') is-invalid @enderror" 
                                      id="observation_notes" name="observation_notes" rows="4" 
                                      placeholder="Record any observations about the tree's condition, growth, or surroundings...">{{ old('observation_notes') }}</textarea>
                            @error('observation_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="next_inspection_date" class="form-label">Next Inspection Date</label>
                            <input type="date" class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                   id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date') }}">
                            @error('next_inspection_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('trees.show', $tree) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Record Inspection</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Previous Tree Photo for Reference -->
            @if($tree->photo_path || $tree->inspections->isNotEmpty())
                <div class="card mt-4">
                    <div class="card-header">
                        <h6>Reference Photos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($tree->photo_path)
                                <div class="col-md-6">
                                    <h6>Original Plantation Photo</h6>
                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" class="img-fluid rounded" alt="Original Tree Photo">
                                    <small class="text-muted d-block mt-1">{{ $tree->plantation_date->format('M d, Y') }}</small>
                                </div>
                            @endif
                            @if($tree->inspections->isNotEmpty())
                                <div class="col-md-6">
                                    <h6>Latest Inspection Photo</h6>
                                    @php $latestInspection = $tree->inspections->sortByDesc('inspection_date')->first(); @endphp
                                    <img src="{{ asset('storage/' . $latestInspection->photo_path) }}" class="img-fluid rounded" alt="Latest Inspection Photo">
                                    <small class="text-muted d-block mt-1">{{ $latestInspection->inspection_date->format('M d, Y') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
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

// Set default next inspection date based on tree health
document.getElementById('tree_health').addEventListener('change', function() {
    const today = new Date();
    const nextInspectionDate = new Date(today);
    
    switch(this.value) {
        case 'good':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 60); // 2 months
            break;
        case 'average':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 30); // 1 month
            break;
        case 'poor':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 14); // 2 weeks
            break;
    }
    
    document.getElementById('next_inspection_date').value = nextInspectionDate.toISOString().split('T')[0];
});
</script>
@endsection