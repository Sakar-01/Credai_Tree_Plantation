@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tree Details: {{ $tree->tree_id }}</h4>
                    <div>
                        @if(auth()->user()->isAdmin() || $tree->planted_by === auth()->id())
                            <a href="{{ route('trees.edit', $tree) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @endif
                        @if($tree->next_inspection_date && $tree->next_inspection_date <= now())
                            <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-sm btn-warning">Inspect Now</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            @if($tree->images && count($tree->images) > 0)
                                <div id="treeDetailCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($tree->images as $index => $image)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $image) }}" 
                                                     class="d-block w-100 rounded" 
                                                     style="height: 400px; object-fit: cover;" 
                                                     alt="Tree Photo {{ $index + 1 }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(count($tree->images) > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#treeDetailCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#treeDetailCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                        <!-- Image counter -->
                                        <div class="position-absolute bottom-0 start-0 m-3">
                                            <span class="badge bg-dark bg-opacity-75">
                                                <span id="currentImageIndex">1</span> / {{ count($tree->images) }}
                                            </span>
                                        </div>
                                        <!-- Indicators -->
                                        <div class="carousel-indicators">
                                            @foreach($tree->images as $index => $image)
                                                <button type="button" data-bs-target="#treeDetailCarousel" data-bs-slide-to="{{ $index }}" 
                                                        class="{{ $index == 0 ? 'active' : '' }}" 
                                                        aria-current="{{ $index == 0 ? 'true' : 'false' }}" 
                                                        aria-label="Slide {{ $index + 1 }}"></button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @elseif($tree->photo_path)
                                <img src="{{ asset('storage/' . $tree->photo_path) }}" class="img-fluid rounded" alt="Tree Photo">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>Species:</th>
                                    <td>{{ $tree->species }}</td>
                                </tr>
                                @if($tree->height)
                                <tr>
                                    <th>Plant Height:</th>
                                    <td>{{ $tree->height }} cm</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $tree->location_description }}</td>
                                </tr>
                                @if($tree->landmark)
                                <tr>
                                    <th>Landmark:</th>
                                    <td>{{ $tree->landmark }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Coordinates:</th>
                                    <td>{{ $tree->latitude }}, {{ $tree->longitude }}</td>
                                </tr>
                                <tr>
                                    <th>Plantation Date:</th>
                                    <td>{{ $tree->plantation_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Next Inspection:</th>
                                    <td>
                                        @if($tree->next_inspection_date)
                                            {{ $tree->next_inspection_date->format('M d, Y') }}
                                        @else
                                            <em class="text-muted">Not scheduled</em>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Planted By:</th>
                                    <td>{{ $tree->plantedBy->name }}</td>
                                </tr>
                            </table>

                            @if($tree->description)
                                <div class="mt-3">
                                    <h6>Description:</h6>
                                    <p>{{ $tree->description }}</p>
                                </div>
                            @endif

                            @if($tree->plantation_survey_file)
                                <div class="mt-3">
                                    <a href="{{ asset('storage/' . $tree->plantation_survey_file) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                        View Image
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection History -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Inspection History</h5>
                </div>
                <div class="card-body">
                    @if($tree->inspections->count() > 0)
                        <div class="timeline">
                            @foreach($tree->inspections->sortByDesc('inspection_date') as $inspection)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                @if($inspection->photo_path)
                                                    <img src="{{ asset('storage/' . $inspection->photo_path) }}" class="img-fluid rounded" alt="Inspection Photo">
                                                @endif
                                            </div>
                                            <div class="col-md-9">
                                                <h6>{{ $inspection->inspection_date->format('M d, Y') }}</h6>
                                                <p><strong>Health:</strong> 
                                                    <span class="badge bg-{{ $inspection->tree_health === 'good' ? 'success' : ($inspection->tree_health === 'average' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($inspection->tree_health) }}
                                                    </span>
                                                </p>
                                                @if($inspection->tree_height_cm)
                                                    <p><strong>Height:</strong> {{ $inspection->tree_height_cm }} cm</p>
                                                @endif
                                                @if($inspection->observation_notes)
                                                    <p><strong>Notes:</strong> {{ $inspection->observation_notes }}</p>
                                                @endif
                                                <small class="text-muted">Inspected by: {{ $inspection->inspectedBy->name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No inspections recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Interactive Map -->
            <div class="card">
                <div class="card-header">
                    <h6>Location Map</h6>
                </div>
                <div class="card-body p-0">
                    <div id="treeMap" style="height: 300px; width: 100%;"></div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        📍 {{ $tree->latitude }}, {{ $tree->longitude }}
                    </small>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="getDirections()">
                            <i class="fas fa-directions"></i> Get Directions
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="openInGoogleMaps()">
                            <i class="fas fa-external-link-alt"></i> Open in Google Maps
                        </button>
                        @if($tree->next_inspection_date && $tree->next_inspection_date <= now())
                            <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-warning">Inspect Now</a>
                        @endif
                        <a href="{{ route('trees.index') }}" class="btn btn-outline-secondary">Back to Trees</a>
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('trees.destroy', $tree) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this tree?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Delete Tree</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let treeMap;

// Ensure function is globally available
window.initTreeMap = function initTreeMap() {
    const treeLocation = { 
        lat: {{ $tree->latitude }}, 
        lng: {{ $tree->longitude }} 
    };
    
    treeMap = new google.maps.Map(document.getElementById('treeMap'), {
        zoom: 15,
        center: treeLocation,
        mapTypeId: 'hybrid'
    });

    const marker = new google.maps.Marker({
        position: treeLocation,
        map: treeMap,
        title: '{{ $tree->tree_id }} - {{ $tree->species }}',
        icon: {
      text: '🌳',
      fontSize: '24px',
      fontFamily: 'Arial',
      color: '#28a745'
  }
    });

    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="min-width: 200px;">
                <h6><strong>{{ $tree->tree_id }}</strong></h6>
                <p><strong>Species:</strong> {{ $tree->species }}<br>
                   <strong>Location:</strong> {{ $tree->location_description }}<br>
                   <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $tree->status)) }}<br>
                   <strong>Planted:</strong> {{ $tree->plantation_date->format('M d, Y') }}
                </p>
            </div>
        `
    });

    marker.addListener('click', () => {
        infoWindow.open(treeMap, marker);
    });
}

function getTreeStatusColor(status) {
    const colors = {
        'healthy': '#28a745',
        'needs_attention': '#dc3545',
        'under_inspection': '#ffc107',
        'planted': '#6c757d',
        'dead': '#000000'
    };
    return colors[status] || '#6c757d';
}

// Navigation functions
function openInGoogleMaps() {
    const lat = {{ $tree->latitude }};
    const lng = {{ $tree->longitude }};
    const treeId = '{{ $tree->tree_id }}';
    const species = '{{ $tree->species }}';
    
    // Create Google Maps URL with the tree location
    const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}&query_place_id=${treeId}`;
    window.open(url, '_blank');
}

function getDirections() {
    const lat = {{ $tree->latitude }};
    const lng = {{ $tree->longitude }};
    const treeId = '{{ $tree->tree_id }}';
    
    if (navigator.geolocation) {
        // Show loading state
        const button = document.querySelector('button[onclick="getDirections()"]');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
        button.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Success - got user location
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                
                // Create Google Maps directions URL
                const directionsUrl = `https://www.google.com/maps/dir/${userLat},${userLng}/${lat},${lng}`;
                window.open(directionsUrl, '_blank');
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            },
            function(error) {
                // Error getting location - open maps without origin
                console.warn('Could not get user location:', error.message);
                
                // Fallback: open Google Maps with destination only
                const mapsUrl = `https://www.google.com/maps/dir//${lat},${lng}`;
                window.open(mapsUrl, '_blank');
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
                
                // Show user-friendly message
                if (error.code === error.PERMISSION_DENIED) {
                    alert('Location access denied. Opening Google Maps - you can set your starting location manually.');
                } else {
                    alert('Could not detect your location. Opening Google Maps - you can set your starting location manually.');
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    } else {
        // Geolocation not supported
        alert('Geolocation is not supported by your browser. Opening Google Maps - you can set your starting location manually.');
        const mapsUrl = `https://www.google.com/maps/dir//${lat},${lng}`;
        window.open(mapsUrl, '_blank');
    }
}

// Update image counter for tree detail carousel
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('treeDetailCarousel');
    const imageCounter = document.getElementById('currentImageIndex');
    
    if (carousel && imageCounter) {
        carousel.addEventListener('slid.bs.carousel', function (e) {
            const activeIndex = e.to + 1;
            imageCounter.textContent = activeIndex;
        });
    }
});
</script>

<script>
// Global callback function that Google Maps can always find - MUST be defined before loading Google Maps
window.initGoogleMaps = function() {
    // Add a small delay to ensure all scripts have loaded
    setTimeout(function() {
        if (typeof initTreeMap === 'function') {
            try {
                initTreeMap();
            } catch (error) {
                console.error('Error initializing tree map:', error);
            }
        } else {
            console.warn('initTreeMap function not found, retrying...');
            // Retry once after a short delay
            setTimeout(function() {
                if (typeof initTreeMap === 'function') {
                    try {
                        initTreeMap();
                    } catch (error) {
                        console.error('Error initializing tree map on retry:', error);
                    }
                } else {
                    console.error('initTreeMap function still not found after retry');
                }
            }, 100);
        }
    }, 50);
};
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initGoogleMaps"></script>
@endsection