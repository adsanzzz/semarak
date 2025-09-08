<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaktiController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ProductController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'      => Route::has('login'),
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

// Produk publik (lihat semua produk)
Route::get('/toko', [ProductController::class, 'index'])->name('user.toko');


// Tambah, update, hapus produk (hanya user login + verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/toko', [ProductController::class, 'store'])->name('user.toko.store');
    Route::put('/toko/{id}', [ProductController::class, 'update'])->name('user.toko.update');
    Route::delete('/toko/{id}', [ProductController::class, 'destroy'])->name('user.toko.destroy');
});


/*
|--------------------------------------------------------------------------
| User Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $categories = \App\Models\Category::all();
        return Inertia::render('User/Dashboard', [
            'categories' => $categories,
        ]);
    })->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| Admin Dashboard + CRUD Kategori
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin lihat + kelola kategori
    Route::get('/admin/dashboard', [CategoryController::class, 'index'])
        ->name('admin.dashboard');

    Route::post('/admin/categories', [CategoryController::class, 'store'])
        ->name('admin.categories.store');

    Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])
        ->name('admin.categories.update');

    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('admin.categories.destroy');
});


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| Sakti SSO
|--------------------------------------------------------------------------
*/
Route::get('/sakti/login', [SaktiController::class, 'redirectToSakti'])
    ->name('sakti.login');

Route::get('/sakti/callback', [SaktiController::class, 'handleCallback'])
    ->name('sakti.callback');
