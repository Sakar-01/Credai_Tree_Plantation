<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Check if location already exists
        $existingLocation = Location::where('latitude', $validated['latitude'])
            ->where('longitude', $validated['longitude'])
            ->first();

        if ($existingLocation) {
            return redirect()->route('locations.plant-tree', $existingLocation->id)
                ->with('info', 'This location already exists! You can plant a tree here.');
        }

        // Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('location-images', 'public');
                $imagePaths[] = $path;
            }
        }

        $location = Location::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'images' => $imagePaths,
        ]);

        return redirect()->route('locations.plant-tree', $location->id)
            ->with('success', 'Location added successfully! Now plant your first tree here.');
    }

    public function plantTreeForm($locationId)
    {
        $location = Location::with('landmarks')->findOrFail($locationId);
        return view('locations.plant-tree', compact('location'));
    }

    public function plantTree(Request $request, $locationId)
    {
        $location = Location::findOrFail($locationId);
        
        $validated = $request->validate([
            'species' => 'nullable|string|max:255',
            'height' => 'nullable|numeric|min:0|max:99999',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'landmark' => 'nullable|string|max:255',
            'plantation_date' => 'required|date',
            'next_inspection_date' => 'required|date|after:plantation_date',
            'description' => 'nullable|string',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Handle multiple image uploads for tree
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('tree-photos', 'public');
                $imagePaths[] = $path;
            }
        }

        // Use the first image as the main photo_path for backward compatibility
        $mainPhotoPath = count($imagePaths) > 0 ? $imagePaths[0] : null;

        // Create landmark if provided
        $landmarkId = null;
        if (!empty($validated['landmark'])) {
            $landmark = \App\Models\Landmark::firstOrCreate([
                'name' => $validated['landmark'],
                'location_id' => $location->id,
            ], [
                'description' => 'Landmark near ' . $location->name,
            ]);
            $landmarkId = $landmark->id;
        }

        $tree = Tree::create([
            'tree_id' => 'TREE-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'species' => $validated['species'],
            'height' => $validated['height'],
            'location_id' => $location->id,
            'landmark_id' => $landmarkId,
            'location_description' => $location->name,
            'landmark' => $validated['landmark'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'plantation_date' => $validated['plantation_date'],
            'next_inspection_date' => $validated['next_inspection_date'],
            'photo_path' => $mainPhotoPath,
            'images' => $imagePaths,
            'description' => $validated['description'],
            'planted_by' => auth()->id(),
        ]);

        return redirect()->route('trees.show', $tree)
            ->with('success', 'Tree planted successfully!');
    }

    public function checkDuplicate(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        
        if (!$lat || !$lng) {
            return response()->json(['duplicate' => false]);
        }
        
        // Check for locations within ~100 meters (approximately 0.001 degrees)
        $tolerance = 0.001;
        
        $existingLocation = Location::where('latitude', '>=', $lat - $tolerance)
            ->where('latitude', '<=', $lat + $tolerance)
            ->where('longitude', '>=', $lng - $tolerance)
            ->where('longitude', '<=', $lng + $tolerance)
            ->first();
            
        if ($existingLocation) {
            return response()->json([
                'duplicate' => true,
                'location' => [
                    'id' => $existingLocation->id,
                    'name' => $existingLocation->name,
                    'description' => $existingLocation->description,
                    'latitude' => $existingLocation->latitude,
                    'longitude' => $existingLocation->longitude,
                ]
            ]);
        }
        
        return response()->json(['duplicate' => false]);
    }
}