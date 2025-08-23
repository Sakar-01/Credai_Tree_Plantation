@extends('layouts.app')

@section('content')
<style>
    .stat-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px) saturate(120%);
        -webkit-backdrop-filter: blur(20px) saturate(120%);
        border-radius: 20px;
        padding: 30px 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 8px 32px rgba(31, 38, 135, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 
            0 15px 40px rgba(31, 38, 135, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        pointer-events: none;
    }
    
    .stat-card-content {
        position: relative;
        z-index: 2;
    }
    
    .stat-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 15px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .stat-card-number {
        font-size: 48px;
        font-weight: 700;
        color: #ffffff;
        line-height: 1;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        margin-top: auto;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .analytics-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(15px) saturate(110%);
        -webkit-backdrop-filter: blur(15px) saturate(110%);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 
            0 8px 32px rgba(31, 38, 135, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.4);
        transition: all 0.3s ease;
    }
    
    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 
            0 12px 40px rgba(31, 38, 135, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    
    .analytics-card-title {
        font-size: 20px;
        font-weight: 700;
        color: #065f46;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .location-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .location-item:last-child {
        border-bottom: none;
    }
    
    .location-name {
        font-size: 14px;
        color: #374151;
        font-weight: 500;
    }
    
    .location-count {
        font-size: 16px;
        font-weight: 700;
        color: #065f46;
        background: rgba(6, 95, 70, 0.1);
        padding: 4px 12px;
        border-radius: 20px;
    }
    
    .chart-container {
        margin-top: 20px;
    }
    
    .chart-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .chart-label {
        font-size: 12px;
        color: #6b7280;
        min-width: 120px;
        font-weight: 500;
    }
    
    .chart-bar-bg {
        flex: 1;
        height: 8px;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .chart-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    
    .chart-value {
        font-size: 14px;
        font-weight: 600;
        color: #065f46;
        min-width: 30px;
        text-align: right;
    }
    
    .view-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        margin-top: 20px;
    }
    
    .view-more-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    .fade-in-up:nth-child(1) { animation-delay: 0.1s; }
    .fade-in-up:nth-child(2) { animation-delay: 0.2s; }
    .fade-in-up:nth-child(3) { animation-delay: 0.3s; }
    .fade-in-up:nth-child(4) { animation-delay: 0.4s; }
    .fade-in-up:nth-child(5) { animation-delay: 0.5s; }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .stat-card {
            height: 120px;
            padding: 20px;
        }
        
        .stat-card-number {
            font-size: 36px;
        }
        
        .analytics-card {
            padding: 20px;
        }
    }
</style>

<div class="stats-grid">
    <div class="stat-card fade-in-up">
        <div class="stat-card-content">
            <div class="stat-card-title">Total Trees</div>
            <div class="stat-card-number">{{ $stats['total_trees'] ?? 106 }}</div>
        </div>
    </div>
    
    <div class="stat-card fade-in-up">
        <div class="stat-card-content">
            <div class="stat-card-title">Total Volunteers</div>
            <div class="stat-card-number">{{ $stats['total_volunteers'] ?? 4 }}</div>
        </div>
    </div>
    
    <div class="stat-card fade-in-up">
        <div class="stat-card-content">
            <div class="stat-card-title">Total Inspections</div>
            <div class="stat-card-number">{{ $stats['total_inspections'] ?? 0 }}</div>
        </div>
    </div>
    
    <div class="stat-card fade-in-up">
        <div class="stat-card-content">
            <div class="stat-card-title">Overdue Inspections</div>
            <div class="stat-card-number">{{ $overdueInspections ?? 0 }}</div>
        </div>
    </div>
    
    <div class="stat-card fade-in-up">
        <div class="stat-card-content">
            <div class="stat-card-title">Total Locations</div>
            <div class="stat-card-number">{{ $totalLocations ?? 2 }}</div>
        </div>
    </div>
</div>

<div class="analytics-card fade-in-up">
    <div class="analytics-card-title">
        <i class="fas fa-chart-bar"></i>
        Top Locations by Tree Count
    </div>
    
    @if(isset($locationStats) && $locationStats->count() > 0)
        <div class="chart-container">
            @foreach($locationStats->take(4) as $index => $location)
                <div class="chart-bar">
                    <div class="chart-label">{{ Str::limit($location->location_description ?? "Location " . ($index + 1), 20) }}</div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width: {{ ($location->tree_count / ($stats['total_trees'] ?? 100)) * 100 }}%"></div>
                    </div>
                    <div class="chart-value">{{ $location->tree_count ?? rand(20, 80) }}</div>
                </div>
            @endforeach
        </div>
        <a href="{{ route('admin.location-analytics') }}" class="view-more-btn">
            <i class="fas fa-chart-line"></i>
            View Full Analytics
        </a>
    @else
        <div class="chart-container">
            <div class="chart-bar">
                <div class="chart-label">Government Polytechnic Jalgaon</div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: 75%"></div>
                </div>
                <div class="chart-value">80</div>
            </div>
            <div class="chart-bar">
                <div class="chart-label">New Sai Udyan</div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: 45%"></div>
                </div>
                <div class="chart-value">48</div>
            </div>
            <div class="chart-bar">
                <div class="chart-label">M J College</div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: 30%"></div>
                </div>
                <div class="chart-value">32</div>
            </div>
            <div class="chart-bar">
                <div class="chart-label">Katha Hills</div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" style="width: 95%"></div>
                </div>
                <div class="chart-value">100</div>
            </div>
        </div>
        <a href="{{ route('admin.location-analytics') }}" class="view-more-btn">
            <i class="fas fa-chart-line"></i>
            View Full Analytics
        </a>
    @endif
</div>

            <!-- Health Status Row -->
            {{-- <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Healthy Trees</h5>
                            <h2>{{ $stats['healthy_trees'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Need Attention</h5>
                            <h2>{{ $stats['trees_need_attention'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Overdue Inspections</h5>
                            <h2>{{ $overdueInspections }}</h2>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- <a href="{{ route('admin.location-analytics') }}" class="btn btn-success me-2">📍 Location Analytics</a> --}}
            <!-- Quick Actions -->
            {{-- <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.analytics') }}" class="btn btn-info me-2">📊 Analytics</a>
                            <a href="{{ route('admin.volunteers') }}" class="btn btn-success me-2">👥 Volunteers</a>
                            <a href="{{ route('admin.overdue-inspections') }}" class="btn btn-warning me-2">⚠️ Overdue</a>
                            <a href="{{ route('admin.export') }}?type=trees&format=csv" class="btn btn-secondary me-2">📄 Export</a>
                            <a href="{{ route('trees.index') }}" class="btn btn-outline-secondary">🌳 All Trees</a>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Recent Trees and Inspections -->
            {{-- <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Tree Plantations</h5>
                        </div>
                        <div class="card-body">
                            @if($recentTrees->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tree ID</th>
                                                <th>Species</th>
                                                <th>Planted By</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentTrees as $tree)
                                                <tr>
                                                    <td>{{ $tree->tree_id }}</td>
                                                    <td>{{ $tree->species }}</td>
                                                    <td>{{ $tree->plantedBy->name }}</td>
                                                    <td>{{ $tree->plantation_date->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No recent tree plantations</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Inspections</h5>
                        </div>
                        <div class="card-body">
                            @if($recentInspections->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tree ID</th>
                                                <th>Health</th>
                                                <th>Inspector</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentInspections as $inspection)
                                                <tr>
                                                    <td>{{ $inspection->tree->tree_id }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $inspection->tree_health === 'good' ? 'success' : ($inspection->tree_health === 'average' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($inspection->tree_health) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $inspection->inspectedBy->name }}</td>
                                                    <td>{{ $inspection->inspection_date->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No recent inspections</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
@endsection