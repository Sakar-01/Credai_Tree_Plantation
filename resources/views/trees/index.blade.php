@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Locations</h1>
                <div>
                    <a href="{{ route('trees.create') }}" class="btn btn-primary">Plant New Tree</a>
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
                                    <div class="mt-3">
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
                                    </div>
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
                    <p class="text-muted">Start by planting your first tree!</p>
                    <a href="{{ route('trees.create') }}" class="btn btn-primary btn-lg">Plant Your First Tree</a>
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
</style>
@endsection