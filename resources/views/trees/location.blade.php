@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('trees.index') }}">Locations</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $location->name }}</li>
                        </ol>
                    </nav>
                    <h1>Trees in {{ $location->name }}</h1>
                    @if($location->landmarks->count() > 0)
                        <p class="text-muted">
                            <i class="fas fa-landmark"></i> 
                            @foreach($location->landmarks as $landmark)
                                {{ $landmark->name }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('locations.plant-tree', $location->id) }}" class="btn btn-primary me-2">Plant New Tree</a>
                    <a href="{{ route('locations.plantation-drive', $location->id) }}" class="btn btn-success me-2">Create Plantation Drive</a>
                    <a href="{{ route('inspections.upcoming.location', $location->id) }}" class="btn btn-warning">
                        <i class="fas fa-calendar-check"></i> Upcoming Inspections
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Location Details Section -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-map-marker-alt"></i> Location Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Location Images -->
                                @if($location->images && count($location->images) > 0)
                                    <div class="col-md-4">
                                        <div id="locationCarousel" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                @foreach($location->images as $index => $image)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                        <img src="{{ asset('storage/' . $image) }}" 
                                                             class="d-block w-100 rounded" 
                                                             style="height: 250px; object-fit: cover;" 
                                                             alt="Location Image {{ $index + 1 }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if(count($location->images) > 1)
                                                <button class="carousel-control-prev" type="button" data-bs-target="#locationCarousel" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#locationCarousel" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Location Information -->
                                <div class="col-md-{{ $location->images && count($location->images) > 0 ? '8' : '12' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="40%">Name:</th>
                                                    <td>{{ $location->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Description:</th>
                                                    <td>{{ $location->description }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Coordinates:</th>
                                                    <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Trees:</th>
                                                    <td><span class="badge bg-success">{{ $allTrees->total() }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Plantation Drives:</th>
                                                    <td><span class="badge bg-info">{{ $plantationDrives->total() }}</span></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6><i class="fas fa-chart-pie"></i> Tree Status Summary</h6>
                                                    @php
                                                        $statusCounts = $allTrees->groupBy('status')->map->count();
                                                    @endphp
                                                    @foreach($statusCounts as $status => $count)
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span>{{ ucfirst(str_replace('_', ' ', $status)) }}:</span>
                                                            <span class="badge bg-{{ $status === 'healthy' ? 'success' : ($status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                                                {{ $count }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="locationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">
                        <i class="fas fa-tree"></i> Individual Trees ({{ $individualTrees->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="drives-tab" data-bs-toggle="tab" data-bs-target="#drives" type="button" role="tab">
                        <i class="fas fa-seedling"></i> Plantation Drives ({{ $plantationDrives->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        <i class="fas fa-list"></i> All Trees ({{ $allTrees->total() }})
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="locationTabsContent">
                <!-- Individual Trees Tab -->
                <div class="tab-pane fade show active" id="individual" role="tabpanel">
                    <div class="py-4">
                        @if($individualTrees->count() > 0)
                            <div class="row">
                                @foreach($individualTrees as $tree)
                                    @include('trees.partials.tree-card', ['tree' => $tree])
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $individualTrees->appends(request()->query())->fragment('individual')->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-tree fa-3x text-muted mb-3"></i>
                                <h4>No individual trees found</h4>
                                <p class="text-muted">Plant your first individual tree in this location!</p>
                                <a href="{{ route('locations.plant-tree', $location->id) }}" class="btn btn-primary">Plant a Tree</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Plantation Drives Tab -->
                <div class="tab-pane fade" id="drives" role="tabpanel">
                    <div class="py-4">
                        @if($plantationDrives->count() > 0)
                            <div class="row">
                                @foreach($plantationDrives as $drive)
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            @if($drive->images && count($drive->images) > 0)
                                                <div id="driveCarousel{{ $drive->id }}" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-inner">
                                                        @foreach($drive->images as $index => $image)
                                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                <img src="{{ asset('storage/' . $image) }}" 
                                                                     class="d-block w-100 card-img-top" 
                                                                     style="height: 200px; object-fit: cover;" 
                                                                     alt="Drive Image {{ $index + 1 }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @if(count($drive->images) > 1)
                                                        <button class="carousel-control-prev" type="button" data-bs-target="#driveCarousel{{ $drive->id }}" data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                            <span class="visually-hidden">Previous</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button" data-bs-target="#driveCarousel{{ $drive->id }}" data-bs-slide="next">
                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                            <span class="visually-hidden">Next</span>
                                                        </button>
                                                        <div class="position-absolute top-0 end-0 m-2">
                                                            <span class="badge bg-dark bg-opacity-75">{{ count($drive->images) }} photos</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                    <i class="fas fa-seedling fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="fas fa-calendar"></i> {{ $drive->plantation_date->format('M d, Y') }}
                                                </h5>
                                                <p class="card-text">
                                                    <strong>Trees Planted:</strong> <span class="badge bg-success">{{ $drive->tree_count }}</span><br>
                                                    <strong>Created by:</strong> {{ $drive->createdBy->name }}<br>
                                                    @if($drive->landmark)
                                                        <strong>Landmark:</strong> {{ $drive->landmark }}<br>
                                                    @endif
                                                    @if($drive->description)
                                                        <strong>Description:</strong> {{ Str::limit($drive->description, 60) }}
                                                    @endif
                                                </p>
                                                @php
                                                    $driveTreesWithSpecies = $drive->trees->whereNotNull('species')->count();
                                                    $driveTreesNeedingSpecies = $drive->trees->whereNull('species')->count();
                                                @endphp
                                                <div class="mb-3">
                                                    @if($driveTreesWithSpecies > 0)
                                                        <span class="badge bg-info me-1">{{ $driveTreesWithSpecies }} with species</span>
                                                    @endif
                                                    @if($driveTreesNeedingSpecies > 0)
                                                        <span class="badge bg-warning">{{ $driveTreesNeedingSpecies }} need species</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <a href="{{ route('plantations.trees', $drive) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View Trees
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $plantationDrives->appends(request()->query())->fragment('drives')->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                                <h4>No plantation drives found</h4>
                                <p class="text-muted">Create your first plantation drive in this location!</p>
                                <a href="{{ route('plantations.create') }}?location_id={{ $location->id }}" class="btn btn-success">Create Plantation Drive</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- All Trees Tab -->
                <div class="tab-pane fade" id="all" role="tabpanel">
                    <div class="py-4">
                        @if($allTrees->count() > 0)
                            <div class="row">
                                @foreach($allTrees as $tree)
                                    @include('trees.partials.tree-card', ['tree' => $tree, 'showPlantationInfo' => true])
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $allTrees->appends(request()->query())->fragment('all')->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-tree fa-3x text-muted mb-3"></i>
                                <h4>No trees found in this location</h4>
                                <p class="text-muted">Be the first to plant trees here!</p>
                                <div>
                                    <a href="{{ route('locations.plant-tree', $location->id) }}" class="btn btn-primary me-2">Plant a Tree</a>
                                    <a href="{{ route('plantations.create') }}?location_id={{ $location->id }}" class="btn btn-success">Create Plantation Drive</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection