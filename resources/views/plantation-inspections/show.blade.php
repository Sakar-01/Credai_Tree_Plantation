@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-clipboard-check"></i> Plantation Drive Inspection</h4>
                        <div>
                            @if(auth()->user()->isAdmin() || $plantationInspection->inspected_by === auth()->id())
                                <a href="{{ route('plantation-inspections.edit', $plantationInspection) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('plantations.show', $plantationInspection->plantation) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Drive
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Inspection Images -->
                    @if($plantationInspection->images && count($plantationInspection->images) > 0)
                        <div class="mb-4">
                            <div id="inspectionCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($plantationInspection->images as $index => $image)
                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $image) }}" 
                                                 class="d-block w-100 rounded" 
                                                 style="height: 400px; object-fit: cover;" 
                                                 alt="Inspection Photo {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($plantationInspection->images) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#inspectionCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#inspectionCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75">{{ count($plantationInspection->images) }} photos</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Inspection Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Inspection Date:</th>
                                    <td>{{ $plantationInspection->inspection_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Inspector:</th>
                                    <td>{{ $plantationInspection->inspectedBy->name }}</td>
                                </tr>
                                <tr>
                                    <th>Overall Health:</th>
                                    <td>
                                        <span class="badge bg-{{ $plantationInspection->overall_health === 'excellent' ? 'success' : ($plantationInspection->overall_health === 'good' ? 'primary' : ($plantationInspection->overall_health === 'average' ? 'warning' : ($plantationInspection->overall_health === 'poor' ? 'danger' : 'dark'))) }} fs-6">
                                            {{ ucfirst($plantationInspection->overall_health) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trees Inspected:</th>
                                    <td>{{ $plantationInspection->trees_inspected }}/{{ $plantationInspection->plantation->tree_count }}</td>
                                </tr>
                                <tr>
                                    <th>Healthy Trees:</th>
                                    <td><span class="badge bg-success">{{ $plantationInspection->healthy_trees }}</span></td>
                                </tr>
                                <tr>
                                    <th>Unhealthy Trees:</th>
                                    <td><span class="badge bg-danger">{{ $plantationInspection->unhealthy_trees }}</span></td>
                                </tr>
                                @if($plantationInspection->next_inspection_date)
                                <tr>
                                    <th>Next Inspection:</th>
                                    <td>{{ $plantationInspection->next_inspection_date->format('M d, Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <!-- Tree Health Chart -->
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Tree Health Distribution</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <h3 class="text-success">{{ $plantationInspection->healthy_trees }}</h3>
                                            <small class="text-muted">Healthy</small>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-danger">{{ $plantationInspection->unhealthy_trees }}</h3>
                                            <small class="text-muted">Unhealthy</small>
                                        </div>
                                    </div>
                                    @if($plantationInspection->trees_inspected > 0)
                                        <div class="progress mt-3">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ ($plantationInspection->healthy_trees / $plantationInspection->trees_inspected) * 100 }}%">
                                                {{ round(($plantationInspection->healthy_trees / $plantationInspection->trees_inspected) * 100) }}%
                                            </div>
                                            <div class="progress-bar bg-danger" role="progressbar" 
                                                 style="width: {{ ($plantationInspection->unhealthy_trees / $plantationInspection->trees_inspected) * 100 }}%">
                                                {{ round(($plantationInspection->unhealthy_trees / $plantationInspection->trees_inspected) * 100) }}%
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h6><i class="fas fa-file-alt"></i> Inspection Description</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $plantationInspection->description }}
                        </div>
                    </div>

                    <!-- Recommendations -->
                    @if($plantationInspection->recommendations)
                        <div class="mb-4">
                            <h6><i class="fas fa-lightbulb"></i> Maintenance Recommendations</h6>
                            <div class="bg-warning bg-opacity-10 border-start border-warning border-4 p-3">
                                {{ $plantationInspection->recommendations }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Plantation Drive Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-seedling"></i> Plantation Drive</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $plantationInspection->plantation->location->name ?? $plantationInspection->plantation->location_description }}</h6>
                    @if($plantationInspection->plantation->landmark)
                        <p class="text-muted mb-2"><i class="fas fa-landmark"></i> {{ $plantationInspection->plantation->landmark }}</p>
                    @endif
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Planted:</th>
                            <td>{{ $plantationInspection->plantation->plantation_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Total Trees:</th>
                            <td>{{ $plantationInspection->plantation->tree_count }}</td>
                        </tr>
                        <tr>
                            <th>Created by:</th>
                            <td>{{ $plantationInspection->plantation->createdBy->name }}</td>
                        </tr>
                    </table>
                    
                    <div class="d-grid">
                        <a href="{{ route('plantations.show', $plantationInspection->plantation) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> View Full Drive
                        </a>
                    </div>
                </div>
            </div>

            <!-- Other Inspections -->
            @php
                $otherInspections = $plantationInspection->plantation->inspections()
                    ->where('id', '!=', $plantationInspection->id)
                    ->orderByDesc('inspection_date')
                    ->take(5)
                    ->get();
            @endphp
            
            @if($otherInspections->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h6><i class="fas fa-history"></i> Other Drive Inspections</h6>
                    </div>
                    <div class="card-body">
                        @foreach($otherInspections as $inspection)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <small class="fw-bold">{{ $inspection->inspection_date->format('M d, Y') }}</small>
                                    <br>
                                    <span class="badge bg-{{ $inspection->overall_health === 'excellent' ? 'success' : ($inspection->overall_health === 'good' ? 'primary' : ($inspection->overall_health === 'average' ? 'warning' : ($inspection->overall_health === 'poor' ? 'danger' : 'dark'))) }} small">
                                        {{ ucfirst($inspection->overall_health) }}
                                    </span>
                                </div>
                                <a href="{{ route('plantation-inspections.show', $inspection) }}" class="btn btn-xs btn-outline-primary">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection