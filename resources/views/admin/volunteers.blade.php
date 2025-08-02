@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Volunteer Management</h1>
                <div>
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addVolunteerModal">
                        Add Volunteer
                    </button>
                    <a href="{{ route('admin.export') }}?type=volunteers&format=csv" class="btn btn-success">Export Volunteers</a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5>All Volunteers</h5>
                </div>
                <div class="card-body">
                    @if($volunteers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Region</th>
                                        <th>Trees Planted</th>
                                        <th>Inspections</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($volunteers as $volunteer)
                                        <tr>
                                            <td>
                                                <strong>{{ $volunteer->name }}</strong>
                                            </td>
                                            <td>{{ $volunteer->email }}</td>
                                            <td>{{ $volunteer->phone ?? 'N/A' }}</td>
                                            <td>{{ $volunteer->assigned_region ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $volunteer->planted_trees_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $volunteer->inspections_count }}</span>
                                            </td>
                                            <td>{{ $volunteer->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#volunteerModal{{ $volunteer->id }}">
                                                        View Details
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $volunteers->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted">No volunteers found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Volunteer Modal -->
<div class="modal fade" id="addVolunteerModal" tabindex="-1" aria-labelledby="addVolunteerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVolunteerModalLabel">Add New Volunteer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.volunteers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="assigned_region" class="form-label">Assigned Region</label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="assigned_region" name="assigned_region" 
                                   placeholder="Start typing location name..." autocomplete="off">
                            <div id="region_suggestions" class="dropdown-menu w-100" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Volunteer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Volunteer Detail Modals -->
<!-- JavaScript for location autocomplete -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionInput = document.getElementById('assigned_region');
    const suggestionsContainer = document.getElementById('region_suggestions');
    const locations = @json($locations);
    
    regionInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        
        if (query.length < 1) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        const filteredLocations = locations.filter(location => 
            location.toLowerCase().includes(query)
        );
        
        showSuggestions(filteredLocations);
    });
    
    function showSuggestions(suggestions) {
        if (suggestions.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        suggestionsContainer.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const suggestionItem = document.createElement('a');
            suggestionItem.className = 'dropdown-item';
            suggestionItem.href = '#';
            suggestionItem.textContent = suggestion;
            
            suggestionItem.addEventListener('click', function(e) {
                e.preventDefault();
                regionInput.value = suggestion;
                suggestionsContainer.style.display = 'none';
            });
            
            suggestionsContainer.appendChild(suggestionItem);
        });
        
        suggestionsContainer.style.display = 'block';
    }
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(event) {
        if (!regionInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
    
    // Hide suggestions on escape key
    regionInput.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            suggestionsContainer.style.display = 'none';
        }
    });
});
</script>

@foreach($volunteers as $volunteer)
<div class="modal fade" id="volunteerModal{{ $volunteer->id }}" tabindex="-1" aria-labelledby="volunteerModalLabel{{ $volunteer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="volunteerModalLabel{{ $volunteer->id }}">{{ $volunteer->name }} - Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Contact Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Email:</th>
                                <td>{{ $volunteer->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $volunteer->phone ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <th>Region:</th>
                                <td>{{ $volunteer->assigned_region ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <th>Joined:</th>
                                <td>{{ $volunteer->created_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Activity Summary</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h4>{{ $volunteer->planted_trees_count }}</h4>
                                        <small>Trees Planted</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h4>{{ $volunteer->inspections_count }}</h4>
                                        <small>Inspections Done</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($volunteer->plantedTrees->count() > 0)
                    <hr>
                    <h6>Recent Trees Planted</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tree ID</th>
                                    <th>Species</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($volunteer->plantedTrees->take(5) as $tree)
                                    <tr>
                                        <td>{{ $tree->tree_id }}</td>
                                        <td>{{ $tree->species }}</td>
                                        <td>{{ $tree->location_description }}</td>
                                        <td>{{ $tree->plantation_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection