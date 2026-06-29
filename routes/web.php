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
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $products = \App\Models\Product::where('is_active', true)
        ->with(['category', 'user', 'orders' => function($q) {
            $q->completedReviews();
        }])
        ->latest()
        ->take(8)
        ->get()
        ->map(function ($item) {
            $ratings = $item->orders->pluck('rating')->filter();
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->category?->nama_kategori ?? '-',
                'harga' => $item->harga,
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                'rating' => $ratings->count() ? round($ratings->avg(), 1) : null,
                'rating_count' => $ratings->count(),
                'terjual' => $item->terjual ?? 0,
                'toko' => $item->user?->nama_toko ?? $item->user?->name ?? 'Toko Lokal',
            ];
        });

    return Inertia::render('Welcome', [
        'products' => $products,
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
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
// DETAIL PRODUK (publik)
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');

// PROFIL TOKO (publik)
Route::get('/toko-profile/{id}', [ProductController::class, 'storeProfile'])->name('toko.profile');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| USER (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // LAPORAN PENGADUAN & KOMPLAIN (LAPORKAN PRODUK / AKUN)
    Route::post('/report', [ComplaintController::class, 'storeReport'])->name('report.store');

    // DASHBOARD
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('dashboard');

    // PRODUK MANAGEMENT
    Route::controller(ProductController::class)->group(function () {
        Route::post('/toko', 'store')->name('user.toko.store');
        Route::put('/toko/{id}', 'update')->name('user.toko.update');
        Route::delete('/toko/{id}', 'destroy')->name('user.toko.destroy');
        Route::get('/products', 'manage')->name('user.products');
    });

    // PESANAN
    Route::get('/pesanan', [BuyerController::class, 'kelolaPesanan'])->name('user.orders');
    Route::get('/riwayat-pemesanan', [BuyerController::class, 'riwayatPemesanan'])->name('user.riwayat-pemesanan');
    Route::post('/orders/{id}/accept', [OrderController::class, 'accept'])
        ->name('orders.accept');
    Route::post('/orders/{id}/reject', [OrderController::class, 'reject'])
        ->name('orders.reject');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])
    ->name('orders.updateStatus');
    Route::post('/orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])
        ->name('orders.confirm-payment');

    Route::post('/orders/{id}/review', [OrderController::class, 'review'])
        ->name('orders.review');

    // Ulasan (untuk penjual melihat dan membalas)
    Route::get('/toko/ulasan', [\App\Http\Controllers\User\ReviewController::class, 'index'])
        ->name('user.reviews');

    Route::post('/toko/ulasan/{order}/reply', [\App\Http\Controllers\User\ReviewController::class, 'reply'])
        ->name('user.reviews.reply');

Route::delete('/orders/{id}', [OrderController::class, 'destroy'])
    ->name('orders.destroy');

    // PENGADUAN & KOMPLAIN
    Route::get('/pengaduan', [ComplaintController::class, 'create'])->name('pengaduan');
    Route::post('/pengaduan', [ComplaintController::class, 'store'])->name('pengaduan.store');

    // LIVE CHAT
    Route::get('/live-chat', function () {
        return Inertia::render('LiveChat');
    });

    // PROMO
    Route::get('/promo-buyer', [BuyerController::class, 'promoBuyer'])->name('promo.buyer');

    // KERANJANG
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    // CHECKOUT
    Route::middleware(['auth'])->group(function () {

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout/prepare/product', [CheckoutController::class, 'prepareFromProduct'])
        ->name('checkout.prepare.product');

    Route::post('/checkout/prepare/cart', [CheckoutController::class, 'prepareFromCart'])
        ->name('checkout.prepare.cart');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::get('/checkout-success', function () {
        return Inertia::render('CheckoutSuccess', [
            'checkoutResult' => session('checkout_result'),
        ]);
    })->name('checkout.success');

    Route::post('/checkout/qris/confirm', [CheckoutController::class, 'confirmQrisPayment'])
        ->name('checkout.qris.confirm');

    Route::post(
    '/checkout/upload-proof',
    [CheckoutController::class, 'uploadProof']
)->name('checkout.upload-proof');

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

    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat.index');

    Route::get('/chat/start/{seller}', [ChatController::class, 'start'])
        ->name('chat.start');

    Route::get('/chat/{id}', [ChatController::class, 'show'])
        ->name('chat.show');

    Route::post('/chat/{id}', [ChatController::class, 'send'])
        ->name('chat.send');

    Route::delete('/chat/{id}/messages/all', [ChatController::class, 'deleteAllMessages'])
        ->name('chat.messages.deleteAll');

    Route::delete('/chat/{id}/messages', [ChatController::class, 'deleteSelectedMessages'])
        ->name('chat.messages.deleteSelected');
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

    // SUB CATEGORY
Route::controller(SubCategoryController::class)->group(function () {
    Route::delete('/sub-categories/{id}', 'destroy')->name('subcategories.destroy');
    Route::put('/sub-categories/{id}', 'update')->name('subcategories.update');
});

    // 👤 USERS
    Route::controller(\App\Http\Controllers\Admin\UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::delete('/users/{id}', 'destroy')->name('users.destroy');
        Route::post('/users/{id}/deactivate', 'deactivate')->name('users.deactivate');
        Route::post('/users/{id}/activate', 'activate')->name('users.activate');
    });

    // 📦 HISTORI PESANAN
    Route::get('/orders', [AdminController::class, 'orders'])
        ->name('orders');

    // 📜 SERTIFIKASI TOKO
    Route::get('/sertifikasi', [AdminController::class, 'certifications'])
        ->name('sertifikasi.index');
    Route::post('/sertifikasi/{id}/approve', [AdminController::class, 'approveCertification'])
        ->name('sertifikasi.approve');
    Route::post('/sertifikasi/{id}/reject', [AdminController::class, 'rejectCertification'])
        ->name('sertifikasi.reject');

    // 🔍 FILTER PENINJAUAN
    Route::get('/filter-peninjauan', [AdminController::class, 'moderationKeywords'])->name('moderation.index');
    Route::post('/filter-peninjauan', [AdminController::class, 'storeModerationKeyword'])->name('moderation.store');
    Route::delete('/filter-peninjauan/{id}', [AdminController::class, 'destroyModerationKeyword'])->name('moderation.destroy');

    // 📢 KOMPLAIN
    Route::get('/komplain', [AdminController::class, 'complaints'])
        ->name('komplain');
    Route::post('/komplain', [AdminController::class, 'storeComplaint'])
        ->name('komplain.store');
    Route::post('/komplain/{id}/forward', [AdminController::class, 'forwardComplaint'])
        ->name('komplain.forward');
    Route::post('/komplain/{id}/reply', [AdminController::class, 'replyComplaint'])
        ->name('komplain.reply');

    // 🛍️ PRODUK
    Route::get('/products', [AdminController::class, 'products'])
        ->name('products');

    Route::get('/products/reports', [AdminController::class, 'productReports'])
        ->name('products.reports');

    Route::post('/products/{id}/deactivate', [AdminController::class, 'deactivateProduct'])
        ->name('products.deactivate');

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

//QRISS
Route::get('/create-qris', [PaymentController::class, 'createQRIS']);
Route::post('/create-qris', [PaymentController::class, 'createQRIS']);