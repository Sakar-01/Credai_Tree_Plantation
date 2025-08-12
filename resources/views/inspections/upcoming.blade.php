@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Upcoming Inspections</h1>
                    @if(isset($location))
                        <p class="text-muted mb-0">
                            <i class="fas fa-map-marker-alt"></i> {{ $location->name }}
                        </p>
                    @endif
                </div>
                <a href="{{ isset($location) ? route('trees.location', $location->id) : route('trees.index') }}" class="btn btn-secondary">
                    {{ isset($location) ? 'Back to Location' : 'Back to Trees' }}
                </a>
            </div>

            @if($upcomingTrees->count() > 0)
                <div class="row">
                    @foreach($upcomingTrees as $tree)
                        <div class="col-md-4 mb-4">
                            <div class="card {{ $tree->next_inspection_date < now() ? 'border-danger' : ($tree->next_inspection_date <= now()->addDays(3) ? 'border-warning' : '') }}">
                                @if($tree->images && count($tree->images) > 0)
                                    <div id="inspectionCarousel{{ $tree->id }}" class="carousel slide" data-bs-ride="carousel">
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
                                            <button class="carousel-control-prev" type="button" data-bs-target="#inspectionCarousel{{ $tree->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#inspectionCarousel{{ $tree->id }}" data-bs-slide="next">
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
                                        Location: {{ $tree->location_description }}<br>
                                        Planted: {{ $tree->plantation_date->format('M d, Y') }}<br>
                                        Status: 
                                        <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                        </span>
                                    </p>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('trees.show', $tree) }}" class="btn btn-sm btn-primary">View Details</a>
                                        <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-sm btn-warning">Inspect Now</a>
                                    </div>
                                </div>
                                <div class="card-footer {{ $tree->next_inspection_date < now() ? 'bg-danger text-white' : ($tree->next_inspection_date <= now()->addDays(3) ? 'bg-warning' : 'text-muted') }}">
                                    @if($tree->next_inspection_date < now())
                                        <strong>OVERDUE:</strong> {{ $tree->next_inspection_date->format('M d, Y') }}
                                        ({{ $tree->next_inspection_date->diffForHumans() }})
                                    @else
                                        Due: {{ $tree->next_inspection_date->format('M d, Y') }}
                                        ({{ $tree->next_inspection_date->diffForHumans() }})
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center">
                    <h3>No upcoming inspections</h3>
                    <p class="text-muted">
                        @if(isset($location))
                            All trees in {{ $location->name }} are up to date with inspections!
                        @else
                            All your trees are up to date with inspections!
                        @endif
                    </p>
                    <a href="{{ isset($location) ? route('trees.location', $location->id) : route('trees.index') }}" class="btn btn-primary">
                        {{ isset($location) ? 'View Location Trees' : 'View All Trees' }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection