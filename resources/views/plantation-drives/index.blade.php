@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Locations</h1>
                <div>
                    <a href="{{ route('plantation-drives.create') }}" class="btn btn-primary">Create Plantation Drive</a>
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
                            <div class="card h-100 location-card" style="cursor: pointer;" onclick="window.location.href='{{ route('plantation-drives.location', $location->id) }}'">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $location->name }}</h5>
                                    @if($location->landmark)
                                        <p class="text-muted mb-2"><i class="fas fa-landmark"></i> {{ $location->landmark }}</p>
                                    @endif
                                    <p class="card-text">
                                        <strong>{{ $location->plantation_drives_count }}</strong> {{ $location->plantation_drives_count == 1 ? 'drive' : 'drives' }}<br>
                                        {{-- <strong>{{ $location->plantationDrives ? $location->plantationDrives->sum('number_of_trees') : 0 }}</strong> total trees planted<br>
                                        <small class="text-muted">Latest drive: {{ $location->plantationDrives?->first()?->plantation_date?->format('M d, Y') ?? 'N/A' }}</small> --}}
                                    </p>
                                    <div class="mt-3">
                                        @php
                                            $trees = $location->trees ?? collect();
                                            $healthyCount = $trees->where('status', 'healthy')->count();
                                            $needsAttentionCount = $trees->where('status', 'needs_attention')->count();
                                            $plantedCount = $trees->where('status', 'planted')->count();
                                            $otherCount = $trees->count() - $healthyCount - $needsAttentionCount - $plantedCount;
                                        @endphp
                                        @if($plantedCount > 0)
                                            <span class="badge bg-info me-1">{{ $plantedCount }} Planted</span>
                                        @endif
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
                                    <small class="text-muted">Click to view plantation drives in this location</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center">
                    <h3>No plantation drives yet</h3>
                    <p class="text-muted">Start by creating your first plantation drive!</p>
                    <a href="{{ route('plantation-drives.create') }}" class="btn btn-primary btn-lg">Create Plantation Drive</a>
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