<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tree;
use App\Models\Plantation;
use App\Models\Landmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:102400',
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
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:102400',
        ]);
        
        // Validate that the coordinates are within 500m of the location center
        $distance = $this->calculateDistance(
            $location->latitude, $location->longitude,
            $validated['latitude'], $validated['longitude']
        );
        
        if ($distance > 500) { // 500 meters
            return back()->withErrors([
                'latitude' => 'The selected location must be within 500 meters of the designated area.',
                'longitude' => 'The selected location must be within 500 meters of the designated area.'
            ])->withInput();
        }

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

    public function plantationDriveForm(Location $location)
    {
        if (auth()->user()->isVolunteer()) {
            // Volunteers can create plantation drives in any location
        }

        $location->load('landmarks');
        
        return view('locations.plantation-drive', compact('location'));
    }

    public function storePlantationDrive(Request $request, Location $location)
    {
        $validated = $request->validate([
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'plantation_date' => 'required|date',
            'next_inspection_date' => 'required|date|after_or_equal:today',
            'tree_count' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:102400',
        ]);
        
        // Validate that the coordinates are within 500m of the location center
        $distance = $this->calculateDistance(
            $location->latitude, $location->longitude,
            $validated['latitude'], $validated['longitude']
        );
        
        if ($distance > 500) { // 500 meters
            return back()->withErrors([
                'latitude' => 'The selected location must be within 500 meters of the designated area.',
                'longitude' => 'The selected location must be within 500 meters of the designated area.'
            ])->withInput();
        }

        // Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('plantation-photos', 'public');
            }
        }

        // Create landmark if provided
        $landmarkId = null;
        if (!empty($validated['landmark'])) {
            $landmark = Landmark::firstOrCreate([
                'name' => $validated['landmark'],
                'location_id' => $location->id,
            ], [
                'description' => 'Landmark in ' . $location->name,
            ]);
            $landmarkId = $landmark->id;
        }

        // Create plantation drive
        $plantation = Plantation::create([
            'location_id' => $location->id,
            'landmark_id' => $landmarkId,
            'location_description' => $location->description,
            'landmark' => $validated['landmark'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'plantation_date' => $validated['plantation_date'],
            'next_inspection_date' => $validated['next_inspection_date'],
            'tree_count' => $validated['tree_count'],
            'description' => $validated['description'],
            'images' => $imagePaths,
            'created_by' => auth()->id(),
        ]);

        // Create multiple trees for this plantation drive
        for ($i = 1; $i <= $validated['tree_count']; $i++) {
            Tree::create([
                'tree_id' => 'TREE-' . strtoupper(Str::random(8)),
                'plantation_id' => $plantation->id,
                'location_id' => $location->id,
                'landmark_id' => $landmarkId,
                'location_description' => $location->description,
                'landmark' => $validated['landmark'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'plantation_date' => $validated['plantation_date'],
                'planted_by' => auth()->id(),
                'status' => 'planted',
            ]);
        }

        return redirect()->route('plantations.show', $plantation)
            ->with('success', "Plantation drive created successfully in {$location->name}! {$validated['tree_count']} trees have been planted.");
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:102400',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'nullable|string'
        ]);


        // Handle image removal
        $currentImages = $location->images ?? [];
        $removeImages = array_filter($validated['remove_images'] ?? [], function($image) {
            return !empty($image);
        });
        
        foreach ($removeImages as $imageToRemove) {
            // Remove from storage
            if (Storage::disk('public')->exists($imageToRemove)) {
                Storage::disk('public')->delete($imageToRemove);
            }
            // Remove from array
            $currentImages = array_filter($currentImages, function($image) use ($imageToRemove) {
                return $image !== $imageToRemove;
            });
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('location-images', 'public');
                $currentImages[] = $path;
            }
        }

        // Update location
        $location->update([
            'name' => $validated['name'],
            'images' => array_values($currentImages), // Re-index array
        ]);

        return redirect()->route('trees.location', $location)->with('success', 'Location updated successfully!');
    }
    
    /**
     * Calculate distance between two coordinates in meters using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth's radius in meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}