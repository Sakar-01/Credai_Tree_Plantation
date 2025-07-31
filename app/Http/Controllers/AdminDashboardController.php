<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentTrees = Tree::with('plantedBy')->latest()->take(5)->get();
        $recentInspections = Inspection::with(['tree', 'inspectedBy'])->latest()->take(5)->get();
        $overdueInspections = Tree::where('next_inspection_date', '<', now())->count();
        
        return view('admin.dashboard', compact('stats', 'recentTrees', 'recentInspections', 'overdueInspections'));
    }

    public function analytics(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $startDate = now()->subDays($dateRange);
        
        $plantationTrends = Tree::selectRaw('DATE(plantation_date) as date, COUNT(*) as count')
            ->where('plantation_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $inspectionTrends = Inspection::selectRaw('DATE(inspection_date) as date, COUNT(*) as count')
            ->where('inspection_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $healthDistribution = Inspection::selectRaw('tree_health, COUNT(*) as count')
            ->where('inspection_date', '>=', $startDate)
            ->groupBy('tree_health')
            ->get();

        $volunteerStats = User::where('role', 'volunteer')
            ->withCount(['plantedTrees', 'inspections'])
            ->orderBy('planted_trees_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.analytics', compact(
            'plantationTrends', 
            'inspectionTrends', 
            'healthDistribution', 
            'volunteerStats',
            'dateRange'
        ));
    }

    public function volunteers()
    {
        $volunteers = User::where('role', 'volunteer')
            ->withCount(['plantedTrees', 'inspections'])
            ->paginate(15);

        return view('admin.volunteers', compact('volunteers'));
    }

    public function overdueInspections()
    {
        $overdueTrees = Tree::where('next_inspection_date', '<', now())
            ->with(['plantedBy', 'latestInspection'])
            ->orderBy('next_inspection_date', 'asc')
            ->paginate(15);

        return view('admin.overdue-inspections', compact('overdueTrees'));
    }

    public function mapView()
    {
        $trees = Tree::with(['plantedBy', 'latestInspection'])
            ->select('id', 'tree_id', 'species', 'location_description', 'latitude', 'longitude', 'status', 'plantation_date', 'planted_by')
            ->get();

        $stats = [
            'total_trees' => $trees->count(),
            'healthy_trees' => $trees->where('status', 'healthy')->count(),
            'needs_attention' => $trees->where('status', 'needs_attention')->count(),
            'under_inspection' => $trees->where('status', 'under_inspection')->count(),
            'planted' => $trees->where('status', 'planted')->count(),
        ];

        return view('admin.map', compact('trees', 'stats'));
    }

    public function exportData(Request $request)
    {
        $type = $request->get('type', 'trees');
        $format = $request->get('format', 'csv');

        switch ($type) {
            case 'trees':
                return $this->exportTrees($format);
            case 'inspections':
                return $this->exportInspections($format);
            case 'volunteers':
                return $this->exportVolunteers($format);
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }

    private function getDashboardStats()
    {
        return [
            'total_trees' => Tree::count(),
            'total_volunteers' => User::where('role', 'volunteer')->count(),
            'total_inspections' => Inspection::count(),
            'trees_this_month' => Tree::whereMonth('plantation_date', now()->month)->count(),
            'healthy_trees' => Tree::where('status', 'healthy')->count(),
            'trees_need_attention' => Tree::where('status', 'needs_attention')->count(),
            'overdue_inspections' => Tree::where('next_inspection_date', '<', now())->count(),
            'upcoming_inspections' => Tree::whereBetween('next_inspection_date', [now(), now()->addDays(7)])->count(),
        ];
    }

    private function exportTrees($format)
    {
        $trees = Tree::with(['plantedBy', 'latestInspection'])->get();
        
        if ($format === 'csv') {
            $filename = 'trees_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($trees) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Tree ID', 'Species', 'Location', 'Plantation Date', 'Status', 'Planted By', 'Last Inspection']);

                foreach ($trees as $tree) {
                    fputcsv($file, [
                        $tree->tree_id,
                        $tree->species,
                        $tree->location_description,
                        $tree->plantation_date->format('Y-m-d'),
                        $tree->status,
                        $tree->plantedBy->name,
                        $tree->latestInspection->first()?->inspection_date?->format('Y-m-d') ?? 'N/A'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }

    private function exportInspections($format)
    {
        $inspections = Inspection::with(['tree', 'inspectedBy'])->get();
        
        if ($format === 'csv') {
            $filename = 'inspections_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($inspections) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Tree ID', 'Inspection Date', 'Height (cm)', 'Health', 'Inspected By', 'Notes']);

                foreach ($inspections as $inspection) {
                    fputcsv($file, [
                        $inspection->tree->tree_id,
                        $inspection->inspection_date->format('Y-m-d'),
                        $inspection->tree_height_cm ?? 'N/A',
                        $inspection->tree_health,
                        $inspection->inspectedBy->name,
                        $inspection->observation_notes ?? ''
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }

    private function exportVolunteers($format)
    {
        $volunteers = User::where('role', 'volunteer')
            ->withCount(['plantedTrees', 'inspections'])
            ->get();
        
        if ($format === 'csv') {
            $filename = 'volunteers_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($volunteers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Name', 'Email', 'Phone', 'Region', 'Trees Planted', 'Inspections Conducted']);

                foreach ($volunteers as $volunteer) {
                    fputcsv($file, [
                        $volunteer->name,
                        $volunteer->email,
                        $volunteer->phone ?? 'N/A',
                        $volunteer->assigned_region ?? 'N/A',
                        $volunteer->planted_trees_count,
                        $volunteer->inspections_count
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }
}
