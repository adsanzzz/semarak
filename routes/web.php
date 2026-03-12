<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaktiController;
use App\Http\Controllers\Auth\RegisterTokoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\BuyerController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\ChatController;

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

// Registrasi
Route::get('/register-type', fn () => Inertia::render('Auth/RegisterType'))->name('register.type');

Route::controller(RegisterTokoController::class)->group(function () {
    Route::get('/register-toko', 'create')->name('register.toko');
    Route::post('/register-toko', 'store')->name('register.toko.store');
});

// Produk publik
Route::get('/toko', [ProductController::class, 'index'])->name('user.toko');
// Semua produk (publik) - tampilan listing produk yang sama untuk guest dan auth
Route::get('/produk/lihat', [App\Http\Controllers\User\BuyerController::class, 'lihatProduk'])->name('produk.lihat');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| USER (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('dashboard');

    // DETAIL PRODUK
    Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');

    // PRODUK MANAGEMENT
    Route::controller(ProductController::class)->group(function () {
        Route::post('/toko', 'store')->name('user.toko.store');
        Route::put('/toko/{id}', 'update')->name('user.toko.update');
        Route::delete('/toko/{id}', 'destroy')->name('user.toko.destroy');
        Route::get('/products', 'manage')->name('user.products');
    });

    // PESANAN
    Route::get('/pesanan', [BuyerController::class, 'kelolaPesanan'])->name('user.orders');

    // PROMO
    Route::get('/promo-buyer', [BuyerController::class, 'promoBuyer'])->name('promo.buyer');

    // KERANJANG
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    // CHECKOUT
    Route::middleware(['auth'])->group(function () {

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::get('/checkout-success', function () {
        return Inertia::render('CheckoutSuccess');
    })->name('checkout.success');

});

    // TENTANG
    Route::get('/tentang-semarak', fn () => Inertia::render('TentangSemarak'))->name('tentang.semarak');    

// PROFILE EDIT
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// CHAT
Route::middleware(['auth'])->group(function () {

    Route::get('/chat/start/{seller}', [ChatController::class, 'start'])
        ->name('chat.start');

    Route::get('/chat/{id}', [ChatController::class, 'show'])
        ->name('chat.show');

    Route::post('/chat/{id}', [ChatController::class, 'send'])
        ->name('chat.send');
});

});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // CATEGORY
    Route::controller(AdminCategoryController::class)->group(function () {
        Route::get('/categories', 'index')->name('categories.index');
        Route::post('/categories', 'store')->name('categories.store');
        Route::put('/categories/{id}', 'update')->name('categories.update');
        Route::delete('/categories/{id}', 'destroy')->name('categories.destroy');
    });

    // USERS (Kelola Akun)
    Route::controller(\App\Http\Controllers\Admin\UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::delete('/users/{id}', 'destroy')->name('users.destroy');
    });

});
/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| SAKTI
|--------------------------------------------------------------------------
*/
Route::get('/sakti/login', [SaktiController::class, 'redirectToSakti'])->name('sakti.login');
Route::get('/sakti/callback', [SaktiController::class, 'handleCallback'])->name('sakti.callback');

/*
|--------------------------------------------------------------------------
| MISC
|--------------------------------------------------------------------------
*/
Route::post('/set-nomor-wa', function(Request $request) {
    session(['nomor_wa_penjual' => $request->nomor_wa]);
    return response()->json(['success' => true]);
});