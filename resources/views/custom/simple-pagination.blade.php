@if ($paginator->hasPages())
    <div class="simple-pagination">
        <div class="pagination-info">
            <span>Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results</span>
        </div>
        
        <div class="pagination-controls">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn disabled">
                    <i class="fas fa-chevron-left"></i>
                    <span class="btn-text">Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                    <span class="btn-text">Previous</span>
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">
                    <span class="btn-text">Next</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn disabled">
                    <span class="btn-text">Next</span>
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>

    <style>
        .simple-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin: 30px 0;
        }
        
        .pagination-info {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            color: #065f46;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            min-width: 120px;
            height: 48px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .pagination-btn:hover:not(.disabled) {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(16, 185, 129, 0.3);
            color: #047857;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.15);
        }
        
        .pagination-btn.disabled {
            background: rgba(255, 255, 255, 0.5);
            border-color: rgba(255, 255, 255, 0.2);
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .pagination-btn i {
            font-size: 14px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .simple-pagination {
                gap: 12px;
            }
            
            .pagination-controls {
                gap: 12px;
            }
            
            .pagination-btn {
                padding: 10px 20px;
                min-width: 100px;
                height: 44px;
                font-size: 13px;
            }
            
            .pagination-info {
                font-size: 13px;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .pagination-btn {
                padding: 8px 16px;
                min-width: 80px;
                height: 40px;
                font-size: 12px;
            }
            
            .pagination-btn .btn-text {
                display: none;
            }
            
            .pagination-btn i {
                font-size: 16px;
            }
        }
    </style>
@endif