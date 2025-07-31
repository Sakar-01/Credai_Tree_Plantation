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
                        @if($tree->next_inspection_date <= now())
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
                            @if($tree->photo_path)
                                <img src="{{ asset('storage/' . $tree->photo_path) }}" class="img-fluid rounded" alt="Tree Photo">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>Species:</th>
                                    <td>{{ $tree->species }}</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $tree->location_description }}</td>
                                </tr>
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
                                    <td>{{ $tree->next_inspection_date->format('M d, Y') }}</td>
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
                                        View Survey File
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
            <!-- Map placeholder -->
            <div class="card">
                <div class="card-header">
                    <h6>Location Map</h6>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 300px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                        <p class="text-muted">Map integration can be added here<br>
                        Lat: {{ $tree->latitude }}<br>
                        Lng: {{ $tree->longitude }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($tree->next_inspection_date <= now())
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
@endsection