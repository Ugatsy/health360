<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\BodyRegionController;
use App\Http\Controllers\EmergencyContactController;
use Illuminate\Support\Facades\Route;

// Home route - now uses DashboardController to handle both guests and authenticated users
Route::get('/', [DashboardController::class, 'index'])->name('home');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (redundant but kept for backwards compatibility)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Symptom routes
    Route::prefix('symptoms')->name('symptoms.')->group(function () {
        Route::get('/', [SymptomController::class, 'index'])->name('index');
        Route::post('/', [SymptomController::class, 'store'])->name('store');
        Route::get('/results/{id}', [SymptomController::class, 'results'])->name('results');
        Route::get('/history', [SymptomController::class, 'history'])->name('history');
        Route::post('/feedback/{id}', [SymptomController::class, 'feedback'])->name('feedback');
    });

    // API routes for body regions
    Route::get('/api/body-regions', [BodyRegionController::class, 'getRegions'])->name('api.body-regions');

    // Doctor routes
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        Route::get('/review/{id}', [DoctorController::class, 'review'])->name('review');
        Route::post('/review/{id}/approve', [DoctorController::class, 'approve'])->name('approve');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Emergency contacts
    Route::post('/emergency-contacts', [EmergencyContactController::class, 'store'])->name('emergency-contacts.store');
    Route::delete('/emergency-contacts/{contact}', [EmergencyContactController::class, 'destroy'])->name('emergency-contacts.destroy');
});

require __DIR__.'/auth.php';
