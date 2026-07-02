<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Tampilkan produk (public/toko user)
     */
    

    /**
     * Simpan produk baru
     */

public function index()
{
    $produk = Product::where('is_active', true)->with(['category', 'orders' => function ($query) {
        $query->completedReviews();
    }])->get();
    $categories = Category::all()->map(function ($category) {
        return [
            'id' => $category->id,
            'nama_kategori' => $category->nama_kategori,
        ];
    });

    return Inertia::render('LihatProduk', [
        'produkList' => $produk->map(function ($item) {
            $ratings = $item->orders->pluck('rating')->filter();

            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->kategori_nama, // 🔥 penting
                'harga' => 'Rp ' . number_format($item->harga),
                'hargaCoret' => null,
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                'rating' => $ratings->count() ? round($ratings->avg(), 1) : null,
                'rating_count' => $ratings->count(),
                'terjual' => $item->terjual ?? 0,
                'toko' => 'Toko Kamu',
                'jarak' => '1 km',
            ];
        }),
        'categories' => $categories,
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'harga'       => 'required|integer',
            'stok'        => 'required|integer',
            'kategori_id' => 'required|exists:categories,id',
            'deskripsi'   => 'nullable|string',
            'sub_kategori_id' => 'nullable|exists:sub_categories,id',
            'warna'       => 'nullable|string|max:100',
            'ukuran'      => 'nullable|string|max:100',
            'berat'       => 'nullable|integer',
            'satuan'      => 'nullable|string|max:255',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variations'  => 'nullable|array',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('produk', 'public');
            }
        }

        $kategori = Category::find($request->kategori_id);
        Product::create([
            'user_id'        => Auth::id(),
            'nama'           => $request->nama,
            'harga'          => $request->harga,
            'stok'           => $request->stok,
            'kategori_id'    => $kategori ? $kategori->id : null,
            'kategori_nama'  => $kategori ? $kategori->nama : null,
            'sub_kategori_id'=> $request->sub_kategori_id,
            'deskripsi'      => $request->deskripsi,
            'warna'          => $request->warna,
            'ukuran'         => $request->ukuran,
            'berat'          => $request->berat,
            'satuan'         => $request->satuan,
            'image'          => count($imagePaths) > 0 ? json_encode($imagePaths) : null,
            'variations'     => $request->variations,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Update produk
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'harga'           => 'required|integer',
            'stok'            => 'required|integer',
            'kategori_id'     => 'required|exists:categories,id',
            'sub_kategori_id' => 'nullable|exists:sub_categories,id',
            'deskripsi'       => 'nullable|string',
            'warna'           => 'nullable|string|max:100',
            'ukuran'          => 'nullable|string|max:100',
            'berat'           => 'nullable|integer',
            'satuan'          => 'nullable|string|max:255',
            'images'          => 'nullable|array|max:5',
            'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'existing_images' => 'nullable|array',
            'variations'      => 'nullable|array',
        ]);

        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $existingImages = $request->input('existing_images', []);
        $newImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $newImages[] = $file->store('produk', 'public');
            }
        }

        $finalImages = array_merge($existingImages, $newImages);
        $imageValue = count($finalImages) > 0 ? json_encode($finalImages) : null;

        $kategori = Category::find($request->kategori_id);
        $product->update([
            'nama'           => $request->nama,
            'harga'          => $request->harga,
            'stok'           => $request->stok,
            'kategori_id'    => $kategori ? $kategori->id : null,
            'kategori_nama'  => $kategori ? $kategori->nama : null,
            'sub_kategori_id' => $request->sub_kategori_id,
            'deskripsi'      => $request->deskripsi,
            'warna'          => $request->warna,
            'ukuran'         => $request->ukuran,
            'berat'          => $request->berat,
            'satuan'         => $request->satuan,
            'image'          => $imageValue,
            'variations'     => $request->variations,
        ]);

        return back()->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Hapus produk
     */
    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Halaman kelola produk (untuk user toko)
     */
    public function manage()
    {
        $userId = Auth::id();

        $products = Product::with(['category', 'subCategory', 'latestAppeal'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(15)
            ->through(function ($p) {
                return [
                    'id'        => $p->id,
                    'nama'      => $p->nama,
                    'harga'     => $p->harga,
                    'stok'      => $p->stok,
                    'category'  => $p->category,
                    'sub_category' => $p->subCategory,
                    'deskripsi' => $p->deskripsi,
                    'warna'     => $p->warna,
                    'ukuran'    => $p->ukuran,
                    'berat'     => $p->berat,
                    'satuan'    => $p->satuan,
                    'variations'=> $p->variations,
                    'image_url' => $p->image ? asset('storage/' . $p->image) : null,
                    'images_url'=> $p->images_url,
                    'images'    => $p->images,
                    'is_active' => (bool) $p->is_active,
                    'deactivated_reason' => $p->deactivated_reason,
                    'latest_appeal' => $p->latestAppeal ? [
                        'id' => $p->latestAppeal->id,
                        'alasan_banding' => $p->latestAppeal->alasan_banding,
                        'bukti_pendukung' => $p->latestAppeal->bukti_pendukung ? asset('storage/' . $p->latestAppeal->bukti_pendukung) : null,
                        'admin_reply' => $p->latestAppeal->admin_reply,
                    ] : null,
                ];
            });

        $categories = Category::with('subCategories')->get();
        $satuans = Satuan::all();

        $subCategories = SubCategory::all()->map(function ($s) {
            return [
                'id' => $s->id,
                'nama_sub_kategori' => $s->nama_subkategori,
                'kategori_id' => $s->category_id,
            ];
        });

        return Inertia::render('User/KelolaProduk', [
            'products' => $products,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'satuans' => $satuans,
        ]);
    }

public function show($id)
{
    $produk = Product::where('is_active', true)->with(['category', 'subCategory', 'user', 'orders' => function ($query) {
        $query->completedReviews();
    }])->findOrFail($id);
    $ratings = $produk->orders->pluck('rating')->filter();

    $reviews = \App\Models\Order::with('buyer')
        ->where('product_id', $id)
        ->where('status', 'selesai')
        ->whereNotNull('rating')
        ->latest('reviewed_at')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'buyer_name' => $order->buyer?->name ?? 'Pembeli Umum',
                'rating' => $order->rating,
                'review_text' => $order->review_text,
                'review_image' => $order->review_image ? asset('storage/' . $order->review_image) : null,
                'reviewed_at' => $order->reviewed_at ? $order->reviewed_at->format('d M Y') : null,
                'seller_reply' => $order->seller_reply,
            ];
        });

    return Inertia::render('User/DetailProduk', [
        'produk' => [
            'id'        => $produk->id,
            'nama'      => $produk->nama,
            'harga'     => $produk->harga,
            'stok'      => $produk->stok,
            'deskripsi' => $produk->deskripsi,
            'warna'     => $produk->warna,
            'ukuran'    => $produk->ukuran,
            'image'     => $produk->cover_image_url,
            'images'    => $produk->images_url,
            'toko'      => $produk->user?->nama_toko ?? $produk->user?->name ?? '-',
            'user_id'   => $produk->user_id,
            'kategori'  => $produk->category?->nama_kategori ?? '-',
            'category'  => $produk->category,
            'sub_category' => $produk->subCategory,
            'variations'=> $produk->variations,
            'rating'    => $ratings->count() ? round($ratings->avg(), 1) : null,
            'rating_count' => $ratings->count(),
        ],
        'reviews' => $reviews
    ]);
}

