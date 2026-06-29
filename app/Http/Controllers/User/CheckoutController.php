<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckoutController extends \App\Http\Controllers\Controller
{
    public function prepareFromProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $qty = (int) ($request->qty ?? 1);
        $product = Product::with('user')->findOrFail($request->product_id);

        session([
            'checkout_items' => [
                $this->mapCheckoutItem($product, $qty),
            ],
        ]);

        return redirect()->route('checkout.index');
    }

    public function prepareFromCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $requestItems = collect($request->items)->keyBy('product_id');
        $productIds = $requestItems->keys()->all();

        $products = Product::with('user')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $checkoutItems = [];
        foreach ($productIds as $productId) {
            $product = $products->get($productId);
            if (!$product) {
                continue;
            }

            $qty = (int) ($requestItems->get($productId)['qty'] ?? 1);
            $checkoutItems[] = $this->mapCheckoutItem($product, $qty);
        }

        if (empty($checkoutItems)) {
            return redirect()->route('keranjang.index')->with('error', 'Tidak ada item valid untuk checkout.');
        }

        session(['checkout_items' => $checkoutItems]);

        return redirect()->route('checkout.index');
    }

    public function index()
    {
        $checkoutItems = session('checkout_items', []);

        if (empty($checkoutItems)) {
            return redirect()->route('keranjang.index')->with('error', 'Pilih produk yang ingin di-checkout terlebih dahulu.');
        }

        $stores = collect($checkoutItems)
            ->pluck('seller')
            ->filter()
            ->unique('id')
            ->values();

        $summary = [
            'total_items' => collect($checkoutItems)->sum('qty'),
            'total_price' => collect($checkoutItems)->sum(function ($item) {
                return ((int) $item['price']) * ((int) $item['qty']);
            }),
        ];

        return Inertia::render('User/Checkout', [
            'checkoutItems' => $checkoutItems,
            'stores' => $stores,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|in:ambil_toko,antar_rumah',
            'location_map' => 'nullable|string|max:1000|required_if:shipping_method,antar_rumah',
            'payment_method' => 'required|in:qris,cod,cash',
        ]);

        $paymentMethod = $request->payment_method;
        if ($paymentMethod === 'cash') {
            $paymentMethod = 'cod';
        }

        $checkoutItems = session('checkout_items', []);

        if (empty($checkoutItems)) {
            return redirect()->route('keranjang.index')->with('error', 'Data checkout tidak ditemukan.');
        }

        $createdOrderIds = [];

        DB::transaction(function () use ($checkoutItems, $request, &$createdOrderIds, $paymentMethod) {
            foreach ($checkoutItems as $item) {
    $sellerId = (int) ($item['seller']['id'] ?? 0);

    if ($sellerId <= 0) {
        abort(422, 'Penjual produk tidak valid.');
    }

    $product = Product::findOrFail($item['product_id']);

    if ($product->stok < $item['qty']) {
        abort(422, "Stok produk {$product->nama} tidak mencukupi.");
    }

    $order = Order::create([
        'product_id' => $item['product_id'],
        'buyer_id' => Auth::id(),
        'user_id' => $sellerId,
        'jumlah' => $item['qty'],
        'total_harga' => $item['price'] * $item['qty'],
        'status' => 'pending',
        'shipping_method' => $request->shipping_method,
        'delivery_location' => $request->shipping_method === 'antar_rumah' ? $request->location_map : null,
        'payment_method' => $paymentMethod,
        'payment_status' => $paymentMethod === 'qris'
    ? 'waiting_confirmation'
    : 'unpaid',
    ]);

    $product->decrement('stok', $item['qty']);

    $createdOrderIds[] = $order->id;
}
        });

        $checkoutStores = collect($checkoutItems)
            ->pluck('seller')
            ->filter()
            ->unique('id')
            ->values()
            ->all();

        session([
    'checkout_result' => [
        'payment_method' => $paymentMethod,
        'order_ids' => $createdOrderIds,
        'stores' => $checkoutStores,
        'total_payment' => collect($checkoutItems)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        }),
    ],
]);

        // Hapus item yang sudah di-checkout dari keranjang sesi.
        $checkedOutIds = collect($checkoutItems)->pluck('product_id')->all();
        $cart = session('cart', []);
        $updatedCart = array_values(array_filter($cart, function ($item) use ($checkedOutIds) {
            return !in_array($item['id'], $checkedOutIds);
        }));

        session(['cart' => $updatedCart]);
        session()->forget('checkout_items');

        return redirect()->route('checkout.success');
    }

    public function confirmQrisPayment(Request $request)
    {
        $checkoutResult = session('checkout_result');

        if (!$checkoutResult || ($checkoutResult['payment_method'] ?? null) !== 'qris') {
            return redirect()->route('user.riwayat-pemesanan')->with('error', 'Data pembayaran QRIS tidak ditemukan.');
        }

        $orderIds = $checkoutResult['order_ids'] ?? [];

        if (empty($orderIds)) {
            return redirect()->route('user.riwayat-pemesanan')->with('error', 'Pesanan tidak ditemukan.');
        }

        Order::whereIn('id', $orderIds)
            ->where('buyer_id', Auth::id())
            ->update([
                'payment_status' => 'paid',
            ]);

        session()->forget('checkout_result');

        return redirect()->route('user.riwayat-pemesanan')->with('success', 'Pembayaran Berhasil');
    }

    private function mapCheckoutItem(Product $product, int $qty): array
    {
        $seller = $product->user;

        return [
            'product_id' => $product->id,
            'name' => $product->nama,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
            'price' => (int) $product->harga,
            'qty' => $qty,
            'subtotal' => (int) $product->harga * $qty,
            'seller' => [
                'id' => $seller?->id,
                'name' => $seller?->name,
                'nama_toko' => $seller?->nama_toko,
                'bank_tujuan' => $seller?->bank_tujuan,
                'nama_rekening' => $seller?->nama_rekening,
                'norek' => $seller?->norek,
                'qris_image' => $seller?->qris_image ? asset('storage/' . $seller->qris_image) : null,
                'latitude' => $seller?->latitude,
                'longitude' => $seller?->longitude,
            ],
        ];
    }

    public function uploadProof(Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'order_id' => 'required_without:order_ids',
            'order_ids' => 'required_without:order_id',
        ]);

        $path = $request->file('payment_proof')
            ->store('payment-proofs', 'public');

        $orderIds = [];
        if ($request->has('order_ids')) {
            $orderIds = is_array($request->order_ids) 
                ? $request->order_ids 
                : json_decode($request->order_ids, true) ?? explode(',', $request->order_ids);
        } elseif ($request->has('order_id')) {
            $orderIds = [$request->order_id];
        }

        Order::whereIn('id', $orderIds)
            ->where('buyer_id', Auth::id())
            ->update([
                'payment_proof' => $path,
                'payment_status' => 'waiting_verification',
            ]);

        if ($request->has('order_ids')) {
            session()->forget('checkout_result');
        }

        return back()->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}