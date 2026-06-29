<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        if (!$type) {
            return redirect()->route('admin.users.index', ['type' => 'admin']);
        }

        $roleMap = [
            'admin' => 1,
            'penjual' => 2,
            'pembeli' => 3
        ];

        $role = $roleMap[$type] ?? 1;

        $users = User::where('role', $role)
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function ($u) {
                // Count products
                $productsCount = \App\Models\Product::where('user_id', $u->id)->count();

                // Count orders (For sellers, orders received. For buyers/admins, orders made)
                if ($u->role === 2) {
                    $ordersCount = \App\Models\Order::where('user_id', $u->id)->count();
                } else {
                    $ordersCount = \App\Models\Order::where('buyer_id', $u->id)->count();
                }

                // Count complaints (laporan akun)
                $complaintsCount = \App\Models\Complaint::where('user_id', $u->id)->count();

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'is_active' => $u->is_active,
                    'nama_toko' => $u->nama_toko,
                    'products_count' => $productsCount,
                    'orders_count' => $ordersCount,
                    'complaints_count' => $complaintsCount,
                ];
            });

        return Inertia::render('Admin/Users/index', [
            'users' => $users,
            'type' => $type
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