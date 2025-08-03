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
                            <li class="breadcrumb-item active" aria-current="page">{{ $location->name }}</li>
                        </ol>
                    </nav>
                    <h1>Plantation Drives in {{ $location->name }}</h1>
                    @if($location->landmark)
                        <p class="text-muted"><i class="fas fa-landmark"></i> {{ $location->landmark }}</p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('plantation-drives.create') }}" class="btn btn-primary">Create Plantation Drive</a>
                    <a href="{{ route('inspections.upcoming') }}" class="btn btn-warning">Upcoming Inspections</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($drives->count() > 0)
                <div class="row">
                    @foreach($drives as $drive)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                @if($drive->images && count($drive->images) > 0)
                                    <div id="carousel{{ $drive->id }}" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach($drive->images as $index => $image)
                                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 250px; object-fit: cover;" alt="Drive Photo">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(count($drive->images) > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $drive->id }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $drive->id }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $drive->title }}</h5>
                                    <p class="card-text">
                                        <strong>Trees:</strong> {{ $drive->number_of_trees }}<br>
                                        <strong>Planted:</strong> {{ $drive->plantation_date->format('M d, Y') }}<br>
                                        <strong>By:</strong> {{ $drive->createdBy->name }}<br>
                                        <strong>Species:</strong> 
                                        @php
                                            $species = $drive->trees->whereNotNull('species')->pluck('species')->unique();
                                        @endphp
                                        @if($species->count() > 0)
                                            {{ $species->implode(', ') }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </p>
                                    @if($drive->description)
                                        <p class="card-text text-muted">{{ Str::limit($drive->description, 100) }}</p>
                                    @endif
                                    <div class="mt-3">
                                        <span class="badge bg-primary">{{ $drive->drive_id }}</span>
                                        <span class="badge bg-{{ $drive->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($drive->status) }}</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('plantation-drives.show', $drive) }}" class="btn btn-sm btn-primary">View Details</a>
                                        <a href="{{ route('plantation-drives.trees', $drive) }}" class="btn btn-sm btn-success">View Trees</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center">
                    <h3>No plantation drives in this location yet</h3>
                    <p class="text-muted">Be the first to create a plantation drive here!</p>
                    <a href="{{ route('plantation-drives.create') }}" class="btn btn-primary btn-lg">Create Plantation Drive</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all carousels manually if Bootstrap JS is loaded
    if (typeof bootstrap !== 'undefined') {
        const carouselElements = document.querySelectorAll('.carousel');
        carouselElements.forEach(function(carouselElement) {
            new bootstrap.Carousel(carouselElement, {
                interval: 4000,
                wrap: true
            });
        });
    }
});
</script>
@endsection