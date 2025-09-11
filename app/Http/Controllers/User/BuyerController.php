<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            default => Inertia::render('User/DashboardToko', [
                'categories' => [],
                'products'   => $products,
            ]),
        };
    }

    public function kelolaPesanan()
    {
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
    }

    public function lihatProduk()
    {
        $products = Product::with('category', 'user')->latest()->get()->map(function ($p) {
            return [
                'id'        => $p->id,
                'nama'      => $p->nama,
                'toko'      => $p->user?->name ?? '-',
                'rating'    => 4.8, // dummy, ganti jika ada rating
                'terjual'   => 100,   // dummy, ganti jika ada data
                'kategori'  => $p->category?->nama_kategori ?? '-',
                'hargaCoret'=> null, // dummy, ganti jika ada diskon
                'harga'     => 'Rp ' . number_format($p->harga, 0, ',', '.'),
                'jarak'     => '—', // dummy, ganti jika ada data
                'image'     => $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/300x200',
            ];
        });
        $categories = Category::all();
        return Inertia::render('User/LihatProduk', [
            'produkList' => $products,
            'categories' => $categories,
        ]);
    }

    public function promoBuyer()
    {
        return Inertia::render('User/PromoBuyer');
    }
}
