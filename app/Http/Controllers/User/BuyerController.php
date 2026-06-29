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
        
        $products = Product::where('is_active', true)
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

        // Get list of available months with sales
        $availableMonths = (clone $validSalesOrdersQuery)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"))
            ->groupBy('month_year')
            ->orderByDesc('month_year')
            ->pluck('month_year')
            ->toArray();

        $defaultMonth = !empty($availableMonths) ? $availableMonths[0] : date('Y-m');
        $selectedMonth = request()->query('month', $defaultMonth);
        if ($selectedMonth === 'all') {
            $selectedMonth = 'semua';
        }

        if ($selectedMonth !== 'semua') {
            $filteredSalesQuery = (clone $validSalesOrdersQuery)
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$selectedMonth]);
        } else {
            $filteredSalesQuery = clone $validSalesOrdersQuery;
        }

        $totalProduk = (clone $storeProductsQuery)->count();
        $totalPesanan = (int) ((clone $filteredSalesQuery)->count() ?? 0);
        $totalPenjualan = (int) ((clone $filteredSalesQuery)->sum('jumlah') ?? 0);
        $totalPendapatan = (float) ((clone $filteredSalesQuery)->sum('total_harga') ?? 0);
        $averageOrderValue = $totalPesanan > 0 ? round($totalPendapatan / $totalPesanan, 2) : 0;

        $revenueGrowth = 0;
        $salesGrowth = 0;
        $ordersGrowth = 0;
        $hasComparison = false;

        if ($selectedMonth !== 'semua') {
            try {
                $currentCarbon = \Illuminate\Support\Carbon::createFromFormat('Y-m', $selectedMonth);
                $prevMonth = $currentCarbon->copy()->subMonth()->format('Y-m');

                $prevSalesQuery = (clone $validSalesOrdersQuery)
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$prevMonth]);

                $prevPendapatan = (float) ($prevSalesQuery->sum('total_harga') ?? 0);
                $prevPenjualan = (int) ($prevSalesQuery->sum('jumlah') ?? 0);
                $prevPesanan = (int) ($prevSalesQuery->count() ?? 0);

                $revenueGrowth = $prevPendapatan > 0 ? round((($totalPendapatan - $prevPendapatan) / $prevPendapatan) * 100, 1) : ($totalPendapatan > 0 ? 100 : 0);
                $salesGrowth = $prevPenjualan > 0 ? round((($totalPenjualan - $prevPenjualan) / $prevPenjualan) * 100, 1) : ($totalPenjualan > 0 ? 100 : 0);
                $ordersGrowth = $prevPesanan > 0 ? round((($totalPesanan - $prevPesanan) / $prevPesanan) * 100, 1) : ($totalPesanan > 0 ? 100 : 0);
                $hasComparison = true;
            } catch (\Exception $e) {
                // Fail-safe
            }
        }

        $chartData = [];
        if ($selectedMonth !== 'semua') {
            try {
                $currentCarbon = \Illuminate\Support\Carbon::createFromFormat('Y-m', $selectedMonth);
                $daysInMonth = $currentCarbon->daysInMonth;

                $dailySales = (clone $filteredSalesQuery)
                    ->select(
                        DB::raw("DATE_FORMAT(created_at, '%d') as day"),
                        DB::raw("SUM(total_harga) as revenue"),
                        DB::raw("SUM(jumlah) as qty")
                    )
                    ->groupBy('day')
                    ->get()
                    ->keyBy('day');

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                    $chartData[] = [
                        'label' => (string) $day,
                        'revenue' => isset($dailySales[$dayStr]) ? (float) $dailySales[$dayStr]->revenue : 0,
                        'qty' => isset($dailySales[$dayStr]) ? (int) $dailySales[$dayStr]->qty : 0,
                    ];
                }
            } catch (\Exception $e) {
                // Fail-safe
            }
        } else {
            $monthlySales = (clone $filteredSalesQuery)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"),
                    DB::raw("SUM(total_harga) as revenue"),
                    DB::raw("SUM(jumlah) as qty")
                )
                ->groupBy('month_year')
                ->orderBy('month_year', 'asc')
                ->limit(12)
                ->get();

            foreach ($monthlySales as $sale) {
                try {
                    $carbonDate = \Illuminate\Support\Carbon::createFromFormat('Y-m', $sale->month_year);
                    $label = $carbonDate->translatedFormat('M Y');
                } catch (\Exception $e) {
                    $label = $sale->month_year;
                }

                $chartData[] = [
                    'label' => $label,
                    'revenue' => (float) $sale->revenue,
                    'qty' => (int) $sale->qty,
                ];
            }
        }

        $topProducts = (clone $filteredSalesQuery)
            ->select(
                'product_id',
                DB::raw('SUM(jumlah) as total_terjual'),
                DB::raw('SUM(total_harga) as total_pendapatan')
            )
            ->with(['product' => function ($q) {
                $q->select('id', 'nama', 'harga', 'image', 'kategori_nama');
            }])
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get()
            ->map(function ($row, $index) use ($totalPenjualan) {
                $percentage = $totalPenjualan > 0 ? round(($row->total_terjual / $totalPenjualan) * 100, 1) : 0;
                return [
                    'rank' => $index + 1,
                    'product_id' => $row->product_id,
                    'nama' => $row->product?->nama ?? '-',
                    'harga' => $row->product?->harga ?? 0,
                    'image' => $row->product?->image ? asset('storage/' . $row->product->image) : null,
                    'category' => $row->product?->kategori_nama ?? ($row->product?->category?->nama_kategori ?? '-'),
                    'total_terjual' => (int) $row->total_terjual,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                    'percentage' => $percentage,
                ];
            })
            ->values();

        $recentOrders = (clone $filteredSalesQuery)
            ->with(['product:id,nama,harga,image', 'buyer:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'product_name' => $order->product?->nama ?? 'Produk Dihapus',
                    'product_image' => $order->product?->image ? asset('storage/' . $order->product->image) : null,
                    'buyer_name' => $order->buyer?->name ?? 'Pembeli Umum',
                    'qty' => $order->jumlah,
                    'total_harga' => (float) $order->total_harga,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ];
            });

        return Inertia::render('User/DashboardToko', [
            'stats' => [
                'totalProduk' => $totalProduk,
                'totalPesanan' => $totalPesanan,
                'totalPenjualan' => $totalPenjualan,
                'totalPendapatan' => $totalPendapatan,
                'averageOrderValue' => $averageOrderValue,
                'growth' => [
                    'hasComparison' => $hasComparison,
                    'revenue' => $revenueGrowth,
                    'sales' => $salesGrowth,
                    'orders' => $ordersGrowth,
                ],
            ],
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'availableMonths' => $availableMonths,
            'selectedMonth' => $selectedMonth,
            'chartData' => $chartData,
        ]);
    }
    
}