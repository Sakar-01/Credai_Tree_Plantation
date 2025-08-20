@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-clipboard-check"></i> Inspect Plantation Drive</h4>
                    <small class="text-muted">
                        <strong>{{ $plantation->location->name ?? $plantation->location_description }}</strong>
                        @if($plantation->landmark) - Near {{ $plantation->landmark }} @endif
                        <br>
                        Planted: {{ $plantation->plantation_date->format('M d, Y') }} | Trees: {{ $plantation->tree_count }}
                    </small>
                </div>
                <div class="card-body">
                    <form action="{{ route('plantation-inspections.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="plantation_id" value="{{ $plantation->id }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inspection_date" class="form-label">Inspection Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('inspection_date') is-invalid @enderror" 
                                       id="inspection_date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}" required>
                                @error('inspection_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="overall_health" class="form-label">Overall Drive Health <span class="text-danger">*</span></label>
                                <select class="form-select @error('overall_health') is-invalid @enderror" 
                                        id="overall_health" name="overall_health" required>
                                    <option value="">Select Overall Health</option>
                                    <option value="excellent" {{ old('overall_health') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('overall_health') === 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="average" {{ old('overall_health') === 'average' ? 'selected' : '' }}>Average</option>
                                    <option value="poor" {{ old('overall_health') === 'poor' ? 'selected' : '' }}>Poor</option>
                                    <option value="critical" {{ old('overall_health') === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                                @error('overall_health')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="trees_inspected" class="form-label">Trees Inspected <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('trees_inspected') is-invalid @enderror" 
                                       id="trees_inspected" name="trees_inspected" value="{{ old('trees_inspected', 0) }}" 
                                       min="0" max="{{ $plantation->tree_count }}" required>
                                <small class="form-text text-muted">Out of {{ $plantation->tree_count }} total trees</small>
                                @error('trees_inspected')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="healthy_trees" class="form-label">Healthy Trees <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('healthy_trees') is-invalid @enderror" 
                                       id="healthy_trees" name="healthy_trees" value="{{ old('healthy_trees', 0) }}" 
                                       min="0" required>
                                @error('healthy_trees')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="unhealthy_trees" class="form-label">Unhealthy Trees <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('unhealthy_trees') is-invalid @enderror" 
                                       id="unhealthy_trees" name="unhealthy_trees" value="{{ old('unhealthy_trees', 0) }}" 
                                       min="0" required>
                                @error('unhealthy_trees')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Inspection Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required
                                      placeholder="Describe the overall condition of the plantation drive, growth progress, environmental factors, maintenance needs, etc...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Inspection Photos</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="selectFromGallery()">
                                        <i class="fas fa-folder-open"></i> Choose from Gallery
                                    </button>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-outline-success w-100" onclick="captureFromCamera()">
                                        <i class="fas fa-camera"></i> Take Photo
                                    </button>
                                </div>
                            </div>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror d-none" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            <input type="file" class="d-none" 
                                   id="camera_input" accept="image/*" capture="environment">
                            <small class="form-text text-muted">Upload multiple photos showing the overall plantation condition (Max 5 images, 100MB each)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="image_preview" class="mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Image Preview:</label>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllImages()">
                                    <i class="fas fa-times"></i> Clear All Images
                                </button>
                            </div>
                            <div id="preview_container" class="row"></div>
                        </div>

                        <div class="mb-3">
                            <label for="recommendations" class="form-label">Maintenance Recommendations</label>
                            <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                                      id="recommendations" name="recommendations" rows="3"
                                      placeholder="Suggest specific maintenance actions, improvements, or follow-up activities needed...">{{ old('recommendations') }}</textarea>
                            @error('recommendations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="next_inspection_date" class="form-label">Next Inspection Date</label>
                            <input type="date" class="form-control @error('next_inspection_date') is-invalid @enderror" 
                                   id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date') }}">
                            @error('next_inspection_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Recommended based on overall health status</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('plantations.show', $plantation) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-clipboard-check"></i> Record Inspection
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($plantation->images && count($plantation->images) > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6><i class="fas fa-images"></i> Original Plantation Photos ({{ $plantation->plantation_date->format('M d, Y') }})</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($plantation->images as $image)
                                <div class="col-md-3 mb-3">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" alt="Plantation Photo">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
let selectedFiles = [];

function selectFromGallery() {
    document.getElementById('images').click();
}

function captureFromCamera() {
    const cameraInput = document.getElementById('camera_input');
    
    if (navigator.userAgent.match(/Android/i)) {
        cameraInput.setAttribute('capture', 'camera');
        cameraInput.setAttribute('accept', 'image/*;capture=camera');
        
        setTimeout(() => {
            cameraInput.click();
        }, 100);
    } else {
        cameraInput.click();
    }
}

document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    addFilesToSelection(files);
});

document.getElementById('camera_input').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    addFilesToSelection(files);
});

function addFilesToSelection(newFiles) {
    selectedFiles = [...selectedFiles, ...newFiles];
    if (selectedFiles.length > 5) {
        selectedFiles = selectedFiles.slice(0, 5);
        alert('Maximum 5 images allowed. Only first 5 images will be used.');
    }
    updateImagePreview();
}

function updateImagePreview() {
    const previewContainer = document.getElementById('preview_container');
    const imagePreview = document.getElementById('image_preview');
    
    previewContainer.innerHTML = '';
    
    if (selectedFiles.length > 0) {
        imagePreview.style.display = 'block';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                onclick="removeImage(${index})" style="border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-times" style="font-size: 14px; color: white;"></i>
                        </button>
                        <small class="d-block text-center mt-1 text-muted">${file.name}</small>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        imagePreview.style.display = 'none';
    }
    
    updateFileInput();
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    updateImagePreview();
}

function clearAllImages() {
    selectedFiles = [];
    updateImagePreview();
}

function updateFileInput() {
    const fileInput = document.getElementById('images');
    const dt = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    fileInput.files = dt.files;
}

// Auto-calculate total trees and set next inspection date
function updateCalculations() {
    const healthy = parseInt(document.getElementById('healthy_trees').value) || 0;
    const unhealthy = parseInt(document.getElementById('unhealthy_trees').value) || 0;
    document.getElementById('trees_inspected').value = healthy + unhealthy;
}

document.getElementById('healthy_trees').addEventListener('input', updateCalculations);
document.getElementById('unhealthy_trees').addEventListener('input', updateCalculations);

// Set default next inspection date based on overall health
document.getElementById('overall_health').addEventListener('change', function() {
    const today = new Date();
    const nextInspectionDate = new Date(today);
    
    switch(this.value) {
        case 'excellent':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 90); // 3 months
            break;
        case 'good':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 60); // 2 months
            break;
        case 'average':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 30); // 1 month
            break;
        case 'poor':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 14); // 2 weeks
            break;
        case 'critical':
            nextInspectionDate.setDate(nextInspectionDate.getDate() + 7); // 1 week
            break;
    }
    
    document.getElementById('next_inspection_date').value = nextInspectionDate.toISOString().split('T')[0];
});
</script>
@endsection