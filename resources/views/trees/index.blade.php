@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Locations</h1>
                <div>
                    <a href="{{ route('locations.create') }}" class="btn btn-primary">Add New Location</a>
                    <a href="{{ route('inspections.upcoming') }}" class="btn btn-warning">Upcoming Inspections</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($locations->count() > 0)
                <div class="row">
                    @foreach($locations as $location)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 location-card" style="cursor: pointer;" onclick="window.location.href='{{ route('trees.location', $location->id) }}'">
                                @if($location->images && count($location->images) > 0)
                                    <div id="carousel-{{ $location->id }}" class="carousel slide" data-bs-ride="carousel" onclick="event.stopPropagation();">
                                        <div class="carousel-inner">
                                            @foreach($location->images as $index => $image)
                                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 card-img-top" style="height: 200px; object-fit: cover;" alt="Location Image">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(count($location->images) > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-{{ $location->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#carousel-{{ $location->id }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                            <div class="carousel-indicators">
                                                @foreach($location->images as $index => $image)
                                                    <button type="button" data-bs-target="#carousel-{{ $location->id }}" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $location->name }}</h5>
                                    @if($location->landmarks->count() > 0)
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-landmark"></i> 
                                            @foreach($location->landmarks as $landmark)
                                                {{ $landmark->name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                    @endif
                                    <p class="card-text">
                                        <strong>{{ $location->trees_count }}</strong> {{ $location->trees_count == 1 ? 'tree' : 'trees' }} planted<br>
                                        <small class="text-muted">Last planted: {{ $location->latest_plantation_date ? \Carbon\Carbon::parse($location->latest_plantation_date)->format('M d, Y') : 'N/A' }}</small>
                                    </p>
                                    {{-- <div class="mt-3">
                                        @php
                                            $healthyCount = $location->trees->where('status', 'healthy')->count();
                                            $needsAttentionCount = $location->trees->where('status', 'needs_attention')->count();
                                            $otherCount = $location->trees_count - $healthyCount - $needsAttentionCount;
                                        @endphp
                                        @if($healthyCount > 0)
                                            <span class="badge bg-success me-1">{{ $healthyCount }} Healthy</span>
                                        @endif
                                        @if($needsAttentionCount > 0)
                                            <span class="badge bg-danger me-1">{{ $needsAttentionCount }} Need Attention</span>
                                        @endif
                                        @if($otherCount > 0)
                                            <span class="badge bg-secondary">{{ $otherCount }} Other</span>
                                        @endif
                                    </div> --}}
                                </div>
                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">Click to view trees in this location</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center">
                    <h3>No locations with trees yet</h3>
                    <p class="text-muted">Start by adding your first location!</p>
                    <a href="{{ route('locations.create') }}" class="btn btn-primary btn-lg">Add Your First Location</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.location-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.carousel-control-prev, .carousel-control-next {
    width: 5%;
    opacity: 0.7;
}

.carousel-control-prev:hover, .carousel-control-next:hover {
    opacity: 1;
}

.carousel-indicators {
    margin-bottom: 5px;
}

.carousel-indicators [data-bs-target] {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.card-img-top {
    border-top-left-radius: calc(0.375rem - 1px);
    border-top-right-radius: calc(0.375rem - 1px);
}
</style>
@endsection