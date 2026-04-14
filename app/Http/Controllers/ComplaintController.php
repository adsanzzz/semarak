<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
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
}