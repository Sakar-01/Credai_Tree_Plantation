@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Location Analytics</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Trees</h5>
                                    <h2 class="card-text">{{ $totalTrees }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Locations</h5>
                                    <h2 class="card-text">{{ $totalLocations }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Number of Trees</th>
                                    <th>Average Coordinates</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locationStats as $location)
                                <tr>
                                    <td>{{ $location->location_description }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $location->tree_count }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ number_format($location->avg_latitude, 6) }}, 
                                            {{ number_format($location->avg_longitude, 6) }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ ($location->tree_count / $totalTrees) * 100 }}%">
                                                {{ number_format(($location->tree_count / $totalTrees) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection