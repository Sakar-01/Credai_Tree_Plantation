<div class="col-md-4 mb-4">
    <div class="card h-100">
        @if($tree->images && count($tree->images) > 0)
            <div id="treeCarousel{{ $tree->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($tree->images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image) }}" 
                                 class="d-block w-100 card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="Tree Photo {{ $index + 1 }}">
                        </div>
                    @endforeach
                </div>
                @if(count($tree->images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#treeCarousel{{ $tree->id }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#treeCarousel{{ $tree->id }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-dark bg-opacity-75">{{ count($tree->images) }} photos</span>
                    </div>
                @endif
            </div>
        @elseif($tree->photo_path)
            <img src="{{ asset('storage/' . $tree->photo_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Tree Photo">
        @else
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                <i class="fas fa-tree fa-3x text-muted"></i>
            </div>
        @endif
        
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">
                <strong>{{ $tree->tree_id }}</strong>
                @if(isset($showPlantationInfo) && $showPlantationInfo && $tree->plantation_id)
                    <small class="text-muted d-block">
                        <i class="fas fa-seedling"></i> Plantation Drive
                    </small>
                @endif
            </h5>
            <p class="card-text flex-grow-1">
                <strong>Species:</strong> 
                @if($tree->species)
                    {{ $tree->species }}
                @else
                    <span class="text-warning">Not specified</span>
                    <small class="d-block text-muted">Click edit to add species</small>
                @endif
                <br>
                
                @if($tree->height)
                    <strong>Height:</strong> {{ $tree->height }} cm<br>
                @endif
                
                @if($tree->landmark_id && is_object($tree->landmark))
                    <strong>Landmark:</strong> {{ $tree->landmark->name }}<br>
                @elseif($tree->landmark)
                    <strong>Landmark:</strong> {{ $tree->landmark }}<br>
                @endif
                
                <strong>Planted:</strong> {{ $tree->plantation_date->format('M d, Y') }}<br>
                
                <strong>Status:</strong>
                <span class="badge bg-{{ $tree->status === 'healthy' ? 'success' : ($tree->status === 'needs_attention' ? 'danger' : 'secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $tree->status)) }}
                </span>
            </p>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('trees.show', $tree) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('trees.edit', $tree) }}" class="btn btn-sm btn-primary">Edit</a>
                </div>
                @if($tree->next_inspection_date && $tree->next_inspection_date <= now())
                    <a href="{{ route('trees.inspect', $tree) }}" class="btn btn-sm btn-warning">Inspect</a>
                @endif
            </div>
        </div>
        @if($tree->next_inspection_date)
            <div class="card-footer text-muted small">
                Next inspection: {{ $tree->next_inspection_date->format('M d, Y') }}
            </div>
        @else
            <div class="card-footer text-muted small">
                <em>Next inspection date not set</em>
            </div>
        @endif
    </div>
</div>