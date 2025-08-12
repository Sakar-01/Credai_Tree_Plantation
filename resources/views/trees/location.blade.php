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
                    <a href="{{ route('inspections.upcoming') }}" class="btn btn-warning">Upcoming Inspections</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($trees->count() > 0)
                <div class="row">
                    @foreach($trees as $tree)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                @if($tree->photo_path)
                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Tree Photo">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title"><strong> Species: {{ $tree->species }}</strong> </h5>
                                    <p class="card-text">
                                        Tree Id - {{ $tree->tree_id }}<br>
                                        @if($tree->landmark_id && $tree->landmark)
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