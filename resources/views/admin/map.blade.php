@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5>Tree Locations Map</h5>
                </div>
                <div class="card-body">
                    <!-- Map Legend -->
                    <div class="mb-3">
                        <h6>Legend</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background-color: #28a745; border-radius: 50%; margin-right: 10px;"></div>
                            <span>Healthy ({{ $stats['healthy_trees'] }})</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background-color: #ffc107; border-radius: 50%; margin-right: 10px;"></div>
                            <span>Under Inspection ({{ $stats['under_inspection'] }})</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background-color: #dc3545; border-radius: 50%; margin-right: 10px;"></div>
                            <span>Needs Attention ({{ $stats['needs_attention'] }})</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 20px; height: 20px; background-color: #6c757d; border-radius: 50%; margin-right: 10px;"></div>
                            <span>Planted ({{ $stats['planted'] }})</span>
                        </div>
                    </div>

                    <!-- Map Controls -->
                    <div class="mb-3">
                        <h6>Filters</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showHealthy" checked>
                            <label class="form-check-label" for="showHealthy">Show Healthy Trees</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showUnderInspection" checked>
                            <label class="form-check-label" for="showUnderInspection">Show Under Inspection</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showNeedsAttention" checked>
                            <label class="form-check-label" for="showNeedsAttention">Show Needs Attention</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showPlanted" checked>
                            <label class="form-check-label" for="showPlanted">Show Planted</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showHeatmap">
                            <label class="form-check-label" for="showHeatmap">Show Heatmap</label>
                        </div>
                    </div>

                    <!-- Map Actions -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="centerOnJalgaon()">Center on Jalgaon</button>
                        <button class="btn btn-secondary btn-sm" onclick="fitAllMarkers()">Fit All Trees</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6>Map Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border p-2 rounded mb-2">
                                <h5 class="mb-0">{{ $stats['total_trees'] }}</h5>
                                <small>Total Trees</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-2 rounded mb-2">
                                <h5 class="mb-0 text-success">{{ $stats['healthy_trees'] }}</h5>
                                <small>Healthy</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-2 rounded mb-2">
                                <h5 class="mb-0 text-danger">{{ $stats['needs_attention'] }}</h5>
                                <small>Need Attention</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-2 rounded">
                                <h5 class="mb-0 text-warning">{{ $stats['under_inspection'] }}</h5>
                                <small>Under Inspection</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body p-0">
                    <div id="map" style="height: 80vh; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let map;
let markers = [];
let heatmap;
let heatmapData = [];

// Tree data from server
const trees = @json($trees);

// Jalgaon, India coordinates
const JALGAON_CENTER = { lat: 21.0077, lng: 75.5626 };

function initMap() {
    // Initialize map centered on Jalgaon, India
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: JALGAON_CENTER,
        mapTypeId: 'hybrid'
    });

    // Create markers for all trees
    createTreeMarkers();
    
    // Initialize heatmap data
    initializeHeatmap();
    
    // Set up filter event listeners
    setupFilterListeners();
    
    // Fit map to show all markers if trees exist
    if (trees.length > 0) {
        fitAllMarkers();
    }
}

function createTreeMarkers() {
    trees.forEach(tree => {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(tree.latitude), lng: parseFloat(tree.longitude) },
            map: map,
            title: tree.tree_id + ' - ' + tree.species,
            icon: getMarkerIcon(tree.status),
            treeData: tree
        });

        // Create info window
        const infoWindow = new google.maps.InfoWindow({
            content: createInfoWindowContent(tree)
        });

        marker.addListener('click', () => {
            // Close all other info windows
            markers.forEach(m => {
                if (m.infoWindow) {
                    m.infoWindow.close();
                }
            });
            infoWindow.open(map, marker);
        });

        marker.infoWindow = infoWindow;
        markers.push(marker);
        
        // Add to heatmap data
        heatmapData.push(new google.maps.LatLng(parseFloat(tree.latitude), parseFloat(tree.longitude)));
    });
}

