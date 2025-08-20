<?php

namespace App\Http\Controllers;

use App\Models\Plantation;
use App\Models\PlantationInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlantationInspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $inspections = PlantationInspection::with(['plantation', 'inspectedBy'])
            ->when(auth()->user()->isVolunteer(), function ($query) {
                return $query->where('inspected_by', auth()->id());
            })
            ->orderBy('inspection_date', 'desc')
            ->paginate(15);

        return view('plantation-inspections.index', compact('inspections'));
    }

    public function create(Plantation $plantation)
    {
        if (auth()->user()->isVolunteer() && $plantation->created_by !== auth()->id()) {
            abort(403, 'You can only inspect plantation drives you have created.');
        }

        return view('plantation-inspections.create', compact('plantation'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plantation_id' => 'required|exists:plantations,id',
            'inspection_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:102400',
            'next_inspection_date' => 'nullable|date|after:inspection_date',
            'overall_health' => 'required|in:excellent,good,average,poor,critical',
            'trees_inspected' => 'required|integer|min:0',
            'healthy_trees' => 'required|integer|min:0',
            'unhealthy_trees' => 'required|integer|min:0',
            'recommendations' => 'nullable|string|max:2000',
        ]);

        $plantation = Plantation::findOrFail($validated['plantation_id']);
        
        if (auth()->user()->isVolunteer() && $plantation->created_by !== auth()->id()) {
            abort(403, 'You can only inspect plantation drives you have created.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('plantation-inspection-photos', 'public');
            }
        }

        $inspection = PlantationInspection::create([
            'plantation_id' => $validated['plantation_id'],
            'inspection_date' => $validated['inspection_date'],
            'description' => $validated['description'],
            'images' => $imagePaths,
            'next_inspection_date' => $validated['next_inspection_date'],
            'overall_health' => $validated['overall_health'],
            'trees_inspected' => $validated['trees_inspected'],
            'healthy_trees' => $validated['healthy_trees'],
            'unhealthy_trees' => $validated['unhealthy_trees'],
            'recommendations' => $validated['recommendations'],
            'inspected_by' => auth()->id(),
        ]);

        return redirect()->route('plantations.show', $plantation)
            ->with('success', 'Plantation drive inspection recorded successfully!');
    }

    public function show(PlantationInspection $plantationInspection)
    {
        $plantationInspection->load(['plantation', 'inspectedBy']);
        
        if (auth()->user()->isVolunteer() && $plantationInspection->inspected_by !== auth()->id()) {
            abort(403, 'You can only view inspections you have conducted.');
        }

        return view('plantation-inspections.show', compact('plantationInspection'));
    }

    public function edit(PlantationInspection $plantationInspection)
    {
        if (auth()->user()->isVolunteer() && $plantationInspection->inspected_by !== auth()->id()) {
            abort(403, 'You can only edit inspections you have conducted.');
        }

        return view('plantation-inspections.edit', compact('plantationInspection'));
    }

    public function update(Request $request, PlantationInspection $plantationInspection)
    {
        if (auth()->user()->isVolunteer() && $plantationInspection->inspected_by !== auth()->id()) {
            abort(403, 'You can only edit inspections you have conducted.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:102400',
            'next_inspection_date' => 'nullable|date|after:inspection_date',
            'overall_health' => 'required|in:excellent,good,average,poor,critical',
            'trees_inspected' => 'required|integer|min:0',
            'healthy_trees' => 'required|integer|min:0',
            'unhealthy_trees' => 'required|integer|min:0',
            'recommendations' => 'nullable|string|max:2000',
        ]);

        $imagePaths = $plantationInspection->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($imagePaths as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            
            // Store new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('plantation-inspection-photos', 'public');
            }
        }

        $plantationInspection->update([
            'description' => $validated['description'],
            'images' => $imagePaths,
            'next_inspection_date' => $validated['next_inspection_date'],
            'overall_health' => $validated['overall_health'],
            'trees_inspected' => $validated['trees_inspected'],
            'healthy_trees' => $validated['healthy_trees'],
            'unhealthy_trees' => $validated['unhealthy_trees'],
            'recommendations' => $validated['recommendations'],
        ]);

        return redirect()->route('plantation-inspections.show', $plantationInspection)
            ->with('success', 'Plantation inspection updated successfully!');
    }

    public function destroy(PlantationInspection $plantationInspection)
    {
        if (auth()->user()->isVolunteer()) {
            abort(403, 'Only admins can delete inspections.');
        }

        if ($plantationInspection->images) {
            foreach ($plantationInspection->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $plantationInspection->delete();

        return redirect()->route('plantation-inspections.index')
            ->with('success', 'Plantation inspection deleted successfully!');
    }
}
