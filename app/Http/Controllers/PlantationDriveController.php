<?php

namespace App\Http\Controllers;

use App\Models\PlantationDrive;
use App\Models\Location;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlantationDriveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $locations = Location::withCount('plantationDrives')
            ->whereHas('plantationDrives')
            ->with('plantationDrives')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('plantation-drives.index', compact('locations'));
    }

    public function locationDrives($locationId)
    {
        $location = Location::findOrFail($locationId);
        $drives = PlantationDrive::with(['createdBy', 'trees'])
            ->where('location_id', $location->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('plantation-drives.location', compact('location', 'drives'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('plantation-drives.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_name' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'number_of_trees' => 'required|integer|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'plantation_date' => 'required|date',
            'next_inspection_date' => 'required|date|after:plantation_date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'plantation_survey_file' => 'nullable|file|max:5120',
        ]);

        // Find or create location
        $location = Location::firstOrCreate(
            [
                'name' => $validated['location_name'],
                'landmark' => $validated['landmark']
            ],
            [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude']
            ]
        );

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('plantation-drive-photos', 'public');
            }
        }

        $surveyFilePath = null;
        if ($request->hasFile('plantation_survey_file')) {
            $surveyFilePath = $request->file('plantation_survey_file')->store('survey-files', 'public');
        }

        $drive = PlantationDrive::create([
            'drive_id' => 'DRIVE-' . strtoupper(Str::random(8)),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location_id' => $location->id,
            'number_of_trees' => $validated['number_of_trees'],
            'images' => $imagePaths,
            'plantation_date' => $validated['plantation_date'],
            'next_inspection_date' => $validated['next_inspection_date'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'plantation_survey_file' => $surveyFilePath,
            'created_by' => auth()->id(),
        ]);

        $this->generateTreesFromDrive($drive);

        return redirect()->route('plantation-drives.show', $drive)
            ->with('success', 'Plantation drive created successfully!');
    }

    public function show(PlantationDrive $plantationDrive)
    {
        $plantationDrive->load(['location', 'createdBy', 'trees']);
        return view('plantation-drives.show', compact('plantationDrive'));
    }

    public function trees(PlantationDrive $plantationDrive)
    {
        $trees = $plantationDrive->trees()->with(['plantedBy', 'latestInspection'])->get();
        return view('plantation-drives.trees', compact('plantationDrive', 'trees'));
    }

    private function generateTreesFromDrive(PlantationDrive $drive)
    {
        for ($i = 1; $i <= $drive->number_of_trees; $i++) {
            Tree::create([
                'tree_id' => $drive->drive_id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'species' => 'Not specified', // To be filled later by user
                'location_id' => $drive->location_id,
                'plantation_drive_id' => $drive->id,
                'location_description' => $drive->location->name,
                'landmark' => $drive->location->landmark,
                'latitude' => $drive->latitude,
                'longitude' => $drive->longitude,
                'plantation_date' => $drive->plantation_date,
                'next_inspection_date' => $drive->next_inspection_date,
                'planted_by' => $drive->created_by,
                'status' => 'planted',
                'photo_path' => '', // Empty string for no photo initially
                'description' => '', // Empty string for no description initially
                'plantation_survey_file' => '', // Empty string for no survey file initially
            ]);
        }
    }
}
