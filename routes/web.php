<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\PlantationDriveController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LocationAnalyticsController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [PlantationDriveController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // Plantation Drives (main flow)
    Route::resource('plantation-drives', PlantationDriveController::class);
    Route::get('/location/{location}/drives', [PlantationDriveController::class, 'locationDrives'])->name('plantation-drives.location');
    Route::get('/plantation-drive/{plantationDrive}/trees', [PlantationDriveController::class, 'trees'])->name('plantation-drives.trees');
    
    // Trees (individual tree management)
    Route::resource('trees', TreeController::class);
    Route::get('/location/{location}/trees', [TreeController::class, 'locationTrees'])->name('trees.location');
    
    // Inspections
    Route::resource('inspections', InspectionController::class);
    Route::get('/inspections/upcoming/list', [InspectionController::class, 'upcomingInspections'])
        ->name('inspections.upcoming');
    Route::get('/trees/{tree}/inspect', [InspectionController::class, 'create'])
        ->name('trees.inspect');
    
    Route::get('/api/location-suggestions', [LocationAnalyticsController::class, 'getLocationSuggestions']);
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/location-analytics', [LocationAnalyticsController::class, 'index'])->name('location-analytics');
    Route::get('/volunteers', [AdminDashboardController::class, 'volunteers'])->name('volunteers');
    Route::post('/volunteers', [AdminDashboardController::class, 'storeVolunteer'])->name('volunteers.store');
    Route::get('/overdue-inspections', [AdminDashboardController::class, 'overdueInspections'])->name('overdue-inspections');
    Route::get('/map', [AdminDashboardController::class, 'mapView'])->name('map');
    Route::get('/export', [AdminDashboardController::class, 'exportData'])->name('export');
});
