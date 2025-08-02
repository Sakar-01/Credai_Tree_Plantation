<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Http\Request;

class LocationAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $locationStats = Tree::select('location_description')
            ->selectRaw('COUNT(*) as tree_count')
            ->selectRaw('AVG(latitude) as avg_latitude')
            ->selectRaw('AVG(longitude) as avg_longitude')
            ->groupBy('location_description')
            ->orderBy('tree_count', 'desc')
            ->get();

        $totalTrees = Tree::count();
        $totalLocations = $locationStats->count();

        return view('admin.location-analytics', compact('locationStats', 'totalTrees', 'totalLocations'));
    }

    public function getLocationSuggestions(Request $request)
    {
        $query = $request->get('query');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Tree::select('location_description')
            ->where('location_description', 'LIKE', '%' . $query . '%')
            ->groupBy('location_description')
            ->selectRaw('COUNT(*) as tree_count')
            ->orderBy('tree_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'location' => $item->location_description,
                    'tree_count' => $item->tree_count
                ];
            });

        return response()->json($suggestions);
    }
}