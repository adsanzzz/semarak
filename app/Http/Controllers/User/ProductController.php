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
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
            'kategori' => 'required',
            'deskripsi' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produk', 'public');
        }
        $product->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'image' => $imagePath,
        ]);
    return redirect()->route('user.products');
    }

    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        $product->delete();
    return redirect()->route('user.products');
    }

    public function index()
    {
        if (Auth::check()) {
            // Jika login, tampilkan hanya produk milik user
            $products = Product::with('user')->where('user_id', Auth::id())->get();
        } else {
            // Jika tidak login, tampilkan semua produk
            $products = Product::with('user')->get();
        }
        $categories = Category::all();
        return Inertia::render('User/Toko', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
            'kategori' => 'required',
            'deskripsi' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produk', 'public');
        }
        Product::create([
            'user_id' => Auth::id(),
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'image' => $imagePath,
        ]);
        return redirect()->route('user.toko');
    }
}
