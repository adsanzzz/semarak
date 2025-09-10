<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
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
}
