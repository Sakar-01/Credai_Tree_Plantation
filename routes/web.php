<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LocationAnalyticsController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('trees', TreeController::class);
    Route::get('/location/{location}/trees', [TreeController::class, 'locationTrees'])->name('trees.location');
    Route::resource('inspections', InspectionController::class);
    
    // Location routes
    Route::get('/locations/create', [App\Http\Controllers\LocationController::class, 'create'])->name('locations.create');
    Route::post('/locations', [App\Http\Controllers\LocationController::class, 'store'])->name('locations.store');
    Route::get('/locations/{location}/plant-tree', [App\Http\Controllers\LocationController::class, 'plantTreeForm'])->name('locations.plant-tree');
    Route::post('/locations/{location}/plant-tree', [App\Http\Controllers\LocationController::class, 'plantTree'])->name('locations.plant-tree.store');
    
    Route::get('/inspections/upcoming/list', [InspectionController::class, 'upcomingInspections'])
        ->name('inspections.upcoming');
    Route::get('/trees/{tree}/inspect', [InspectionController::class, 'create'])
        ->name('trees.inspect');
    
    Route::get('/api/location-suggestions', [LocationAnalyticsController::class, 'getLocationSuggestions']);
    Route::get('/api/locations/check-duplicate', [App\Http\Controllers\LocationController::class, 'checkDuplicate']);
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
