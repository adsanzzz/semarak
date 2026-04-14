<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
{
    $categories = Category::with('subCategories')->get();

    return Inertia::render('Admin/Categories/Index', [
        'categories' => $categories
    ]);
}
   public function store(Request $request)
{
    $request->validate([
        'nama_kategori' => 'required|string|max:255',
        'nama_subkategori' => 'required|string|max:255',
    ]);

    // cari atau buat category
    $category = Category::firstOrCreate([
        'nama_kategori' => $request->nama_kategori
    ]);

    // simpan sub category
    \App\Models\SubCategory::create([
        'category_id' => $category->id,
        'nama_subkategori' => $request->nama_subkategori
    ]);

    return redirect()->back();
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'nama_kategori' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'kategori' => $request->kategori,
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}