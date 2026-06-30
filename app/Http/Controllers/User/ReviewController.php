<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $reviews = Order::with(['product:id,nama,user_id', 'buyer:id,name'])
            ->whereHas('product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'selesai')
            ->whereNotNull('rating')
            ->latest('reviewed_at')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'product' => $order->product,
                    'buyer' => $order->buyer,
                    'rating' => $order->rating,
                    'review_text' => $order->review_text,
                    'review_image' => $order->review_image ? asset('storage/' . $order->review_image) : null,
                    'reviewed_at' => $order->reviewed_at,
                    'seller_reply' => $order->seller_reply ?? null,
                ];
            });

        return Inertia::render('User/Reviews', [
            'reviews' => $reviews,
        ]);
    }

    public function reply(Request $request, $orderId)
    {
        $request->validate([
            'seller_reply' => ['required', 'string', 'max:2000'],
        ]);

        $order = Order::with('product')->findOrFail($orderId);

        if ((int) $order->product->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $order->seller_reply = $request->input('seller_reply');
        $order->save();

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }
}
