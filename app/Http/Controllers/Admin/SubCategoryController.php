<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    public function destroy($id)
    {
        $sub = SubCategory::findOrFail($id);
        $sub->delete();

        return back();
    }
}