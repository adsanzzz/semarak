<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Product;
use App\Models\Order;

return new class extends Migration
{
    public function up(): void
    {
        $products = Product::all();
        foreach ($products as $product) {
            $product->terjual = Order::where('product_id', $product->id)
                ->where('status', 'selesai')
                ->sum('jumlah');
            $product->save();
        }
    }

    public function down(): void
    {
        // No rollback needed
    }
};
