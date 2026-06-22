<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

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

    public function deactivate(Request $request, $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $user = User::findOrFail($id);

        if ($user->role !== 2) {
            return back()->with('error', 'Hanya akun penjual yang dapat dinonaktifkan.');
        }

        if (! $user->is_active) {
            return back()->with('error', 'Akun penjual ini sudah nonaktif.');
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'Akun penjual berhasil dinonaktifkan. Alasan: ' . $request->string('reason'));
    }

    public function activate($id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 2) {
            return back()->with('error', 'Hanya akun penjual yang dapat diaktifkan.');
        }

        if ($user->is_active) {
            return back()->with('error', 'Akun penjual ini sudah aktif.');
        }

        $user->update(['is_active' => true]);

        return back()->with('success', 'Akun penjual berhasil diaktifkan kembali.');
    }
}