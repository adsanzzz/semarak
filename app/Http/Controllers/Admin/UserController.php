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

    public function reportedAccounts()
    {
        \App\Models\Complaint::where(function ($q) {
                $q->where('complaint_type', 'report_account')
                  ->orWhereNotNull('reported_user_id');
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $reports = \App\Models\Complaint::with(['user', 'reportedUser'])
            ->where('complaint_type', 'report_account')
            ->orWhereNotNull('reported_user_id')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'reporter_name' => $r->user?->name ?: $r->sender_name ?: 'N/A',
                    'reported_user_id' => $r->reported_user_id,
                    'reported_name' => $r->reportedUser?->name ?: 'N/A',
                    'reported_nama_toko' => $r->reportedUser?->nama_toko ?: '-',
                    'reported_email' => $r->reportedUser?->email ?: '-',
                    'reported_role' => $r->reportedUser?->role ?: 0,
                    'reported_is_active' => (bool) ($r->reportedUser?->is_active ?? true),
                    'reason' => $r->subject ?: '-',
                    'description' => $r->issue_description ?: '-',
                    'bukti' => $r->bukti ? asset('storage/' . $r->bukti) : null,
                    'created_at' => $r->created_at ? $r->created_at->format('d M Y H:i') : '-',
                ];
            });

        return Inertia::render('Admin/Users/ReportedAccounts', [
            'reports' => $reports,
        ]);
    }
}