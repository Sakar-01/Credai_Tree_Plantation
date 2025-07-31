@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Volunteer Management</h1>
                <div>
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

<!-- Volunteer Detail Modals -->
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