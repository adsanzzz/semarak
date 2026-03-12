<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
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
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('produk', 'public')
            : null;

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
            'image'          => $imagePath,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Update produk
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'harga'       => 'required|integer',
            'stok'        => 'required|integer',
            'kategori_id' => 'required|exists:categories,id',
            'sub_kategori_id' => 'nullable|exists:sub_categories,id',
            'deskripsi'   => 'nullable|string',
            'warna'       => 'nullable|string|max:100',
            'ukuran'      => 'nullable|string|max:100',
            'berat'       => 'nullable|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produk', 'public');
        }

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
            'image'          => $imagePath,
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

    $products = Product::with(['category', 'subCategory'])
        ->where('user_id', $userId)
        ->latest()
        ->get()
        ->map(function ($p) {
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
                'image_url' => $p->image ? asset('storage/' . $p->image) : null,
            ];
        });

    $categories = Category::all();

$subCategories = SubCategory::all()->map(function ($s) {
    return [
        'id' => $s->id,
        'nama_sub_kategori' => $s->nama_sub_kategori,
        'kategori_id' => $s->kategori_id,
    ];
});

return Inertia::render('User/KelolaProduk', [
    'products' => $products,
    'categories' => $categories,
    'subCategories' => $subCategories,
]);
}

public function show($id)
{
    $produk = Product::with(['category', 'subCategory'])->findOrFail($id);

    return Inertia::render('User/DetailProduk', [
        'produk' => [
            'id'        => $produk->id,
            'nama'      => $produk->nama,
            'harga'     => $produk->harga,
            'stok'      => $produk->stok,
            'deskripsi' => $produk->deskripsi,
            'warna'     => $produk->warna,
            'ukuran'    => $produk->ukuran,
            'image'     => $produk->image ? asset('storage/' . $produk->image) : null,
            'category'  => $produk->category,
            'sub_category' => $produk->subCategory, // 👈 ini
        ]
    ]);

}
}
