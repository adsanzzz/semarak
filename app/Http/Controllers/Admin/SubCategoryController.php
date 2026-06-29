<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function destroy($id)
    {
        $sub = SubCategory::findOrFail($id);
        $sub->delete();

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'nama_subkategori' => 'required|string|max:255',
        ]);

        $sub = SubCategory::with('category')->findOrFail((int) $id);

        if ($sub->category) {
            $sub->category->update([
                'nama_kategori' => $request->nama_kategori,
            ]);
        }

        $sub->update([
            'nama_subkategori' => $request->nama_subkategori,
        ]);

        return back();
    }
}