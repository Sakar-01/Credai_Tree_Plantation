<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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
        
        // Location Analytics Data
        $locationStats = Tree::select('location_description')
            ->selectRaw('COUNT(*) as tree_count')
            ->selectRaw('AVG(latitude) as avg_latitude')
            ->selectRaw('AVG(longitude) as avg_longitude')
            ->groupBy('location_description')
            ->orderBy('tree_count', 'desc')
            ->take(10) // Limit to top 10 locations
            ->get();

        $totalLocations = Tree::distinct('location_description')->count('location_description');
        
        return view('admin.dashboard', compact('stats', 'recentTrees', 'recentInspections', 'overdueInspections', 'locationStats', 'totalLocations'));
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

        // Location-based analytics
        $locationDistribution = Tree::selectRaw('location_description, COUNT(*) as tree_count')
            ->groupBy('location_description')
            ->orderBy('tree_count', 'desc')
            ->take(10)
            ->get();

        $locationTrends = Tree::selectRaw('location_description, DATE(plantation_date) as date, COUNT(*) as count')
            ->where('plantation_date', '>=', $startDate)
            ->groupBy('location_description', 'date')
            ->orderBy('date')
            ->get()
            ->groupBy('location_description');

        // Top 5 locations for trends chart
        $topLocations = $locationDistribution->take(5);
        
        $locationHealthData = DB::table('trees')
            ->join('inspections', function($join) {
                $join->on('trees.id', '=', 'inspections.tree_id')
                     ->where('inspections.inspection_date', '>=', now()->subDays(90));
            })
            ->select('trees.location_description', 'inspections.tree_health', DB::raw('COUNT(*) as count'))
            ->groupBy('trees.location_description', 'inspections.tree_health')
            ->get()
            ->groupBy('location_description');

        return view('admin.analytics', compact(
            'plantationTrends', 
            'inspectionTrends', 
            'healthDistribution', 
            'volunteerStats',
            'locationDistribution',
            'locationTrends',
            'topLocations',
            'locationHealthData',
            'dateRange'
        ));
    }

    public function volunteers()
    {
        $volunteers = User::where('role', 'volunteer')
            ->withCount(['plantedTrees', 'inspections'])
            ->paginate(15);

        $locations = Tree::select('location_description')
            ->distinct()
            ->whereNotNull('location_description')
            ->where('location_description', '!=', '')
            ->orderBy('location_description')
            ->pluck('location_description');

        return view('admin.volunteers', compact('volunteers', 'locations'));
    }

    public function storeVolunteer(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'assigned_region' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'assigned_region' => $request->assigned_region,
            'role' => 'volunteer',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.volunteers')->with('success', 'Volunteer added successfully!');
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
            case 'locations':
                return $this->exportLocations($format);
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

    private function exportLocations($format)
    {
        $locationStats = Tree::selectRaw('location_description, COUNT(*) as tree_count, AVG(latitude) as avg_latitude, AVG(longitude) as avg_longitude')
            ->groupBy('location_description')
            ->orderBy('tree_count', 'desc')
            ->get();
        
        if ($format === 'csv') {
            $filename = 'location_analytics_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($locationStats) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Location', 'Tree Count', 'Average Latitude', 'Average Longitude', 'Percentage of Total']);

                $totalTrees = Tree::count();
                foreach ($locationStats as $location) {
                    $percentage = $totalTrees > 0 ? round(($location->tree_count / $totalTrees) * 100, 2) : 0;
                    fputcsv($file, [
                        $location->location_description,
                        $location->tree_count,
                        number_format($location->avg_latitude, 6),
                        number_format($location->avg_longitude, 6),
                        $percentage . '%'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }
}
