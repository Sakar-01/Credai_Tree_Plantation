@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Overdue Inspections</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>

            @if($overdueTrees->count() > 0)
                <div class="alert alert-warning" role="alert">
                    <strong>{{ $overdueTrees->total() }} trees</strong> have overdue inspections that require immediate attention.
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Trees Requiring Inspection</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tree ID</th>
                                        <th>Species</th>
                                        <th>Location</th>
                                        <th>Planted By</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Last Inspection</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueTrees as $tree)
                                        <tr class="{{ $tree->next_inspection_date->diffInDays(now()) > 30 ? 'table-danger' : 'table-warning' }}">
                                            <td>
                                                <strong>{{ $tree->tree_id }}</strong>
                                            </td>
                                            <td>{{ $tree->species }}</td>
                                            <td>{{ $tree->location_description }}</td>
                                            <td>{{ $tree->plantedBy->name }}</td>
                                            <td>{{ $tree->next_inspection_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $tree->next_inspection_date->diffInDays(now()) > 30 ? 'danger' : 'warning' }}">
                                                    {{ $tree->next_inspection_date->diffInDays(now()) }} days
                                                </span>
                                            </td>
                                            <td>
                                                @if($tree->latestInspection->first())
                                                    {{ $tree->latestInspection->first()->inspection_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('trees.show', $tree) }}" class="btn btn-outline-primary">View</a>
                                                    <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-warning">Inspect</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $overdueTrees->links() }}
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4>{{ $overdueTrees->where('next_inspection_date', '>', now()->subDays(30))->count() }}</h4>
                                <small>Recently Overdue (< 30 days)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4>{{ $overdueTrees->where('next_inspection_date', '<=', now()->subDays(30))->count() }}</h4>
                                <small>Critically Overdue (> 30 days)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>{{ $overdueTrees->total() }}</h4>
                                <small>Total Overdue Trees</small>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="text-success">🎉 All Caught Up!</h3>
                            <p class="text-muted">No trees have overdue inspections. Great job!</p>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection