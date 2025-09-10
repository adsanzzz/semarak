<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Auth::check()
            ? Product::with('category')->where('user_id', Auth::id())->get()
            : Product::with('category')->get();

        $categories = Category::all();

        return Inertia::render('User/Toko', [
            'products'   => $products,
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
            'warna'       => 'nullable|string|max:100',
            'ukuran'      => 'nullable|string|max:100',
            'berat'       => 'required|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('produk', 'public')
            : null;

        Product::create([
            'user_id'     => Auth::id(),
            'nama'        => $request->nama,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'kategori_id' => $request->kategori_id,
            'deskripsi'   => $request->deskripsi,
            'warna'       => $request->warna,
            'ukuran'      => $request->ukuran,
            'berat'       => $request->berat,
            'image'       => $imagePath,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'harga'       => 'required|integer',
            'stok'        => 'required|integer',
            'kategori_id' => 'required|exists:categories,id',
            'deskripsi'   => 'nullable|string',
            'warna'       => 'nullable|string|max:100',
            'ukuran'      => 'nullable|string|max:100',
            'berat'       => 'required|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produk', 'public');
        }

        $product->update([
            'nama'        => $request->nama,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'kategori_id' => $request->kategori_id,
            'deskripsi'   => $request->deskripsi,
            'warna'       => $request->warna,
            'ukuran'      => $request->ukuran,
            'berat'       => $request->berat,
            'image'       => $imagePath,
        ]);

        return back()->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }

    public function manage()
{
    $user = Auth::user();

    $products = Product::with('category')
        ->where('user_id', $user->id)
        ->latest()
        ->get()
        ->map(function ($p) {
            return [
                'id'        => $p->id,
                'nama'      => $p->nama,
                'harga'     => $p->harga,
                'stok'      => $p->stok,
                'kategori'  => $p->category?->nama_kategori ?? '-', // ambil nama kategori
                'deskripsi' => $p->deskripsi,
                'warna'     => $p->warna,
                'ukuran'    => $p->ukuran,
                'berat'     => $p->berat,
                'image_url' => $p->image ? asset('storage/' . $p->image) : null,
            ];
        });

    return Inertia::render('User/KelolaProduk', [
        'products'   => $products,
        'categories' => Category::all(),
    ]);
}
}
