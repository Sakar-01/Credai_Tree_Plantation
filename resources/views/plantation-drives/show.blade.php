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
                            <li class="breadcrumb-item active" aria-current="page">{{ $plantationDrive->title }}</li>
                        </ol>
                    </nav>
                    <h1>{{ $plantationDrive->title }}</h1>
                </div>
                <div>
                    <a href="{{ route('plantation-drives.trees', $plantationDrive) }}" class="btn btn-success">View Trees</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    @if($plantationDrive->images && count($plantationDrive->images) > 0)
                        <div id="plantationCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($plantationDrive->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 rounded" style="height: 400px; object-fit: cover;" alt="Plantation Drive Photo">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($plantationDrive->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#plantationCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#plantationCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                                <div class="carousel-indicators">
                                    @foreach($plantationDrive->images as $index => $image)
                                        <button type="button" data-bs-target="#plantationCarousel" data-bs-slide-to="{{ $index }}" {{ $index === 0 ? 'class="active"' : '' }}></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($plantationDrive->description)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Description</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $plantationDrive->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Drive Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Drive ID:</strong></td>
                                    <td><code>{{ $plantationDrive->drive_id }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>{{ $plantationDrive->location->name }}</td>
                                </tr>
                                @if($plantationDrive->location->landmark)
                                <tr>
                                    <td><strong>Landmark:</strong></td>
                                    <td>{{ $plantationDrive->location->landmark }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Species:</strong></td>
                                    <td>
                                        @php
                                            $species = $plantationDrive->trees->whereNotNull('species')->pluck('species')->unique();
                                        @endphp
                                        @if($species->count() > 0)
                                            {{ $species->implode(', ') }}
                                        @else
                                            <span class="text-muted">Not specified yet</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Trees:</strong></td>
                                    <td>{{ $plantationDrive->number_of_trees }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Planted:</strong></td>
                                    <td>{{ $plantationDrive->plantation_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Next Inspection:</strong></td>
                                    <td>{{ $plantationDrive->next_inspection_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $plantationDrive->createdBy->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $plantationDrive->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($plantationDrive->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($plantationDrive->latitude && $plantationDrive->longitude)
                                <tr>
                                    <td><strong>Coordinates:</strong></td>
                                    <td>{{ $plantationDrive->latitude }}, {{ $plantationDrive->longitude }}</td>
                                </tr>
                                @endif
                            </table>

                            @if($plantationDrive->plantation_survey_file)
                                <div class="mt-3">
                                    <a href="{{ asset('storage/' . $plantationDrive->plantation_survey_file) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-download"></i> Survey File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Tree Status Summary</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $statusCounts = $plantationDrive->trees->groupBy('status')->map->count();
                            @endphp
                            @foreach($statusCounts as $status => $count)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-{{ $status === 'healthy' ? 'success' : ($status === 'needs_attention' ? 'danger' : ($status === 'planted' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                    <strong>{{ $count }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel - works for both Bootstrap 4 and 5
    const carouselElement = document.querySelector('#plantationCarousel');
    if (carouselElement) {
        // Try Bootstrap 5 first
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Carousel(carouselElement, {
                interval: 5000,
                wrap: true
            });
        }
        // Fallback to Bootstrap 4/jQuery if available
        else if (typeof $ !== 'undefined' && $.fn.carousel) {
            $('#plantationCarousel').carousel({
                interval: 5000,
                wrap: true
            });
        }
        // Manual carousel functionality if Bootstrap JS not available
        else {
            const items = carouselElement.querySelectorAll('.carousel-item');
            const prevBtn = carouselElement.querySelector('.carousel-control-prev');
            const nextBtn = carouselElement.querySelector('.carousel-control-next');
            let currentIndex = 0;

            function showSlide(index) {
                items.forEach((item, i) => {
                    item.classList.toggle('active', i === index);
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                    showSlide(currentIndex);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                    showSlide(currentIndex);
                });
            }

            // Auto-advance slides
            setInterval(() => {
                currentIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                showSlide(currentIndex);
            }, 5000);
        }
    }
});
</script>
@endsection