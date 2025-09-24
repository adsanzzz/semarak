<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
use App\Models\User;

class KeranjangController extends \App\Http\Controllers\Controller
{
    // Tampilkan isi keranjang
    public function index()
    {
        $cart = session('cart', []);
        return Inertia::render('User/Keranjang', [
            'items' => $cart,
        ]);
    }

    // Tambah produk ke keranjang
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $penjual = User::find($product->user_id);

        $cart = session('cart', []);

        // Cek jika produk sudah ada di keranjang
        $found = false;
        foreach ($cart as &$item) {
            if ($item['id'] == $product->id) {
                $item['qty'] += 1;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $cart[] = [
                'id'    => $product->id,
                'name'  => $product->nama,
                'price' => $product->harga,
                'qty'   => 1,
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'penjual_phone' => $penjual ? $penjual->phone : '',
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    // Hapus produk dari keranjang
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = session('cart', []);
        $cart = array_filter($cart, function ($item) use ($request) {
            return $item['id'] != $request->product_id;
        });

        session(['cart' => array_values($cart)]);

        return back()->with('success', 'Produk dihapus dari keranjang');
    }
}
