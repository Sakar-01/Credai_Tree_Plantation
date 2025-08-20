@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('trees.index') }}">Trees</a></li>
            @if(isset($plantation))
                <li class="breadcrumb-item"><a href="{{ route('trees.location', $plantation->location->id) }}">{{ $plantation->location->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('drives.show', $plantation) }}">Drive #{{ $plantation->id }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('drives.trees.show', [$plantation, $tree]) }}">{{ $tree->tree_id }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            @else
                @if($tree->location)
                    <li class="breadcrumb-item"><a href="{{ route('trees.location', $tree->location->id) }}">{{ $tree->location->name }}</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('trees.show', $tree) }}">{{ $tree->tree_id }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            @endif
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit: {{ $tree->tree_id }}</h4>
                    @if(isset($plantation))
                        <small class="text-muted">Part of Drive #{{ $plantation->id }}</small>
                    @else
                        <small class="text-muted">Individual Tree</small>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ isset($plantation) ? route('drives.trees.update', [$plantation, $tree]) : route('trees.update', $tree) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="species" class="form-label">Tree Species *</label>
                                <input type="text" class="form-control @error('species') is-invalid @enderror" 
                                       id="species" name="species" value="{{ old('species', $tree->species) }}" required>
                                @error('species')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="planted" {{ old('status', $tree->status) === 'planted' ? 'selected' : '' }}>Planted</option>
                                    <option value="under_inspection" {{ old('status', $tree->status) === 'under_inspection' ? 'selected' : '' }}>Under Inspection</option>
                                    <option value="healthy" {{ old('status', $tree->status) === 'healthy' ? 'selected' : '' }}>Healthy</option>
                                    <option value="needs_attention" {{ old('status', $tree->status) === 'needs_attention' ? 'selected' : '' }}>Needs Attention</option>
                                    <option value="dead" {{ old('status', $tree->status) === 'dead' ? 'selected' : '' }}>Dead</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location_description" class="form-label">Location Description *</label>
                            <input type="text" class="form-control @error('location_description') is-invalid @enderror" 
                                   id="location_description" name="location_description" 
                                   value="{{ old('location_description', $tree->location_description) }}" required>
                            @error('location_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="next_inspection_date" class="form-label">Next Inspection Date *</label>
                            <input type="date" class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                   id="next_inspection_date" name="next_inspection_date" 
                                   value="{{ old('next_inspection_date', $tree->next_inspection_date ? $tree->next_inspection_date->format('Y-m-d') : '') }}" required>
                            @error('next_inspection_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Update Tree Photo (Optional)</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($tree->photo_path)
                                <div class="mt-2">
                                    <small class="text-muted">Current photo:</small><br>
                                    <img src="{{ asset('storage/' . $tree->photo_path) }}" class="img-thumbnail" style="max-width: 200px;" alt="Current Tree Photo">
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $tree->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <h6>Tree Information (Read-only)</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tree ID</label>
                                    <input type="text" class="form-control" value="{{ $tree->tree_id }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Plantation Date</label>
                                    <input type="text" class="form-control" value="{{ $tree->plantation_date->format('M d, Y') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Planted By</label>
                                    <input type="text" class="form-control" value="{{ $tree->plantedBy->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            @if(isset($plantation))
                                <a href="{{ route('drives.trees.show', [$plantation, $tree]) }}" class="btn btn-secondary">Cancel</a>
                            @else
                                <a href="{{ route('trees.show', $tree) }}" class="btn btn-secondary">Cancel</a>
                            @endif
                            <button type="submit" class="btn btn-primary">Update Tree</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection