@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Analytics & Reports</h1>
                <div>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="dateRange" id="date7" value="7" {{ $dateRange == '7' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="date7">7 Days</label>

                        <input type="radio" class="btn-check" name="dateRange" id="date30" value="30" {{ $dateRange == '30' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="date30">30 Days</label>

                        <input type="radio" class="btn-check" name="dateRange" id="date90" value="90" {{ $dateRange == '90' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="date90">90 Days</label>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">Back to Dashboard</a>
                </div>
            </div>

            <!-- Plantation Trends Chart -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tree Plantation Trends</h5>
                            <small class="text-muted">Last {{ $dateRange }} days</small>
                        </div>
                        <div class="card-body">
                            @if($plantationTrends->count() > 0)
                                <canvas id="plantationChart" width="400" height="200"></canvas>
                            @else
                                <p class="text-muted text-center">No plantation data for this period</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Inspection Trends</h5>
                            <small class="text-muted">Last {{ $dateRange }} days</small>
                        </div>
                        <div class="card-body">
                            @if($inspectionTrends->count() > 0)
                                <canvas id="inspectionChart" width="400" height="200"></canvas>
                            @else
                                <p class="text-muted text-center">No inspection data for this period</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Distribution -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tree Health Distribution</h5>
                            <small class="text-muted">Based on recent inspections</small>
                        </div>
                        <div class="card-body">
                            @if($healthDistribution->count() > 0)
                                <canvas id="healthChart" width="400" height="200"></canvas>
                            @else
                                <p class="text-muted text-center">No health data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Top Performing Volunteers</h5>
                            <small class="text-muted">By trees planted</small>
                        </div>
                        <div class="card-body">
                            @if($volunteerStats->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Volunteer</th>
                                                <th>Trees</th>
                                                <th>Inspections</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($volunteerStats as $volunteer)
                                                <tr>
                                                    <td>{{ $volunteer->name }}</td>
                                                    <td><span class="badge bg-primary">{{ $volunteer->planted_trees_count }}</span></td>
                                                    <td><span class="badge bg-info">{{ $volunteer->inspections_count }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">No volunteer data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Export Data</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h6>Trees Data</h6>
                                            <p class="text-muted">Export all tree plantation records</p>
                                            <a href="{{ route('admin.export') }}?type=trees&format=csv" class="btn btn-primary">Download CSV</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <h6>Inspections Data</h6>
                                            <p class="text-muted">Export all inspection records</p>
                                            <a href="{{ route('admin.export') }}?type=inspections&format=csv" class="btn btn-info">Download CSV</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h6>Volunteers Data</h6>
                                            <p class="text-muted">Export volunteer information</p>
                                            <a href="{{ route('admin.export') }}?type=volunteers&format=csv" class="btn btn-success">Download CSV</a>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Date range filter
document.querySelectorAll('input[name="dateRange"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        window.location.href = '{{ route("admin.analytics") }}?date_range=' + this.value;
    });
});

// Plantation Trends Chart
@if($plantationTrends->count() > 0)
const plantationCtx = document.getElementById('plantationChart').getContext('2d');
const plantationChart = new Chart(plantationCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($plantationTrends->pluck('date')->map(function($date) { return date('M d', strtotime($date)); })) !!},
        datasets: [{
            label: 'Trees Planted',
            data: {!! json_encode($plantationTrends->pluck('count')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
@endif

// Inspection Trends Chart
@if($inspectionTrends->count() > 0)
const inspectionCtx = document.getElementById('inspectionChart').getContext('2d');
const inspectionChart = new Chart(inspectionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($inspectionTrends->pluck('date')->map(function($date) { return date('M d', strtotime($date)); })) !!},
        datasets: [{
            label: 'Inspections Done',
            data: {!! json_encode($inspectionTrends->pluck('count')) !!},
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
@endif

// Health Distribution Chart
@if($healthDistribution->count() > 0)
const healthCtx = document.getElementById('healthChart').getContext('2d');
const healthChart = new Chart(healthCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($healthDistribution->pluck('tree_health')->map(function($health) { return ucfirst($health); })) !!},
        datasets: [{
            data: {!! json_encode($healthDistribution->pluck('count')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
@endif
</script>
@endsection