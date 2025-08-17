<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $inspections = Inspection::with(['tree', 'inspectedBy'])
            ->when(auth()->user()->isVolunteer(), function ($query) {
                return $query->where('inspected_by', auth()->id());
            })
            ->orderBy('inspection_date', 'desc')
            ->paginate(15);

        return view('inspections.index', compact('inspections'));
    }

    public function upcomingInspections()
    {
        $upcomingTrees = Tree::where('next_inspection_date', '<=', now()->addDays(7))
            ->when(auth()->user()->isVolunteer(), function ($query) {
                return $query->where('planted_by', auth()->id());
            })
            ->with(['plantedBy', 'latestInspection'])
            ->orderBy('next_inspection_date', 'asc')
            ->get();

        return view('inspections.upcoming', compact('upcomingTrees'));
    }

    public function upcomingInspectionsByLocation($locationId)
    {
        $location = \App\Models\Location::findOrFail($locationId);
        
        $upcomingTrees = Tree::where('location_id', $locationId)
            ->where('next_inspection_date', '<=', now()->addDays(7))
            ->when(auth()->user()->isVolunteer(), function ($query) {
                return $query->where('planted_by', auth()->id());
            })
            ->with(['plantedBy', 'latestInspection'])
            ->orderBy('next_inspection_date', 'asc')
            ->get();

        return view('inspections.upcoming', compact('upcomingTrees', 'location'));
    }

    public function create(Tree $tree)
    {
        if (auth()->user()->isVolunteer() && $tree->planted_by !== auth()->id()) {
            abort(403, 'You can only inspect trees you have planted.');
        }

        return view('inspections.create', compact('tree'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tree_id' => 'required|exists:trees,id',
            'inspection_date' => 'required|date',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:102400',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'tree_height_cm' => 'nullable|integer|min:1',
            'tree_health' => 'required|in:good,average,poor',
            'observation_notes' => 'nullable|string',
            'next_inspection_date' => 'nullable|date|after:inspection_date',
        ]);

        $tree = Tree::findOrFail($validated['tree_id']);
        
        if (auth()->user()->isVolunteer() && $tree->planted_by !== auth()->id()) {
            abort(403, 'You can only inspect trees you have planted.');
        }

        $photoPath = $request->file('photo')->store('inspection-photos', 'public');

        $inspection = Inspection::create([
            'tree_id' => $validated['tree_id'],
            'inspection_date' => $validated['inspection_date'],
            'photo_path' => $photoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'tree_height_cm' => $validated['tree_height_cm'],
            'tree_health' => $validated['tree_health'],
            'observation_notes' => $validated['observation_notes'],
            'next_inspection_date' => $validated['next_inspection_date'],
            'inspected_by' => auth()->id(),
        ]);

        if ($validated['next_inspection_date']) {
            $tree->update([
                'next_inspection_date' => $validated['next_inspection_date'],
                'status' => match($validated['tree_health']) {
                    'good' => 'healthy',
                    'average' => 'under_inspection',
                    'poor' => 'needs_attention',
                }
            ]);
        }

        return redirect()->route('trees.show', $tree)
            ->with('success', 'Inspection recorded successfully!');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['tree', 'inspectedBy']);
        
        if (auth()->user()->isVolunteer() && $inspection->inspected_by !== auth()->id()) {
            abort(403, 'You can only view inspections you have conducted.');
        }

        return view('inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        if (auth()->user()->isVolunteer() && $inspection->inspected_by !== auth()->id()) {
            abort(403, 'You can only edit inspections you have conducted.');
        }

        return view('inspections.edit', compact('inspection'));
    }

    public function update(Request $request, Inspection $inspection)
    {
        if (auth()->user()->isVolunteer() && $inspection->inspected_by !== auth()->id()) {
            abort(403, 'You can only edit inspections you have conducted.');
        }

        $validated = $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:102400',
            'tree_height_cm' => 'nullable|integer|min:1',
            'tree_health' => 'required|in:good,average,poor',
            'observation_notes' => 'nullable|string',
            'next_inspection_date' => 'nullable|date|after:inspection_date',
        ]);

        if ($request->hasFile('photo')) {
            if ($inspection->photo_path) {
                Storage::disk('public')->delete($inspection->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('inspection-photos', 'public');
        }

        $inspection->update($validated);

        if (isset($validated['next_inspection_date'])) {
            $inspection->tree->update([
                'next_inspection_date' => $validated['next_inspection_date'],
                'status' => match($validated['tree_health']) {
                    'good' => 'healthy',
                    'average' => 'under_inspection',
                    'poor' => 'needs_attention',
                }
            ]);
        }

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection updated successfully!');
    }

    public function destroy(Inspection $inspection)
    {
        if (auth()->user()->isVolunteer()) {
            abort(403, 'Only admins can delete inspections.');
        }

        if ($inspection->photo_path) {
            Storage::disk('public')->delete($inspection->photo_path);
        }

        $inspection->delete();

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted successfully!');
    }
}
