@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Upcoming Inspections</h1>
                <a href="{{ route('trees.index') }}" class="btn btn-secondary">Back to Trees</a>
            </div>

            @if($upcomingTrees->count() > 0)
                <div class="row">
                    @foreach($upcomingTrees as $tree)
                        <div class="col-md-4 mb-4">
                            <div class="card {{ $tree->next_inspection_date < now() ? 'border-danger' : ($tree->next_inspection_date <= now()->addDays(3) ? 'border-warning' : '') }}">
                                @if($tree->photo_path)
                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Tree Photo">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title"><strong> Species: {{ $tree->species }}</strong> </h5>
                                    <p class="card-text">
                                        Tree Id - {{ $tree->tree_id }}<br>
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
                    <p class="text-muted">All your trees are up to date with inspections!</p>
                    <a href="{{ route('trees.index') }}" class="btn btn-primary">View All Trees</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection