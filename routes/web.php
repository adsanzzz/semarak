<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaktiController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\Auth\RegisterTokoController;

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
});

// Pilihan registrasi user/toko
Route::get('/register-type', function () {
    return Inertia::render('Auth/RegisterType');
})->name('register.type');

// Registrasi toko
Route::get('/register-toko', [RegisterTokoController::class, 'create'])->name('register.toko');
Route::post('/register-toko', [RegisterTokoController::class, 'store'])->name('register.toko.store');

// Produk publik (lihat semua produk)
Route::get('/toko', [ProductController::class, 'index'])->name('user.toko');

/*
|--------------------------------------------------------------------------
| Produk (CRUD, hanya untuk user login & verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/toko', [ProductController::class, 'store'])->name('user.toko.store');
    Route::put('/toko/{id}', [ProductController::class, 'update'])->name('user.toko.update');
    Route::delete('/toko/{id}', [ProductController::class, 'destroy'])->name('user.toko.destroy');

    // Kelola produk khusus user toko
    Route::get('/products', function () {
        $user = Auth::user();
        $products = \App\Models\Product::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        $categories = \App\Models\Category::all();

        return Inertia::render('User/KelolaProduk', [
            'products'   => $products,
            'categories' => $categories,
        ]);
    })->name('user.products');
});

/*
|--------------------------------------------------------------------------
| User Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = Auth::user();
    $products = \App\Models\Product::orderByDesc('created_at')->get();

    if ($user->role == 2) {
        // Dashboard untuk Toko
        $categories = \App\Models\Category::all();
        return Inertia::render('User/DashboardToko', [
            'categories' => $categories,
            'products'   => $products,
        ]);
    } elseif ($user->role == 3) {
        // Dashboard untuk Buyer
        return Inertia::render('User/DashboardBuyer', [
            'products' => $products,
        ]);
    }

    // Fallback (admin default ke Dashboard Toko kosong kategori)
    return Inertia::render('User/DashboardToko', [
        'categories' => [],
        'products'   => $products,
    ]);
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Dashboard + CRUD Kategori
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [CategoryController::class, 'index'])->name('admin.dashboard');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});

/*
|--------------------------------------------------------------------------
| Kelola Pesanan (khusus user toko)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->get('/pesanan', function () {
    $user = Auth::user();

    // Dummy data orders (sementara)
    $orders = [
        [
            'id'           => 1,
            'product_nama' => 'Produk Contoh',
            'buyer_name'   => 'Pembeli Satu',
            'jumlah'       => 2,
            'status'       => 'Menunggu',
            'created_at'   => now()->toDateTimeString(),
        ],
    ];

    return Inertia::render('User/KelolaPesanan', [
        'orders' => $orders,
    ]);
})->name('user.orders');

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
