<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

// create seller and buyer with unique emails
$seller = User::create([
    'name' => 'Test Seller',
    'email' => 'seller+' . time() . '@example.test',
    'password' => 'password',
    'role' => 2,
]);

$buyer = User::create([
    'name' => 'Test Buyer',
    'email' => 'buyer+' . time() . '@example.test',
    'password' => 'password',
    'role' => 3,
]);

$prod = Product::create([
    'user_id' => $seller->id,
    'nama' => 'Test Product',
    'harga' => 1000,
    'stok' => 10,
]);

$order = Order::create([
    'product_id' => $prod->id,
    'user_id' => $seller->id,
    'buyer_id' => $buyer->id,
    'jumlah' => 1,
    'total_harga' => 1000,
    'status' => 'selesai',
    'rating' => 5,
    'review_text' => 'Mantap produk',
    'reviewed_at' => now(),
]);

// login as seller
Auth::loginUsingId($seller->id);

$reviews = Order::with(['product','buyer'])
    ->whereHas('product', function($q) use ($seller) {
        $q->where('user_id', $seller->id);
    })
    ->where('status', 'selesai')
    ->whereNotNull('rating')
    ->get();

echo 'reviews: ' . count($reviews) . PHP_EOL;

// save reply
$order->seller_reply = 'Terima kasih atas ulasannya';
$order->save();

echo 'saved reply: ' . Order::find($order->id)->seller_reply . PHP_EOL;

echo 'done' . PHP_EOL;
