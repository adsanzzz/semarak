<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Complaint;
use App\Models\Product;
use App\Models\User;
use App\Models\ModerationKeyword;
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

    public function products(Request $request)
    {
        $search = $request->query('search');
        $filterSuspicious = $request->query('suspicious');

        $keywords = ModerationKeyword::pluck('keyword')->all();

        $query = Product::with(['user', 'category', 'subCategory']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->get()->map(function($product) use ($keywords) {
            $isSuspicious = false;
            $matchedKeywords = [];
            
            $textToSearch = strtolower($product->nama . ' ' . $product->deskripsi);
            foreach ($keywords as $kw) {
                if (str_contains($textToSearch, strtolower($kw))) {
                    $isSuspicious = true;
                    $matchedKeywords[] = $kw;
                }
            }
            
            $product->is_suspicious = $isSuspicious;
            $product->matched_keywords = $matchedKeywords;
            return $product;
        });

        if ($filterSuspicious === '1') {
            $products = $products->filter(function($p) {
                return $p->is_suspicious;
            })->values();
        }

        return Inertia::render('Admin/Products', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'suspicious' => $filterSuspicious === '1' ? true : false,
            ]
        ]);
    }

    public function moderationKeywords()
    {
        $keywords = ModerationKeyword::latest()->get();
        return Inertia::render('Admin/ModerationKeywords', [
            'keywords' => $keywords
        ]);
    }

    public function storeModerationKeyword(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:moderation_keywords,keyword',
        ]);

        ModerationKeyword::create([
            'keyword' => strtolower(trim($request->keyword))
        ]);

        return back()->with('success', 'Keyword filter berhasil ditambahkan');
    }

    public function destroyModerationKeyword($id)
    {
        $keyword = ModerationKeyword::findOrFail($id);
        $keyword->delete();

        return back()->with('success', 'Keyword filter berhasil dihapus');
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
        return back()->with('success', 'Produk berhasil dinonaktifkan');
    }

    public function productReports()
    {
        $products = Product::whereHas('complaints', function($q) {
            $q->where('complaint_type', 'report_product');
        })
        ->with(['user', 'category', 'complaints' => function($q) {
            $q->where('complaint_type', 'report_product')->with('user');
        }])
        ->get()
        ->map(function($product) {
            $reportersCount = $product->complaints->count();
            
            $reports = $product->complaints->map(function($complaint) {
                return [
                    'id' => $complaint->id,
                    'reporter_name' => $complaint->user?->name ?? $complaint->sender_name ?? 'Pembeli',
                    'reason' => $complaint->subject,
                    'description' => $complaint->issue_description,
                    'bukti_url' => $complaint->bukti ? asset('storage/' . $complaint->bukti) : null,
                    'created_at' => $complaint->created_at->format('d M Y H:i'),
                ];
            });

            return [
                'id' => $product->id,
                'nama' => $product->nama,
                'harga' => $product->harga,
                'stok' => $product->stok,
                'is_active' => $product->is_active,
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'toko' => $product->user?->nama_toko ?? $product->user?->name ?? '-',
                'category' => $product->category?->nama_kategori ?? '-',
                'reporters_count' => $reportersCount,
                'reports' => $reports,
            ];
        });

        return Inertia::render('Admin/ProductReports', [
            'reportedProducts' => $products
        ]);
    }
}