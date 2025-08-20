@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('trees.index') }}">Trees</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('trees.location', $plantation->location->id) }}">{{ $plantation->location->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Drive #{{ $plantation->id }}</li>
                        </ol>
                    </nav>
                    <h1><i class="fas fa-seedling"></i> Drive #{{ $plantation->id }}</h1>
                    <p class="text-muted mb-0">{{ $plantation->location_description }}</p>
                    @if($plantation->landmark)
                        <p class="text-muted"><i class="fas fa-landmark"></i> {{ $plantation->landmark }}</p>
                    @endif
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('plantations.inspect', $plantation) }}" class="btn btn-warning">
                            <i class="fas fa-clipboard-check"></i> Inspect Drive
                        </a>
                        <a href="{{ route('plantations.create') }}" class="btn btn-primary">Create New Drive</a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Plantation Drive Details -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-info-circle"></i> Plantation Drive Details</h5>
                            <a href="{{ route('plantations.inspect', $plantation) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-clipboard-check"></i> Inspect Drive
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($plantation->images && count($plantation->images) > 0)
                                    <div class="col-md-4">
                                        <div id="plantationCarousel" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                @foreach($plantation->images as $index => $image)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                        <img src="{{ asset('storage/' . $image) }}" 
                                                             class="d-block w-100 rounded" 
                                                             style="height: 250px; object-fit: cover;" 
                                                             alt="Plantation Image {{ $index + 1 }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if(count($plantation->images) > 1)
                                                <button class="carousel-control-prev" type="button" data-bs-target="#plantationCarousel" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#plantationCarousel" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-dark bg-opacity-75">{{ count($plantation->images) }} photos</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="col-md-{{ $plantation->images && count($plantation->images) > 0 ? '8' : '12' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="40%">Plantation Date:</th>
                                                    <td>{{ $plantation->plantation_date->format('M d, Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Trees:</th>
                                                    <td><span class="badge bg-success">{{ $plantation->tree_count }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Created by:</th>
                                                    <td>{{ $plantation->createdBy->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Coordinates:</th>
                                                    <td>{{ $plantation->latitude }}, {{ $plantation->longitude }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6><i class="fas fa-chart-pie"></i> Tree Status Summary</h6>
                                                    @php
                                                        $statusCounts = $trees->groupBy('status')->map->count();
                                                    @endphp
                                                    @foreach($statusCounts as $status => $count)
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span>{{ ucfirst(str_replace('_', ' ', $status)) }}:</span>
                                                            <span class="badge bg-{{ $status === 'healthy' ? 'success' : ($status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                                                {{ $count }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                    
                                                    @php
                                                        $speciesCount = $trees->whereNotNull('species')->groupBy('species')->count();
                                                        $noSpeciesCount = $trees->whereNull('species')->count();
                                                    @endphp
                                                    <hr>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>Species Added:</span>
                                                        <span class="badge bg-info">{{ $speciesCount }} types</span>
                                                    </div>
                                                    @if($noSpeciesCount > 0)
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span>Missing Species:</span>
                                                            <span class="badge bg-warning">{{ $noSpeciesCount }} trees</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($plantation->description)
                                        <div class="mt-3">
                                            <h6>Description:</h6>
                                            <p class="text-muted">{{ $plantation->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Drive Inspections and Trees in Columns -->
            <div class="row mb-4">
                <!-- Drive Inspections Column (Left) -->
                <div class="col-md-5">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-clipboard-list"></i> Drive Inspections</h5>
                            <div>
                                @if($plantation->inspections->count() > 0)
                                    <span class="badge bg-primary">{{ $plantation->inspections->count() }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($plantation->inspections->count() > 0)
                                @php
                                    $latestInspection = $plantation->inspections->sortByDesc('inspection_date')->first();
                                    $recentInspections = $plantation->inspections->sortByDesc('inspection_date')->take(5);
                                @endphp

                                <!-- Latest Inspection Highlight -->
                                @if($latestInspection)
                                    <div class="alert alert-info border-start border-primary border-4 mb-3">
                                        <h6 class="alert-heading mb-1">
                                            <i class="fas fa-clock"></i> Latest Inspection
                                        </h6>
                                        <small class="text-muted">{{ $latestInspection->inspection_date->format('M d, Y') }}</small>
                                        <div class="mt-2">
                                            <div class="mb-1">
                                                <strong>Health:</strong>
                                                <span class="badge bg-{{ $latestInspection->overall_health === 'excellent' ? 'success' : ($latestInspection->overall_health === 'good' ? 'primary' : ($latestInspection->overall_health === 'average' ? 'warning' : ($latestInspection->overall_health === 'poor' ? 'danger' : 'dark'))) }}">
                                                    {{ ucfirst($latestInspection->overall_health) }}
                                                </span>
                                            </div>
                                            <div class="mb-1">
                                                <strong>Trees:</strong> {{ $latestInspection->trees_inspected }}/{{ $plantation->tree_count }}
                                                (<span class="text-success">{{ $latestInspection->healthy_trees }}</span>H / 
                                                <span class="text-danger">{{ $latestInspection->unhealthy_trees }}</span>U)
                                            </div>
                                            @if($latestInspection->next_inspection_date)
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt"></i> Next: {{ $latestInspection->next_inspection_date->format('M d, Y') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('plantation-inspections.show', $latestInspection) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <!-- Recent Inspections List -->
                                @if($recentInspections->count() > 1)
                                    <h6><i class="fas fa-history"></i> Recent Inspections</h6>
                                    @foreach($recentInspections->skip(1) as $inspection)
                                        <div class="card mb-2 border-0 bg-light">
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong class="small">{{ $inspection->inspection_date->format('M d, Y') }}</strong>
                                                        <span class="badge bg-{{ $inspection->overall_health === 'excellent' ? 'success' : ($inspection->overall_health === 'good' ? 'primary' : ($inspection->overall_health === 'average' ? 'warning' : ($inspection->overall_health === 'poor' ? 'danger' : 'dark'))) }} small ms-1">
                                                            {{ ucfirst($inspection->overall_health) }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $inspection->trees_inspected }}/{{ $plantation->tree_count }} trees
                                                            (<span class="text-success">{{ $inspection->healthy_trees }}</span>H / 
                                                            <span class="text-danger">{{ $inspection->unhealthy_trees }}</span>U)
                                                        </small>
                                                    </div>
                                                    <a href="{{ route('plantation-inspections.show', $inspection) }}" class="btn btn-xs btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <!-- Action Buttons -->
                                <div class="mt-3">
                                    <a href="{{ route('plantations.inspect', $plantation) }}" class="btn btn-warning btn-sm w-100 mb-2">
                                        <i class="fas fa-plus"></i> New Inspection
                                    </a>
                                    @if($plantation->inspections->count() > 5)
                                        <a href="{{ route('plantation-inspections.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                            View All {{ $plantation->inspections->count() }} Inspections
                                        </a>
                                    @endif
                                </div>

                            @else
                                <!-- No Inspections Yet -->
                                <div class="text-center py-3">
                                    <i class="fas fa-clipboard-check fa-2x text-muted mb-3"></i>
                                    <h6>No Drive Inspections Yet</h6>
                                    <p class="text-muted small">Start tracking the overall health and progress of this plantation drive.</p>
                                    <a href="{{ route('plantations.inspect', $plantation) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-clipboard-check"></i> Start First Inspection
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Trees Summary Column (Right) -->
                <div class="col-md-7">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-tree"></i> Trees Overview</h5>
                            <span class="badge bg-success">{{ $plantation->tree_count }} trees</span>
                        </div>
                        <div class="card-body">
                            @php
                                $statusCounts = $trees->groupBy('status')->map->count();
                                $speciesCount = $trees->whereNotNull('species')->groupBy('species')->count();
                                $noSpeciesCount = $trees->whereNull('species')->count();
                                $recentTrees = $trees->take(6);
                            @endphp

                            <!-- Quick Stats -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <h6 class="text-success mb-1">{{ $plantation->tree_count }}</h6>
                                        <small class="text-muted">Total Trees</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <h6 class="text-info mb-1">{{ $speciesCount }}</h6>
                                        <small class="text-muted">Species Types</small>
                                    </div>
                                </div>
                            </div>


                            <!-- Recent Trees Preview -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6>Recent Trees</h6>
                                    <small class="text-muted">{{ $trees->count() }} total</small>
                                </div>
                                <div class="row">
                                    @foreach($recentTrees as $tree)
                                        <div class="col-4 mb-2">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body p-2">
                                                    <div class="text-center">
                                                        @if($tree->images && count($tree->images) > 0)
                                                            <img src="{{ asset('storage/' . $tree->images[0]) }}" 
                                                                 class="img-fluid rounded mb-1" 
                                                                 style="height: 40px; width: 40px; object-fit: cover;">
                                                        @else
                                                            <i class="fas fa-tree fa-2x text-muted mb-1"></i>
                                                        @endif
                                                        <br>
                                                        <small class="fw-bold">{{ $tree->tree_id }}</small>
                                                        <br>
                                                        <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }} small">
                                                            {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="text-center">
                                <a href="#trees-section" class="btn btn-primary btn-sm" onclick="document.getElementById('trees-section').scrollIntoView({behavior: 'smooth'})">
                                    <i class="fas fa-tree"></i> View All Trees Below
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trees Grid -->
            @if($trees->count() > 0)
                <div id="trees-section" class="row">
                    @foreach($trees as $tree)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                @if($tree->images && count($tree->images) > 0)
                                    <div id="treeCarousel{{ $tree->id }}" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach($tree->images as $index => $image)
                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $image) }}" 
                                                         class="d-block w-100 card-img-top" 
                                                         style="height: 200px; object-fit: cover;" 
                                                         alt="Tree Photo {{ $index + 1 }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(count($tree->images) > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#treeCarousel{{ $tree->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#treeCarousel{{ $tree->id }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        @endif
                                    </div>
                                @elseif($tree->photo_path)
                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Tree Photo">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-tree fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <strong>{{ $tree->tree_id }}</strong>
                                    </h5>
                                    <p class="card-text flex-grow-1">
                                        <strong>Species:</strong> 
                                        @if($tree->species)
                                            {{ $tree->species }}
                                        @else
                                            <span class="text-warning">Not specified</span>
                                            <small class="d-block text-muted">Click edit to add species</small>
                                        @endif
                                        <br>
                                        
                                        @if($tree->height)
                                            <strong>Height:</strong> {{ $tree->height }} cm<br>
                                        @endif
                                        
                                        <strong>Planted:</strong> {{ $tree->plantation_date->format('M d, Y') }}<br>
                                        
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                        </span>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('drives.trees.show', [$plantation, $tree]) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="{{ route('drives.trees.edit', [$plantation, $tree]) }}" class="btn btn-sm btn-primary">Edit</a>
                                        </div>
                                        @if($tree->next_inspection_date && $tree->next_inspection_date <= now())
                                            <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-sm btn-warning">Inspect</a>
                                        @endif
                                    </div>
                                </div>
                                @if($tree->next_inspection_date)
                                    <div class="card-footer text-muted small">
                                        Next inspection: {{ $tree->next_inspection_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $trees->links() }}
                </div>
            @else
                <div class="text-center">
                    <h3>No trees found in this plantation drive</h3>
                    <p class="text-muted">This should not happen. Please contact support.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection