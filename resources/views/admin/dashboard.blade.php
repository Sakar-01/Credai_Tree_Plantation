@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Admin Dashboard</h1>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Trees</h5>
                            <h2>{{ $stats['total_trees'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Volunteers</h5>
                            <h2>{{ $stats['total_volunteers'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Inspections</h5>
                            <h2>{{ $stats['total_inspections'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Trees This Month</h5>
                            <h2>{{ $stats['trees_this_month'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Status Row -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Healthy Trees</h5>
                            <h2>{{ $stats['healthy_trees'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Need Attention</h5>
                            <h2>{{ $stats['trees_need_attention'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Overdue Inspections</h5>
                            <h2>{{ $overdueInspections }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.map') }}" class="btn btn-primary me-2">🗺️ Map View</a>
                            <a href="{{ route('admin.analytics') }}" class="btn btn-info me-2">📊 Analytics</a>
                            <a href="{{ route('admin.volunteers') }}" class="btn btn-success me-2">👥 Volunteers</a>
                            <a href="{{ route('admin.overdue-inspections') }}" class="btn btn-warning me-2">⚠️ Overdue</a>
                            <a href="{{ route('admin.export') }}?type=trees&format=csv" class="btn btn-secondary me-2">📄 Export</a>
                            <a href="{{ route('trees.index') }}" class="btn btn-outline-secondary">🌳 All Trees</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Trees and Inspections -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Tree Plantations</h5>
                        </div>
                        <div class="card-body">
                            @if($recentTrees->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tree ID</th>
                                                <th>Species</th>
                                                <th>Planted By</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentTrees as $tree)
                                                <tr>
                                                    <td>{{ $tree->tree_id }}</td>
                                                    <td>{{ $tree->species }}</td>
                                                    <td>{{ $tree->plantedBy->name }}</td>
                                                    <td>{{ $tree->plantation_date->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No recent tree plantations</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Inspections</h5>
                        </div>
                        <div class="card-body">
                            @if($recentInspections->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tree ID</th>
                                                <th>Health</th>
                                                <th>Inspector</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentInspections as $inspection)
                                                <tr>
                                                    <td>{{ $inspection->tree->tree_id }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $inspection->tree_health === 'good' ? 'success' : ($inspection->tree_health === 'average' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($inspection->tree_health) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $inspection->inspectedBy->name }}</td>
                                                    <td>{{ $inspection->inspection_date->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No recent inspections</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection