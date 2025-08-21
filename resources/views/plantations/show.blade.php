@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('trees.index') }}">Locations</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('trees.location', $plantation->location->id) }}">{{ $plantation->location->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Drive #{{ $plantation->id }}</li>
                        </ol>
                    </nav>
                    <h1><i class="fas fa-seedling"></i> Plantation Drive #{{ $plantation->id }}</h1>
                    <p class="text-muted mb-0">{{ $plantation->location_description }}</p>
                    @if($plantation->landmark)
                        <p class="text-muted"><i class="fas fa-landmark"></i> {{ $plantation->landmark }}</p>
                    @endif
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('plantation-inspections.create', ['plantation' => $plantation->id]) }}" class="btn btn-warning">
                            <i class="fas fa-clipboard-check"></i> Inspect Drive
                        </a>
                        <a href="{{ route('plantations.create') }}" class="btn btn-primary">Create New Drive</a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Plantation Drive Details -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-info-circle"></i> Plantation Drive Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Location</label>
                                        <p class="mb-1"><strong>{{ $plantation->location_description }}</strong></p>
                                        @if($plantation->landmark)
                                            <small class="text-muted"><i class="fas fa-landmark"></i> {{ $plantation->landmark }}</small>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small">Coordinates</label>
                                        <p class="mb-0">
                                            <span class="badge bg-secondary">{{ number_format($plantation->latitude, 6) }}, {{ number_format($plantation->longitude, 6) }}</span>
                                            <a href="https://www.google.com/maps?q={{ $plantation->latitude }},{{ $plantation->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-external-link-alt"></i> View on Map
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Plantation Date</label>
                                        <p class="mb-0"><strong>{{ $plantation->plantation_date->format('d M Y') }}</strong></p>
                                    </div>
                                    
                                    @if($plantation->next_inspection_date)
                                    <div class="mb-3">
                                        <label class="text-muted small">Next Inspection Date</label>
                                        <p class="mb-0">
                                            <span class="badge {{ $plantation->next_inspection_date->isPast() ? 'bg-danger' : 'bg-success' }}">
                                                {{ $plantation->next_inspection_date->format('d M Y') }}
                                            </span>
                                        </p>
                                    </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small">Tree Count</label>
                                        <p class="mb-0"><strong>{{ $plantation->tree_count }} trees</strong></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small">Created By</label>
                                        <p class="mb-0">{{ $plantation->createdBy->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($plantation->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="text-muted small">Description</label>
                                        <p class="mb-0">{{ $plantation->description }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Images -->
                    @if($plantation->images && count($plantation->images) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-images"></i> Location Images</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($plantation->images as $image)
                                <div class="col-6 mb-2">
                                    <img src="{{ Storage::url($image) }}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;" 
                                         data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ Storage::url($image) }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Inspections Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-clipboard-list"></i> Drive Inspections</h5>
                            <a href="{{ route('plantation-inspections.create', ['plantation' => $plantation->id]) }}" class="btn btn-warning">
                                <i class="fas fa-clipboard-check"></i> Create New Inspection
                            </a>
                        </div>
                        <div class="card-body">
                            @if($plantation->inspections && count($plantation->inspections) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Overall Health</th>
                                                <th>Growth Rate</th>
                                                <th>Survival Rate</th>
                                                <th>Issues</th>
                                                <th>Inspector</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($plantation->inspections->sortByDesc('inspection_date') as $inspection)
                                            <tr>
                                                <td>
                                                    <strong>{{ $inspection->inspection_date->format('d M Y') }}</strong><br>
                                                    <small class="text-muted">{{ $inspection->inspection_date->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $inspection->overall_health == 'excellent' ? 'success' : ($inspection->overall_health == 'good' ? 'primary' : ($inspection->overall_health == 'fair' ? 'warning' : 'danger')) }}">
                                                        {{ ucfirst($inspection->overall_health) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $inspection->growth_rate == 'excellent' ? 'success' : ($inspection->growth_rate == 'good' ? 'primary' : ($inspection->growth_rate == 'fair' ? 'warning' : 'danger')) }}">
                                                        {{ ucfirst($inspection->growth_rate) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($inspection->survival_rate)
                                                        <span class="badge bg-{{ $inspection->survival_rate >= 80 ? 'success' : ($inspection->survival_rate >= 60 ? 'warning' : 'danger') }}">
                                                            {{ $inspection->survival_rate }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($inspection->issues_found)
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Issues Found</span>
                                                    @else
                                                        <span class="text-success"><i class="fas fa-check-circle"></i> No Issues</span>
                                                    @endif
                                                </td>
                                                <td>{{ $inspection->inspectedBy->name ?? 'Unknown' }}</td>
                                                <td>
                                                    <a href="{{ route('plantation-inspections.show', $inspection) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Inspections Yet</h5>
                                    <p class="text-muted mb-3">This plantation drive hasn't been inspected yet.</p>
                                    <a href="{{ route('plantation-inspections.create', ['plantation' => $plantation->id]) }}" class="btn btn-warning">
                                        <i class="fas fa-clipboard-check"></i> Create First Inspection
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Location Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Handle image modal
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const imageSrc = button.getAttribute('data-image');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
        });
    }
});
</script>
@endsection