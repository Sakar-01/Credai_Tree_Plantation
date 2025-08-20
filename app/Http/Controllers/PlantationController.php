<?php

namespace App\Http\Controllers;

use App\Models\Plantation;
use App\Models\Tree;
use App\Models\Location;
use App\Models\Landmark;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlantationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('plantations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_description' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'plantation_date' => 'required|date',
            'next_inspection_date' => 'nullable|date|after_or_equal:today',
            'tree_count' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:102400',
        ]);

        // Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('plantation-photos', 'public');
            }
        }

        // Find or create location based on coordinates and description
        $location = Location::firstOrCreate([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ], [
            'name' => $validated['location_description'],
            'description' => $validated['location_description'],
        ]);

        // Create landmark if provided
        $landmarkId = null;
        if (!empty($validated['landmark'])) {
            $landmark = Landmark::firstOrCreate([
                'name' => $validated['landmark'],
                'location_id' => $location->id,
            ], [
                'description' => 'Landmark near ' . $validated['location_description'],
            ]);
            $landmarkId = $landmark->id;
        }

        // Create plantation drive
        $plantation = Plantation::create([
            'location_id' => $location->id,
            'landmark_id' => $landmarkId,
            'location_description' => $validated['location_description'],
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
                'location_description' => $validated['location_description'],
                'landmark' => $validated['landmark'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'plantation_date' => $validated['plantation_date'],
                'planted_by' => auth()->id(),
                'status' => 'planted',
            ]);
        }

        return redirect()->route('plantations.show', $plantation)
            ->with('success', "Plantation drive created successfully! {$validated['tree_count']} trees have been planted.");
    }

    public function trees(Plantation $plantation)
    {
        if (auth()->user()->isVolunteer() && $plantation->created_by !== auth()->id()) {
            abort(403, 'You can only view plantation drives you have created.');
        }

        $plantation->load(['location', 'landmark', 'createdBy', 'inspections.inspectedBy']);
        
        $trees = Tree::with(['latestInspection'])
            ->where('plantation_id', $plantation->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('plantations.trees', compact('plantation', 'trees'));
    }

}
