@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="fas fa-clipboard-check"></i> Plantation Drive Inspections</h1>
                    <p class="text-muted">Track and manage plantation drive inspections</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($inspections->count() > 0)
                <div class="row">
                    @foreach($inspections as $inspection)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                @if($inspection->images && count($inspection->images) > 0)
                                    <div id="inspectionCarousel{{ $inspection->id }}" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach($inspection->images as $index => $image)
                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $image) }}" 
                                                         class="d-block w-100 card-img-top" 
                                                         style="height: 200px; object-fit: cover;" 
                                                         alt="Inspection Photo {{ $index + 1 }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(count($inspection->images) > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#inspectionCarousel{{ $inspection->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#inspectionCarousel{{ $inspection->id }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-dark bg-opacity-75">{{ count($inspection->images) }} photos</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-clipboard-check fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            {{ $inspection->plantation->location->name ?? $inspection->plantation->location_description }}
                                        </h5>
                                        <span class="badge bg-{{ $inspection->overall_health === 'excellent' ? 'success' : ($inspection->overall_health === 'good' ? 'primary' : ($inspection->overall_health === 'average' ? 'warning' : ($inspection->overall_health === 'poor' ? 'danger' : 'dark'))) }}">
                                            {{ ucfirst($inspection->overall_health) }}
                                        </span>
                                    </div>
                                    
                                    <p class="card-text flex-grow-1">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $inspection->inspection_date->format('M d, Y') }} 
                                            by {{ $inspection->inspectedBy->name }}
                                        </small>
                                        <br>
                                        <strong>Trees:</strong> {{ $inspection->trees_inspected }}/{{ $inspection->plantation->tree_count }}
                                        <span class="text-success">({{ $inspection->healthy_trees }} healthy)</span>
                                        @if($inspection->unhealthy_trees > 0)
                                            <span class="text-danger">({{ $inspection->unhealthy_trees }} unhealthy)</span>
                                        @endif
                                        <br>
                                        {{ Str::limit($inspection->description, 100) }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('plantation-inspections.show', $inspection) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                            @if(auth()->user()->isAdmin() || $inspection->inspected_by === auth()->id())
                                                <a href="{{ route('plantation-inspections.edit', $inspection) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endif
                                        </div>
                                        <a href="{{ route('plantations.show', $inspection->plantation) }}" class="btn btn-sm btn-outline-secondary">View Drive</a>
                                    </div>
                                </div>
                                @if($inspection->next_inspection_date)
                                    <div class="card-footer text-muted small">
                                        Next inspection: {{ $inspection->next_inspection_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $inspections->links() }}
                </div>
            @else
                <div class="text-center">
                    <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                    <h3>No Plantation Drive Inspections Yet</h3>
                    <p class="text-muted">Start inspecting your plantation drives to track their progress and health.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection