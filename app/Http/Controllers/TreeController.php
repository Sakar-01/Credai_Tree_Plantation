<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TreeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $locations = Location::withCount('trees')
            ->whereHas('trees')
            ->with('trees')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trees.index', compact('locations'));
    }

    public function locationTrees($locationId)
    {
        $location = Location::findOrFail($locationId);

        $trees = Tree::with(['plantedBy', 'latestInspection'])
            ->where('location_id', $location->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('trees.location', compact('trees', 'location'));
    }

    public function create()
    {
        return view('trees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'species' => 'required|string|max:255',
            'location_description' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'plantation_date' => 'required|date',
            'next_inspection_date' => 'required|date|after:plantation_date',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'plantation_survey_file' => 'nullable|file|max:5120',
        ]);

        $photoPath = $request->file('photo')->store('tree-photos', 'public');
        
        $surveyFilePath = null;
        if ($request->hasFile('plantation_survey_file')) {
            $surveyFilePath = $request->file('plantation_survey_file')->store('survey-files', 'public');
        }

        $tree = Tree::create([
            'tree_id' => 'TREE-' . strtoupper(Str::random(8)),
            'species' => $validated['species'],
            'location_description' => $validated['location_description'],
            'landmark' => $validated['landmark'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'plantation_date' => $validated['plantation_date'],
            'next_inspection_date' => $validated['next_inspection_date'],
            'photo_path' => $photoPath,
            'description' => $validated['description'],
            'plantation_survey_file' => $surveyFilePath,
            'planted_by' => auth()->id(),
        ]);

        return redirect()->route('trees.show', $tree)
            ->with('success', 'Tree planted successfully!');
    }

    public function show(Tree $tree)
    {
        $tree->load(['plantedBy', 'inspections.inspectedBy', 'plantationDrive']);

        return view('trees.show', compact('tree'));
    }

    public function edit(Tree $tree)
    {
        return view('trees.edit', compact('tree'));
    }

    public function update(Request $request, Tree $tree)
    {
        $validated = $request->validate([
            'species' => 'required|string|max:255',
            'location_description' => 'required|string|max:255',
            'height' => 'nullable|string|max:255',
            'tree_description' => 'nullable|string',
            'next_inspection_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:planted,under_inspection,healthy,needs_attention,dead',
        ]);

        if ($request->hasFile('photo')) {
            if ($tree->photo_path) {
                Storage::disk('public')->delete($tree->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('tree-photos', 'public');
        }

        $tree->update($validated);

        return redirect()->route('trees.show', $tree)
            ->with('success', 'Tree updated successfully!');
    }

    public function destroy(Tree $tree)
    {
        if ($tree->photo_path) {
            Storage::disk('public')->delete($tree->photo_path);
        }
        
        if ($tree->plantation_survey_file) {
            Storage::disk('public')->delete($tree->plantation_survey_file);
        }

        $tree->delete();

        return redirect()->route('trees.index')
            ->with('success', 'Tree deleted successfully!');
    }
}
