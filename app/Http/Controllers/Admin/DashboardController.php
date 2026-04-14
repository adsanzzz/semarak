<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data ringkasan untuk dashboard admin
        $totalUsers     = User::count();
        $totalProducts  = Product::count();
        $totalCategories = Category::count();

        return Inertia::render('Admin/AdminDashboard', [
            'totalUsers'      => $totalUsers,
            'totalProducts'   => $totalProducts,
            'totalCategories' => $totalCategories,
            'categories'      => Category::all(),
        ]);
    }

    public function ordersHistory()
    {
        $orders = Order::with('product', 'buyer')->latest()->get();

        return Inertia::render('Admin/OrdersHistory', [
            'orders' => $orders,
        ]);
    }

    public function complaints()
    {
        // Placeholder for complaints, since not persisted yet
        $complaints = []; // In future, fetch from Complaint model

        return Inertia::render('Admin/Complaints', [
            'complaints' => $complaints,
        ]);
    }

    public function products()
    {
        $products = Product::with('user')->latest()->get();

        return Inertia::render('Admin/Products', [
            'products' => $products,
        ]);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus');
    }
}
