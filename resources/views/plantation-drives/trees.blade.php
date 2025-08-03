@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('plantation-drives.index') }}">Locations</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantation-drives.location', $plantationDrive->location_id) }}">{{ $plantationDrive->location->name }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantation-drives.show', $plantationDrive) }}">{{ $plantationDrive->title }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Trees</li>
                        </ol>
                    </nav>
                    <h1>Trees from {{ $plantationDrive->title }}</h1>
                    <p class="text-muted">
                        {{ $plantationDrive->location->name }} • 
                        Planted on {{ $plantationDrive->plantation_date->format('M d, Y') }} • 
                        @php
                            $species = $plantationDrive->trees->whereNotNull('species')->pluck('species')->unique();
                        @endphp
                        @if($species->count() > 0)
                            <strong>Species:</strong> {{ $species->implode(', ') }}
                        @else
                            <strong>Species:</strong> <span class="text-muted">Not specified</span>
                        @endif
                    </p>
                </div>
                <div>
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Trees List ({{ $trees->count() }} total)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tree ID</th>
                                        <th>Species</th>
                                        <th>Status</th>
                                        <th>Height</th>
                                        <th>Photo</th>
                                        <th>Last Inspection</th>
                                        <th>Next Inspection</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trees as $tree)
                                        <tr>
                                            <td><code>{{ $tree->tree_id }}</code></td>
                                            <td>{{ $tree->species ?? 'Not specified' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : ($tree->status === 'planted' ? 'info' : 'secondary')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $tree->height ?? 'Not recorded' }}</td>
                                            <td>
                                                @if($tree->photo_path)
                                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" alt="Tree" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted">No photo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($tree->latestInspection->first())
                                                    {{ $tree->latestInspection->first()->inspection_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>{{ $tree->next_inspection_date->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('trees.show', $tree) }}" class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('trees.edit', $tree) }}" class="btn btn-outline-secondary" title="Edit Tree">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($tree->next_inspection_date <= now())
                                                        <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-outline-warning" title="Inspect Now">
                                                            <i class="fas fa-search"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">{{ $trees->where('status', 'planted')->count() }}</h3>
                                    <p class="card-text">Planted</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">{{ $trees->where('status', 'healthy')->count() }}</h3>
                                    <p class="card-text">Healthy</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-danger">{{ $trees->where('status', 'needs_attention')->count() }}</h3>
                                    <p class="card-text">Need Attention</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-secondary">{{ $trees->whereNotIn('status', ['planted', 'healthy', 'needs_attention'])->count() }}</h3>
                                    <p class="card-text">Other</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">
                    <h3>No trees found</h3>
                    <p class="text-muted">This should not happen. Trees should be automatically generated from the plantation drive.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection