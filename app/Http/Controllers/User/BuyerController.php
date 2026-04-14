<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;

class BuyerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $products = Product::latest()->get();

        return match ($user->role) {
            1 => Inertia::render('Admin/AdminDashboard', [
                'categories' => Category::all(),
            ]),
            2 => Inertia::render('User/DashboardToko', [
                'categories' => Category::all(),
                'products'   => $products,
            ]),
            3 => Inertia::render('User/DashboardBuyer', [
                'products' => $products,
            ]),
            default => Inertia::render('User/DashboardBuyer', [
                'products' => $products,
            ]),
        };
    }

   public function kelolaPesanan()
{
    $orders = \App\Models\Order::with('product', 'buyer')->get();

    return Inertia::render('Buyer/KelolaPesanan', [
        'orders' => $orders
    ]);
}

    public function lihatProduk()
    {
        $products = Product::with('category', 'user')->latest()->get()->map(function ($p) {
            return [
                'id'        => $p->id,
                'nama'      => $p->nama,
                'toko'      => $p->user?->name ?? '-',
                'rating'    => 4.8,
                'terjual'   => 100,
                'kategori'  => $p->category?->nama_kategori ?? '-',
                'hargaCoret'=> null,
                'harga'     => 'Rp ' . number_format($p->harga, 0, ',', '.'),
                'jarak'     => '-',
                'image'     => $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/300x200',
            ];
        });

        return Inertia::render('User/LihatProduk', [
            'produkList' => $products,
            'categories' => Category::all(),
        ]);
    }

    public function riwayatPemesanan()
    {
        $userId = auth()->id();
        $orders = Order::where('user_id', auth()->id())
    ->latest()
    ->get();

        return Inertia::render('User/RiwayatPemesanan', [
            'orders' => $orders,
        ]);
    }

    public function promoBuyer()
    {
        return Inertia::render('User/PromoBuyer');
    }
    
}