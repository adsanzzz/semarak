<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class CheckoutController extends \App\Http\Controllers\Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong');
        }

        // Ambil nomor WA dari session jika ada
        $nomorWaSession = session('nomor_wa_penjual');

        // Ambil produk pertama di keranjang
        $firstProductId = $cart[0]['id'];
        $product = Product::find($firstProductId);

        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan');
        }

        // Ambil penjual dari produk pertama
        $penjual = User::find($product->user_id);

        // Pilih nomor WA: dari session jika ada, jika tidak dari penjual
        $nomorRaw = $nomorWaSession ?: ($penjual ? $penjual->phone : null);

        if (!$nomorRaw) {
            return back()->with('error', 'Nomor penjual tidak ditemukan');
        }

        // Format nomor WA (hilangkan spasi, strip, dan 0 di depan, ganti dengan 62)
        $nomor = preg_replace('/[^0-9]/', '', $nomorRaw);
        if (substr($nomor, 0, 1) === '0') {
            $nomor = '62' . substr($nomor, 1);
        }

        // Buat draft pesan WhatsApp
        $pesan = "Halo, saya ingin memesan produk berikut:\n";
        foreach ($cart as $item) {
            $pesan .= "- {$item['name']} x{$item['qty']} (Rp " . number_format($item['price'], 0, ',', '.') . ")\n";
        }
        $pesan .= "\nTotal: Rp " . number_format(array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart)), 0, ',', '.');
        $pesan .= "\nMohon konfirmasi pesanan saya. Terima kasih.";

        // Redirect ke wa.me
        $waUrl = "https://wa.me/{$nomor}?text=" . urlencode($pesan);

        // Kosongkan keranjang setelah checkout
        session()->forget('cart');

        return redirect()->away($waUrl);
    }
}
