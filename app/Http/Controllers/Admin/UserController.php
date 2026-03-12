<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
   public function index()
{
    $admin = User::where('role', 1)->get();
    $penjual = User::where('role', 2)->get();
    $pembeli = User::where('role', 3)->get();

    return Inertia::render('Admin/Users/index', [
        'admin' => $admin,
        'penjual' => $penjual,
        'pembeli' => $pembeli,
    ]);
}

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}