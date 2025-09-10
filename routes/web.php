<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaktiController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\Auth\RegisterTokoController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
})->name('home');

// Pilihan registrasi user/toko
Route::get('/register-type', fn () => Inertia::render('Auth/RegisterType'))->name('register.type');

// Registrasi toko
Route::get('/register-toko', [RegisterTokoController::class, 'create'])->name('register.toko');
Route::post('/register-toko', [RegisterTokoController::class, 'store'])->name('register.toko.store');

// Produk publik
Route::get('/toko', [ProductController::class, 'index'])->name('user.toko');

/*
|--------------------------------------------------------------------------
| User Routes (Produk, Dashboard, Pesanan)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // CRUD Produk
    Route::post('/toko', [ProductController::class, 'store'])->name('user.toko.store');
    Route::put('/toko/{id}', [ProductController::class, 'update'])->name('user.toko.update');
    Route::delete('/toko/{id}', [ProductController::class, 'destroy'])->name('user.toko.destroy');

    // Kelola Produk (halaman user)
    Route::get('/products', [ProductController::class, 'manage'])->name('user.products');

    // Dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $products = \App\Models\Product::latest()->get();

        return match ($user->role) {
            1 => Inertia::render('Admin/AdminDashboard', [
                'categories' => \App\Models\Category::all(),
            ]),
            2 => Inertia::render('User/DashboardToko', [
                'categories' => \App\Models\Category::all(),
                'products'   => $products,
            ]),
            3 => Inertia::render('User/DashboardBuyer', [
                'products' => $products,
            ]),
            default => Inertia::render('User/DashboardToko', [
                'categories' => [],
                'products'   => $products,
            ]),
        };
    })->name('dashboard');

    // Kelola Pesanan
    Route::get('/pesanan', function () {
        return Inertia::render('User/KelolaPesanan', [
            'orders' => [
                [
                    'id'           => 1,
                    'product_nama' => 'Produk Contoh',
                    'buyer_name'   => 'Pembeli Satu',
                    'jumlah'       => 2,
                    'status'       => 'Menunggu',
                    'created_at'   => now()->toDateTimeString(),
                ],
            ],
        ]);
    })->name('user.orders');

    // Lihat Semua Produk
    Route::get('/lihat-produk', function () {
        return Inertia::render('User/LihatProduk');
    })->name('lihat-produk');
    
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
Route::get('/sakti/login', [SaktiController::class, 'redirectToSakti'])->name('sakti.login');
Route::get('/sakti/callback', [SaktiController::class, 'handleCallback'])->name('sakti.callback');
