<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BuyerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $products = Product::where('is_active', true)->latest()->get();

        return match ($user->role) {
            1 => Inertia::render('Admin/AdminDashboard', [
                'categories' => Category::all(),
            ]),
            2 => $this->dashboardToko($user->id),
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
    $orders = \App\Models\Order::with(['product', 'buyer'])
        ->whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->latest()
        ->get();

    return Inertia::render('Buyer/KelolaPesanan', [
        'orders' => $orders
    ]);
}

    public function lihatProduk(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = Product::where('is_active', true)->with(['category', 'user', 'orders' => function ($orderQuery) {
            $orderQuery->completedReviews();
        }]);

        if ($search !== '') {
            $query->where(function ($productQuery) use ($search) {
                $productQuery->where('nama', 'like', '%' . $search . '%');
            });
        }

        $products = $query->latest()->get()->map(function ($p) {
            $ratings = $p->orders->pluck('rating')->filter();

            return [
                'id'        => $p->id,
                'nama'      => $p->nama,
                'toko'      => $p->user?->name ?? '-',
                'rating'    => $ratings->count() ? round($ratings->avg(), 1) : null,
                'rating_count' => $ratings->count(),
                'terjual'   => $p->terjual ?? 0,
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
            'search' => $search,
        ]);
    }

    public function riwayatPemesanan()
    {
        $orders = Order::with(['product.user', 'buyer'])
            ->where('buyer_id', auth()->id())
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

    private function dashboardToko(int $userId)
    {
        $storeProductsQuery = Product::query()->where('user_id', $userId);

        $storeOrdersQuery = Order::query()
            ->whereHas('product', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        $validSalesOrdersQuery = (clone $storeOrdersQuery)
            ->where(function ($query) {
                $query->whereNull('review_status')
                    ->orWhere('review_status', '!=', 'ditolak');
            })
            ->where('status', '!=', 'dibatalkan');

        $totalProduk = (clone $storeProductsQuery)->count();
        $totalPesanan = (int) ((clone $storeOrdersQuery)->count() ?? 0);
        $totalPenjualan = (int) ((clone $validSalesOrdersQuery)->sum('jumlah') ?? 0);
        $totalPendapatan = (float) ((clone $validSalesOrdersQuery)->sum('total_harga') ?? 0);

        $topProducts = (clone $validSalesOrdersQuery)
            ->select(
                'product_id',
                DB::raw('SUM(jumlah) as total_terjual'),
                DB::raw('SUM(total_harga) as total_pendapatan')
            )
            ->with('product:id,nama,harga')
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get()
            ->map(function ($row, $index) {
                return [
                    'rank' => $index + 1,
                    'product_id' => $row->product_id,
                    'nama' => $row->product?->nama ?? '-',
                    'total_terjual' => (int) $row->total_terjual,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];
            })
            ->values();

        return Inertia::render('User/DashboardToko', [
            'stats' => [
                'totalProduk' => $totalProduk,
                'totalPesanan' => $totalPesanan,
                'totalPenjualan' => $totalPenjualan,
                'totalPendapatan' => $totalPendapatan,
            ],
            'topProducts' => $topProducts,
        ]);
    }
    
}