public function storeProfile($id)
{
    $toko = User::where('role', 2)->findOrFail($id);
    
    $produkList = Product::where('user_id', $id)
        ->where('is_active', true)
        ->with(['category', 'orders' => function ($query) {
            $query->completedReviews();
        }])
        ->latest()
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
                'stok' => $item->stok,
            ];
        });

    return Inertia::render('User/ProfilToko', [
        'toko' => $toko,
        'produkList' => $produkList,
    ]);
}

public function create()
{
    $categories = Category::with('subCategories')->get();

    return Inertia::render('User/Product/Create', [
    'categories' => $categories
    ]);
}

public function submitAppeal(Request $request, $id)
{
    $request->validate([
        'alasan_banding' => 'required|string|max:2000',
        'bukti_pendukung' => 'required|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120',
    ]);

    $product = Product::where('user_id', Auth::id())->findOrFail($id);

    if ($product->is_active) {
        return back()->with('error', 'Produk ini masih aktif.');
    }

    $filePath = $request->file('bukti_pendukung')->store('bukti_banding', 'public');

    \App\Models\ProductAppeal::create([
        'product_id' => $product->id,
        'user_id' => Auth::id(),
        'alasan_dinonaktifkan' => $product->deactivated_reason ?? 'Tidak ada alasan spesifik.',
        'alasan_banding' => trim($request->alasan_banding),
        'bukti_pendukung' => $filePath,
        'status' => 'pending',
    ]);

    return back()->with('success', 'Pengajuan banding berhasil diajukan.');
}

public function acknowledgeAppeal($id)
{
    $appeal = \App\Models\ProductAppeal::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
    
    $appeal->update([
        'status' => 'completed'
    ]);

    return back()->with('success', 'Banding selesai diproses.');
}
}
