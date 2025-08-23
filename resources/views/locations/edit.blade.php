@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Location</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="locationEditForm" action="{{ route('locations.update', $location) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Location Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $location->name) }}" required
                                   placeholder="e.g., Central Park, School Garden">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <!-- Current Images Section -->
                        @if($location->images && count($location->images) > 0)
                        <div class="mb-3">
                            <label class="form-label">Current Images</label>
                            <div class="row" id="current_images">
                                @foreach($location->images as $index => $image)
                                <div class="col-md-3 mb-2" id="current_image_{{ $index }}">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail" 
                                             style="height: 150px; width: 100%; object-fit: cover;" alt="Location Image">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                                onclick="markImageForRemoval('{{ $image }}', {{ $index }})" 
                                                style="border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times" style="font-size: 14px; color: white;"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Add New Images Section -->
                        <div class="mb-3">
                            <label for="images" class="form-label">Add New Images</label>
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
                                   id="camera_input" accept="image/*" capture="environment" capture="camera">
                            <small class="form-text text-muted">Upload additional images (JPEG, PNG, JPG - Max 100MB each)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Images Preview -->
                        <div id="new_image_preview" class="mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">New Images Preview:</label>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearNewImages()">
                                    <i class="fas fa-times"></i> Clear New Images
                                </button>
                            </div>
                            <div id="new_preview_container" class="row"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Location</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store files to be removed
let imagesToRemove = [];
// Store new selected files
let newSelectedFiles = [];

// Function to mark image for removal
function markImageForRemoval(imagePath, index) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Add to removal list
        imagesToRemove.push(imagePath);
        
        // Update hidden input
        updateRemoveImagesInput();
        
        // Hide the image div
        const imageDiv = document.getElementById('current_image_' + index);
        imageDiv.style.opacity = '0.5';
        imageDiv.querySelector('button').innerHTML = '<i class="fas fa-undo" style="font-size: 14px; color: white;"></i>';
        imageDiv.querySelector('button').onclick = function() { undoImageRemoval(imagePath, index); };
        
        // Add removed class for visual feedback
        imageDiv.classList.add('text-decoration-line-through');
    }
}

// Function to undo image removal
function undoImageRemoval(imagePath, index) {
    // Remove from removal list
    imagesToRemove = imagesToRemove.filter(img => img !== imagePath);
    
    // Update hidden input
    updateRemoveImagesInput();
    
    // Restore the image div
    const imageDiv = document.getElementById('current_image_' + index);
    imageDiv.style.opacity = '1';
    imageDiv.querySelector('button').innerHTML = '<i class="fas fa-times" style="font-size: 14px; color: white;"></i>';
    imageDiv.querySelector('button').onclick = function() { markImageForRemoval(imagePath, index); };
    
    // Remove the crossed-out class
    imageDiv.classList.remove('text-decoration-line-through');
}

// Update hidden input with images to remove
function updateRemoveImagesInput() {
    const form = document.getElementById('locationEditForm');
    
    // Remove existing hidden inputs
    const existingInputs = form.querySelectorAll('input[name="remove_images[]"]');
    existingInputs.forEach(input => input.remove());
    
    // Add new hidden inputs for each image to remove
    imagesToRemove.forEach(imagePath => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_images[]';
        input.value = imagePath;
        form.appendChild(input);
    });
}

// New image selection functions
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

// New image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    addNewFilesToSelection(files);
});

document.getElementById('camera_input').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    addNewFilesToSelection(files);
});

function addNewFilesToSelection(newFiles) {
    newSelectedFiles = [...newSelectedFiles, ...newFiles];
    updateNewImagePreview();
}

function updateNewImagePreview() {
    const previewContainer = document.getElementById('new_preview_container');
    const imagePreview = document.getElementById('new_image_preview');
    
    previewContainer.innerHTML = '';
    
    if (newSelectedFiles.length > 0) {
        imagePreview.style.display = 'block';
        
        newSelectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                onclick="removeNewImage(${index})" style="border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
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
    
    // Update the file input
    updateNewFileInput();
}

function removeNewImage(index) {
    newSelectedFiles.splice(index, 1);
    updateNewImagePreview();
}

function clearNewImages() {
    newSelectedFiles = [];
    updateNewImagePreview();
}

function updateNewFileInput() {
    const fileInput = document.getElementById('images');
    const dt = new DataTransfer();
    
    newSelectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    fileInput.files = dt.files;
}
</script>
@endsection