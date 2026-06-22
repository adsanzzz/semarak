<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Complaint;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function orders()
    {
        $orders = Order::with(['buyer', 'product'])->get();
        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
        ]);
    }

    public function complaints()
    {
        $buyerComplaints = Complaint::with(['user', 'order', 'forwardedFrom'])
            ->where(function ($query) {
                $query->where('complaint_type', 'buyer')
                    ->orWhere(function ($fallbackQuery) {
                        $fallbackQuery->whereNull('complaint_type')
                            ->whereHas('user', function ($userQuery) {
                                $userQuery->whereIn('role', [1, 3]);
                            });
                    });
            })
            ->latest()
            ->get();

        $sellerComplaints = Complaint::with(['user', 'order', 'forwardedFrom'])
            ->where(function ($query) {
                $query->where('complaint_type', 'seller')
                    ->orWhere(function ($fallbackQuery) {
                        $fallbackQuery->whereNull('complaint_type')
                            ->whereHas('user', function ($userQuery) {
                                $userQuery->where('role', 2);
                            });
                    });
            })
            ->whereNull('forwarded_from_id')
            ->latest()
            ->get();

        $sellers = User::query()
            ->where('role', 2)
            ->select('id', 'name', 'nama_toko')
            ->orderBy('nama_toko')
            ->orderBy('name')
            ->get();

        $forwardedIds = Complaint::whereNotNull('forwarded_from_id')->pluck('forwarded_from_id')->all();

        $buyerComplaints = $buyerComplaints->map(function (Complaint $complaint) use ($forwardedIds) {
            $complaint->complaint_group = 'buyer';
            $complaint->is_forwarded = in_array($complaint->id, $forwardedIds, true);

            return $complaint;
        })->values();

        $sellerComplaints = $sellerComplaints->map(function (Complaint $complaint) {
            $complaint->complaint_group = 'seller';

            return $complaint;
        })->values();

        return Inertia::render('Admin/Complaints', [
            'buyerComplaints' => $buyerComplaints,
            'sellerComplaints' => $sellerComplaints,
            'sellers' => $sellers,
        ]);
    }

    public function storeComplaint(Request $request)
    {
        $request->validate([
            'name_or_store' => 'required|string|max:255',
            'issue_description' => 'required|string',
            'input' => 'required|string',
        ]);

        $complaintType = auth()->user()?->role === 2 ? 'seller' : 'buyer';

        // Asumsikan user_id dari admin atau null, order_id null untuk komplain admin
        Complaint::create([
            'user_id' => auth()->id(), // atau null jika tidak terkait user
            'order_id' => null,
            'complaint_type' => $complaintType,
            'sender_name' => $request->name_or_store,
            'issue_description' => $request->issue_description,
            'input' => $request->input,
        ]);

        return redirect()->route('admin.komplain')->with('success', 'Komplain berhasil ditambahkan');
    }

    public function forwardComplaint(Request $request, $id)
    {
        $request->validate([
            'seller_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $complaint = Complaint::with('user')->findOrFail($id);
        $sellerId = (int) $request->input('seller_id');
        $seller = User::where('role', 2)->find($sellerId);

        if (! $seller) {
            return back()->with('error', 'Toko tujuan tidak valid.');
        }

        if (($complaint->complaint_type ?? ($complaint->user?->role === 2 ? 'seller' : 'buyer')) !== 'buyer') {
            return back()->with('error', 'Hanya komplain pembeli yang bisa diteruskan ke penjual.');
        }

        if (Complaint::where('forwarded_from_id', $complaint->id)->where('user_id', $sellerId)->exists()) {
            return back()->with('error', 'Komplain ini sudah diteruskan ke penjual.');
        }

        Complaint::create([
            'user_id' => $sellerId,
            'order_id' => $complaint->order_id,
            'complaint_type' => 'seller',
            'forwarded_from_id' => $complaint->id,
            'sender_name' => $complaint->sender_name,
            'issue_description' => $complaint->issue_description,
            'input' => $complaint->input,
            'subject' => $complaint->subject,
            'message' => $complaint->message,
        ]);

        return back()->with('success', 'Komplain berhasil diteruskan ke toko ' . ($seller->nama_toko ?: $seller->name) . '.');
    }

    public function replyComplaint(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => ['required', 'string', 'max:2000'],
        ]);

        $complaint = Complaint::with('user')->findOrFail($id);
        $group = $complaint->complaint_type
            ?? (($complaint->user?->role === 2) ? 'seller' : 'buyer');

        if ($group !== 'seller') {
            return back()->with('error', 'Balasan admin hanya tersedia untuk tabel komplain penjual.');
        }

        $complaint->update([
            'admin_reply' => trim((string) $request->input('admin_reply')),
        ]);

        return back()->with('success', 'Balasan admin berhasil disimpan.');
    }

    public function products()
    {
        $products = Product::with(['user', 'category', 'subCategory'])->get();
        return Inertia::render('Admin/Products', [
            'products' => $products,
        ]);
    }

    public function certifications()
    {
        $certifications = User::query()
            ->where('role', 2)
            ->whereNotNull('sertifikasi_file')
            ->latest('updated_at')
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nama_toko' => $user->nama_toko,
                    'nik_penjual' => $user->nik_penjual,
                    'sertifikasi_jenis' => $user->sertifikasi_jenis,
                    'sertifikasi_file' => $user->sertifikasi_file,
                    'sertifikasi_status' => $user->sertifikasi_status ?? 'pending',
                    'updated_at' => $user->updated_at,
                ];
            });

        return Inertia::render('Admin/Sertifikasi', [
            'certifications' => $certifications,
        ]);
    }

    public function approveCertification($id)
    {
        $user = User::where('role', 2)->findOrFail($id);
        $user->update(['sertifikasi_status' => 'approved']);

        return back()->with('success', 'Sertifikasi berhasil disetujui');
    }

    public function rejectCertification($id)
    {
        $user = User::where('role', 2)->findOrFail($id);
        $user->update(['sertifikasi_status' => 'rejected']);

        return back()->with('success', 'Sertifikasi berhasil ditolak');
    }

    public function deactivateProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false]);
        return redirect()->route('admin.products')->with('success', 'Produk berhasil dinonaktifkan');
    }
}