<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('trees', TreeController::class);
    Route::resource('inspections', InspectionController::class);
    
    Route::get('/inspections/upcoming/list', [InspectionController::class, 'upcomingInspections'])
        ->name('inspections.upcoming');
    Route::get('/trees/{tree}/inspect', [InspectionController::class, 'create'])
        ->name('trees.inspect');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/volunteers', [AdminDashboardController::class, 'volunteers'])->name('volunteers');
    Route::get('/overdue-inspections', [AdminDashboardController::class, 'overdueInspections'])->name('overdue-inspections');
    Route::get('/map', [AdminDashboardController::class, 'mapView'])->name('map');
    Route::get('/export', [AdminDashboardController::class, 'exportData'])->name('export');
});
