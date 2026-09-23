<?php

use App\Http\Controllers\ClinicianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\PerformanceLogController;
use App\Http\Controllers\PracticeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('event-types', EventTypeController::class);
    
    // Single route for practice settings
    Route::get('/practice/settings', [PracticeController::class, 'settings'])
        ->name('practice.settings');
    Route::put('/practice/settings', [PracticeController::class, 'update'])
        ->name('practice.update');

    Route::resource('appointments', AppointmentController::class);
    Route::resource('availability', AvailabilityController::class)->only(['index', 'store']);

    Route::resource('clinicians', ClinicianController::class)->except(['show']);
    Route::resource('performance-logs', PerformanceLogController::class)->except(['show']);
});



// Test Vuetify
Route::get('/test-vuetify', function () {
    return Inertia::render('TestVuetify');
});

Route::get('/test', function () {
    return Inertia::render('Availability/Index'); // Ensure capitalization matches file name
});