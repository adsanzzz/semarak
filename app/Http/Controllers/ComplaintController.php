<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::latest()->get();

        return Inertia::render('Admin/KelolaPengaduan', [
            'complaints' => $complaints
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        if ($user?->role === 2) {
            $complaints = Complaint::where('user_id', $user->id)
                ->whereNull('forwarded_from_id')
                ->latest()
                ->get();

            $forwardedComplaints = Complaint::where('user_id', $user->id)
                ->whereNotNull('forwarded_from_id')
                ->with('forwardedFrom')
                ->latest()
                ->get();

            return Inertia::render('PengaduanKomplain', [
                'complaints' => $complaints,
                'forwardedComplaints' => $forwardedComplaints,
            ]);
        }

        $complaints = Complaint::where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('PengaduanKomplain', [
            'complaints' => $complaints,
            'forwardedComplaints' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_or_store' => 'required|string|max:255',
            'issue_description' => 'required|string',
            'input' => 'required|string',
        ]);

        $complaintType = auth()->user()?->role === 2 ? 'seller' : 'buyer';

        Complaint::create([
            'user_id' => auth()->id(),
            'order_id' => null,
            'complaint_type' => $complaintType,
            'sender_name' => $request->name_or_store,
            'issue_description' => $request->issue_description,
            'input' => $request->input,
        ]);

        return redirect()->route('pengaduan')->with('success', 'Pengaduan berhasil dikirim.');
    }
}