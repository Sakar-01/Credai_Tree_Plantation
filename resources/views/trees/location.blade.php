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
                    <a href="{{ route('locations.plant-tree', $location->id) }}" class="btn btn-primary">Plant New Tree</a>
                    <a href="{{ route('inspections.upcoming.location', $location->id) }}" class="btn btn-warning">
                        <i class="fas fa-calendar-check"></i> Upcoming Inspections ({{ $location->name }})
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
                                                    <td><span class="badge bg-success">{{ $trees->total() }}</span></td>
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

            @if($trees->count() > 0)
                <div class="row">
                    @foreach($trees as $tree)
                        <div class="col-md-4 mb-4">
                            <div class="card">
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
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-dark bg-opacity-75">{{ count($tree->images) }} photos</span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($tree->photo_path)
                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Tree Photo">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title"><strong> Species: {{ $tree->species }}</strong> </h5>
                                    <p class="card-text">
                                        Tree Id - {{ $tree->tree_id }}<br>
                                        @if($tree->height)
                                            Height: {{ $tree->height }} cm<br>
                                        @endif
                                        @if($tree->landmark_id && is_object($tree->landmark))
                                            Landmark: {{ $tree->landmark->name }}<br>
                                        @elseif($tree->landmark)
                                            Landmark: {{ $tree->landmark }}<br>
                                        @endif
                                        Planted: {{ $tree->plantation_date->format('M d, Y') }}<br>
                                        Status:
                                        <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                        </span>
                                    </p>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('trees.show', $tree) }}" class="btn btn-sm btn-primary">View Details</a>
                                        @if($tree->next_inspection_date <= now())
                                            <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-sm btn-warning">Inspect Now</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    Next inspection: {{ $tree->next_inspection_date->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $trees->links() }}
                </div>
            @else
                <div class="text-center">
                    <h3>No trees found in this location</h3>
                    <p class="text-muted">Be the first to plant a tree here!</p>
                    <a href="{{ route('locations.plant-tree', $location->id) }}" class="btn btn-primary btn-lg">Plant a Tree</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection