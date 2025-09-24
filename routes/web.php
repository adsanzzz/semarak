<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaktiController;
use App\Http\Controllers\Auth\RegisterTokoController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\BuyerController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\CheckoutController;
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
Route::controller(RegisterTokoController::class)->group(function () {
    Route::get('/register-toko', 'create')->name('register.toko');
    Route::post('/register-toko', 'store')->name('register.toko.store');
});

// Produk publik
Route::get('/toko', [ProductController::class, 'index'])->name('user.toko');

/*
|--------------------------------------------------------------------------
| User Routes (Produk, Dashboard, Pesanan, Promo)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Produk
    Route::controller(ProductController::class)->group(function () {
        Route::post('/toko', 'store')->name('user.toko.store');
        Route::put('/toko/{id}', 'update')->name('user.toko.update');
        Route::delete('/toko/{id}', 'destroy')->name('user.toko.destroy');
        Route::get('/products', 'manage')->name('user.products');
    });

    // Buyer
    Route::controller(BuyerController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/pesanan', 'kelolaPesanan')->name('user.orders');
        Route::get('/produk/lihat', 'lihatProduk')->name('produk.lihat');
        Route::get('/promo-buyer', 'promoBuyer')->name('promo.buyer');
    });

    // Keranjang (📌 pastikan path Vue-nya sesuai)
    Route::get('/keranjang', fn () => Inertia::render('User/Keranjang'))->name('keranjang.index');

    // Lihat Semua Produk (route lama, sebaiknya dihapus kalau sudah pakai route baru)
    Route::get('/lihat-produk', fn () => Inertia::render('User/LihatProduk'))->name('lihat-produk');

    // Halaman Tentang Semarak
    Route::get('/tentang-semarak', fn () => Inertia::render('TentangSemarak'))->name('tentang.semarak');

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::controller(AdminCategoryController::class)->group(function () {
        Route::post('/categories', 'store')->name('categories.store');
        Route::put('/categories/{id}', 'update')->name('categories.update');
        Route::delete('/categories/{id}', 'destroy')->name('categories.destroy');
    });
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
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Sakti SSO
|--------------------------------------------------------------------------
*/
Route::get('/sakti/login', [SaktiController::class, 'redirectToSakti'])->name('sakti.login');
Route::get('/sakti/callback', [SaktiController::class, 'handleCallback'])->name('sakti.callback');

/*
|--------------------------------------------------------------------------
| Keranjang Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
});

/*
|--------------------------------------------------------------------------
| Misc Routes
|--------------------------------------------------------------------------
*/
// Route untuk menyimpan nomor WA penjual ke session
Route::post('/set-nomor-wa', function(Request $request) {
    session(['nomor_wa_penjual' => $request->nomor_wa]);
    return response()->json(['success' => true]);
});
