<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SaktiController;
use App\Http\Controllers\User\CategoryController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// =======================
// Dashboard User (lihat kategori)
// =======================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $categories = \App\Models\Category::all();
        return Inertia::render('User/Dashboard', [
            'categories' => $categories
        ]);
    })->name('dashboard');
});

// =======================
// Dashboard Admin + CRUD Kategori
// =======================
Route::middleware(['auth', 'verified'])->group(function () {
    // admin lihat + kelola kategori
    Route::get('/admin/dashboard', [CategoryController::class, 'index'])
        ->name('admin.dashboard');

    Route::post('/admin/categories', [CategoryController::class, 'store'])
        ->name('admin.categories.store');
    Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])
        ->name('admin.categories.update');
    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('admin.categories.destroy');
});

// =======================
// Profile
// =======================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// =======================
// Sakti SSO
// =======================
Route::get('/sakti/login', [SaktiController::class, 'redirectToSakti'])->name('sakti.login');
Route::get('/sakti/callback', [SaktiController::class, 'handleCallback'])->name('sakti.callback');
