<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected function managedOrder(int $id): Order
    {
        return Order::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['diterima', 'diproses', 'selesai', 'dibatalkan'])],
        ]);

        $order = $this->managedOrder((int) $id);

        $order->status = $validated['status'];
        $order->save();

        // Update product's terjual count
        $product = $order->product;
        if ($product) {
            $product->terjual = Order::where('product_id', $product->id)
                ->where('status', 'selesai')
                ->sum('jumlah');
            $product->save();
        }

        return back();
    }

    public function accept(Request $request, $id)
    {
        $order = $this->managedOrder((int) $id);

        $paymentStatus = $order->payment_status;
        if (!in_array($paymentStatus, ['waiting_verification', 'paid'])) {
            $paymentStatus = 'waiting_payment';
        }

        $order->update([
            'review_status' => 'diterima',
            'status' => 'diterima',
            'payment_status' => $paymentStatus,
            'rejection_reason' => null,
        ]);

        return back();
    }

    public function confirmPayment(Request $request, $id)
    {
        $order = $this->managedOrder((int) $id);

        $order->update([
            'payment_status' => 'paid',
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', Rule::in([
                'Stok Barang Habis/Kosong',
                'Alamat Luar Jangkauan',
                'Kelebihan Antrean Pesanan',
            ])],
        ]);

        $order = $this->managedOrder((int) $id);

        $order->update([
            'review_status' => 'ditolak',
            'status' => 'dibatalkan',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Update product's terjual count
        $product = $order->product;
        if ($product) {
            $product->terjual = Order::where('product_id', $product->id)
                ->where('status', 'selesai')
                ->sum('jumlah');
            $product->save();
        }

        return back();
    }

    public function destroy($id)
    {
        $order = $this->managedOrder((int) $id);
        $order->delete();

        return back();
    }

    public function review(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:2000'],
            'review_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $order = Order::where('buyer_id', auth()->id())->findOrFail((int) $id);

        if ($order->status !== 'selesai') {
            throw ValidationException::withMessages([
                'rating' => 'Ulasan hanya bisa diberikan setelah pesanan selesai.',
            ]);
        }

        $imagePath = $order->review_image;
        if ($request->hasFile('review_image')) {
            if ($order->review_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($order->review_image);
            }
            $imagePath = $request->file('review_image')->store('reviews', 'public');
        }

        $order->update([
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
            'review_image' => $imagePath,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Ulasan berhasil disimpan.');
    }
}