function getMarkerIcon(status) {
    const colors = {
        'healthy': '#28a745',
        'needs_attention': '#dc3545',
        'under_inspection': '#ffc107',
        'planted': '#6c757d',
        'dead': '#000000'
    };
    
    const color = colors[status] || '#6c757d';
    
    return {
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: color,
        fillOpacity: 0.8,
        strokeColor: '#ffffff',
        strokeWeight: 2,
        scale: 8
    };
}

function createInfoWindowContent(tree) {
    const statusBadgeClass = {
        'healthy': 'success',
        'needs_attention': 'danger',
        'under_inspection': 'warning',
        'planted': 'secondary',
        'dead': 'dark'
    };

    return `
        <div style="min-width: 250px;">
            <h6><strong>${tree.tree_id}</strong></h6>
            <p><strong>Species:</strong> ${tree.species}<br>
               <strong>Location:</strong> ${tree.location_description}<br>
               <strong>Planted:</strong> ${new Date(tree.plantation_date).toLocaleDateString()}<br>
               <strong>Planted by:</strong> ${tree.planted_by ? tree.planted_by.name : 'Unknown'}<br>
               <strong>Status:</strong> <span class="badge bg-${statusBadgeClass[tree.status] || 'secondary'}">${tree.status.replace('_', ' ').toUpperCase()}</span>
            </p>
            <div class="d-flex gap-2">
                <a href="/trees/${tree.id}" class="btn btn-sm btn-primary" target="_blank">View Details</a>
                <a href="/trees/${tree.id}/inspect" class="btn btn-sm btn-warning" target="_blank">Inspect</a>
            </div>
        </div>
    `;
}

function initializeHeatmap() {
    heatmap = new google.maps.visualization.HeatmapLayer({
        data: heatmapData,
        map: null, // Initially hidden
        radius: 20,
        opacity: 0.6
    });
}

function setupFilterListeners() {
    const filters = ['showHealthy', 'showUnderInspection', 'showNeedsAttention', 'showPlanted'];
    
    filters.forEach(filterId => {
        document.getElementById(filterId).addEventListener('change', filterMarkers);
    });
    
    document.getElementById('showHeatmap').addEventListener('change', function() {
        heatmap.setMap(this.checked ? map : null);
    });
}

function filterMarkers() {
    const showHealthy = document.getElementById('showHealthy').checked;
    const showUnderInspection = document.getElementById('showUnderInspection').checked;
    const showNeedsAttention = document.getElementById('showNeedsAttention').checked;
    const showPlanted = document.getElementById('showPlanted').checked;

    markers.forEach(marker => {
        const status = marker.treeData.status;
        let show = false;

        switch(status) {
            case 'healthy':
                show = showHealthy;
                break;
            case 'under_inspection':
                show = showUnderInspection;
                break;
            case 'needs_attention':
                show = showNeedsAttention;
                break;
            case 'planted':
                show = showPlanted;
                break;
        }

        marker.setVisible(show);
    });
}

function centerOnJalgaon() {
    map.setCenter(JALGAON_CENTER);
    map.setZoom(12);
}

function fitAllMarkers() {
    if (markers.length === 0) return;
    
    const bounds = new google.maps.LatLngBounds();
    markers.forEach(marker => {
        if (marker.getVisible()) {
            bounds.extend(marker.getPosition());
        }
    });
    
    map.fitBounds(bounds);
    
    // Ensure minimum zoom level
    const listener = google.maps.event.addListener(map, "idle", function() {
        if (map.getZoom() > 16) map.setZoom(16);
        google.maps.event.removeListener(listener);
    });
}

// Handle map resize when window resizes
window.addEventListener('resize', function() {
    if (map) {
        google.maps.event.trigger(map, 'resize');
    }
});
</script>

<!-- Load Google Maps API with Visualization Library -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvsbRmU1nrJiiVgHekSmZrkvQDiowP6zw&libraries=visualization&callback=initMap"></script>

<style>
.gm-style-iw {
    border-radius: 8px;
}

.gm-style-iw-d {
    overflow: hidden !important;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-check {
    margin-bottom: 0.5rem;
}

#map {
    border-radius: 0.375rem;
}
</style>
@endsection