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

        return back();
    }

    public function accept(Request $request, $id)
    {
        $order = $this->managedOrder((int) $id);

       $order->update([
    'review_status' => 'diterima',
    'status' => 'diterima',
    'payment_status' => 'waiting_payment',
    'rejection_reason' => null,
]);

        return back();
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
        ]);

        $order = Order::where('buyer_id', auth()->id())->findOrFail((int) $id);

        if ($order->status !== 'selesai') {
            throw ValidationException::withMessages([
                'rating' => 'Ulasan hanya bisa diberikan setelah pesanan selesai.',
            ]);
        }

        $order->update([
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Ulasan berhasil disimpan.');
    }
}