<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Admin/Categories/index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'nama_subkategori' => 'required|string|max:255',
        ]);

        $category = Category::firstOrCreate([
            'nama_kategori' => $request->nama_kategori
        ]);

        Subcategory::create([
            'category_id' => $category->id,
            'nama_subkategori' => $request->nama_subkategori
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        Subcategory::findOrFail($id)->delete();

        return redirect()->back();
    }
}