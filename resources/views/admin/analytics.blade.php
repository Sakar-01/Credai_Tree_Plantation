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

            <!-- Location Analytics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Trees by Location</h5>
                            <small class="text-muted">Top 10 locations</small>
                        </div>
                        <div class="card-body">
                            @if($locationDistribution->count() > 0)
                                <canvas id="locationChart" width="400" height="200"></canvas>
                            @else
                                <p class="text-muted text-center">No location data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Location Plantation Trends</h5>
                            <small class="text-muted">Top 5 locations - Last {{ $dateRange }} days</small>
                        </div>
                        <div class="card-body">
                            @if($topLocations->count() > 0 && $locationTrends->count() > 0)
                                <canvas id="locationTrendsChart" width="400" height="200"></canvas>
                            @else
                                <p class="text-muted text-center">No location trend data for this period</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Health Analysis -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tree Health by Location</h5>
                            <small class="text-muted">Based on recent inspections (last 90 days)</small>
                        </div>
                        <div class="card-body">
                            @if($locationHealthData->count() > 0)
                                <canvas id="locationHealthChart" width="600" height="300"></canvas>
                            @else
                                <p class="text-muted text-center">No location health data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Location Statistics</h5>
                        </div>
                        <div class="card-body">
                            @if($locationDistribution->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Trees</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($locationDistribution->take(8) as $location)
                                                <tr>
                                                    <td>{{ \Illuminate\Support\Str::limit($location->location_description, 20) }}</td>
                                                    <td><span class="badge bg-primary">{{ $location->tree_count }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">No location data available</p>
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
                                <div class="col-md-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h6>Trees Data</h6>
                                            <p class="text-muted">Export all tree plantation records</p>
                                            <a href="{{ route('admin.export') }}?type=trees&format=csv" class="btn btn-primary">Download CSV</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <h6>Inspections Data</h6>
                                            <p class="text-muted">Export all inspection records</p>
                                            <a href="{{ route('admin.export') }}?type=inspections&format=csv" class="btn btn-info">Download CSV</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h6>Volunteers Data</h6>
                                            <p class="text-muted">Export volunteer information</p>
                                            <a href="{{ route('admin.export') }}?type=volunteers&format=csv" class="btn btn-success">Download CSV</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <h6>Location Data</h6>
                                            <p class="text-muted">Export location analytics</p>
                                            <a href="{{ route('admin.export') }}?type=locations&format=csv" class="btn btn-warning">Download CSV</a>
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

// Location Distribution Chart
@if($locationDistribution->count() > 0)
const locationCtx = document.getElementById('locationChart').getContext('2d');
const locationChart = new Chart(locationCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($locationDistribution->pluck('location_description')->map(function($location) { return \Illuminate\Support\Str::limit($location, 15); })) !!},
        datasets: [{
            label: 'Trees',
            data: {!! json_encode($locationDistribution->pluck('tree_count')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
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
            },
            x: {
                ticks: {
                    maxRotation: 45
                }
            }
        }
    }
});
@endif

// Location Trends Chart
@if($topLocations->count() > 0 && $locationTrends->count() > 0)
const locationTrendsCtx = document.getElementById('locationTrendsChart').getContext('2d');

// Prepare data for location trends
const locationTrendsData = {
    labels: [], // Will be filled with dates
    datasets: []
};

// Get all unique dates
const allDates = [];
@foreach($locationTrends as $location => $trends)
    @foreach($trends as $trend)
        allDates.push('{{ $trend->date }}');
    @endforeach
@endforeach

// Get unique sorted dates
const uniqueDates = [...new Set(allDates)].sort();
locationTrendsData.labels = uniqueDates.map(date => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});

// Colors for different locations
const colors = [
    'rgba(255, 99, 132, 0.8)',
    'rgba(54, 162, 235, 0.8)', 
    'rgba(255, 205, 86, 0.8)',
    'rgba(75, 192, 192, 0.8)',
    'rgba(153, 102, 255, 0.8)'
];

let colorIndex = 0;
@foreach($topLocations as $index => $location)
    const location_{{ $index }}_data = new Array(uniqueDates.length).fill(0);
    
    @if(isset($locationTrends[$location->location_description]))
        @foreach($locationTrends[$location->location_description] as $trend)
            const dateIndex_{{ $loop->parent->index }}_{{ $loop->index }} = uniqueDates.indexOf('{{ $trend->date }}');
            if (dateIndex_{{ $loop->parent->index }}_{{ $loop->index }} !== -1) {
                location_{{ $loop->parent->index }}_data[dateIndex_{{ $loop->parent->index }}_{{ $loop->index }}] = {{ $trend->count }};
            }
        @endforeach
    @endif
    
    locationTrendsData.datasets.push({
        label: '{{ \Illuminate\Support\Str::limit($location->location_description, 20) }}',
        data: location_{{ $index }}_data,
        borderColor: colors[colorIndex % colors.length],
        backgroundColor: colors[colorIndex % colors.length].replace('0.8', '0.2'),
        tension: 0.1
    });
    colorIndex++;
@endforeach

const locationTrendsChart = new Chart(locationTrendsCtx, {
    type: 'line',
    data: locationTrendsData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
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

// Location Health Chart
@if($locationHealthData->count() > 0)
const locationHealthCtx = document.getElementById('locationHealthChart').getContext('2d');

// Prepare stacked bar chart data
const locationHealthData = {
    labels: {!! json_encode($locationHealthData->keys()->take(6)) !!}.map(location => location.length > 15 ? location.substring(0, 15) + '...' : location),
    datasets: [
        {
            label: 'Good',
            data: [],
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        },
        {
            label: 'Average', 
            data: [],
            backgroundColor: 'rgba(255, 206, 86, 0.8)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        },
        {
            label: 'Poor',
            data: [],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }
    ]
};

// Fill data for each location
@foreach($locationHealthData->take(6) as $location => $healthData)
    const goodCount_{{ $loop->index }} = {{ $healthData->where('tree_health', 'good')->sum('count') }};
    const averageCount_{{ $loop->index }} = {{ $healthData->where('tree_health', 'average')->sum('count') }};
    const poorCount_{{ $loop->index }} = {{ $healthData->where('tree_health', 'poor')->sum('count') }};
    
    locationHealthData.datasets[0].data.push(goodCount_{{ $loop->index }});
    locationHealthData.datasets[1].data.push(averageCount_{{ $loop->index }});
    locationHealthData.datasets[2].data.push(poorCount_{{ $loop->index }});
@endforeach

const locationHealthChart = new Chart(locationHealthCtx, {
    type: 'bar',
    data: locationHealthData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            x: {
                stacked: true,
                ticks: {
                    maxRotation: 45
                }
            },
            y: {
                stacked: true,
                beginAtZero: true
            }
        }
    }
});
@endif
</script>
@endsection