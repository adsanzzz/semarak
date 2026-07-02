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
            'qty'        => 'nullable|integer|min:1',
            'variations' => 'nullable|array',
        ]);

        $qty = (int) $request->input('qty', 1);
        $chosenVariations = $request->input('variations', []);

        $product = Product::findOrFail($request->product_id);

        if ($product->stok < $qty) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $penjual = User::find($product->user_id);
        $cart = session('cart', []);

        // Cek jika produk dengan variasi yang sama sudah ada di keranjang
        $found = false;
        foreach ($cart as &$item) {
            $itemVariations = $item['variations'] ?? [];
            if ($item['id'] == $product->id && $itemVariations === $chosenVariations) {
                if ($product->stok < ($item['qty'] + $qty)) {
                    return back()->with('error', 'Stok tidak mencukupi untuk jumlah total di keranjang.');
                }
                $item['qty'] += $qty;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'id'            => $product->id,
                'name'          => $product->nama,
                'price'         => $product->harga,
                'qty'           => $qty,
                'image'         => $product->image ? asset('storage/' . $product->image) : null,
                'penjual_phone' => $penjual ? $penjual->phone : '',
                'variations'    => $chosenVariations,
                'stok'          => $product->stok,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    // Update jumlah produk di keranjang
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'variations' => 'nullable|array',
        ]);

        $cart = session('cart', []);
        $chosenVariations = $request->input('variations', []);

        foreach ($cart as &$item) {
            $itemVariations = $item['variations'] ?? [];
            if ($item['id'] == $request->product_id && $itemVariations === $chosenVariations) {
                $product = Product::find($request->product_id);
                if ($product && $product->stok < $request->qty) {
                    return back()->with('error', 'Stok tidak mencukupi.');
                }
                $item['qty'] = $request->qty;
                break;
            }
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    // Hapus produk dari keranjang
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variations' => 'nullable|array',
        ]);

        $cart = session('cart', []);
        $chosenVariations = $request->input('variations', []);

        $cart = array_filter($cart, function ($item) use ($request, $chosenVariations) {
            $itemVariations = $item['variations'] ?? [];
            return !($item['id'] == $request->product_id && $itemVariations === $chosenVariations);
        });

        session(['cart' => array_values($cart)]);

        return back()->with('success', 'Produk dihapus dari keranjang');
    }
}
