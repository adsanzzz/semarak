<?php

namespace App\Http\Controllers;

use App\Models\UserToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserTokoController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/RegisterToko');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'nik_penjual' => 'required|string|max:32',
            'nama_lengkap_penjual' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:32',
            'password' => 'required|string|min:8|confirmed',
            'alamat_penjual' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'kategori_usaha' => 'required|string',
            'modal_usaha' => 'required|string',
            'omset_tahun' => 'required|string',
            'sertifikasi_halal' => 'nullable|string',
            'sertifikasi_haki' => 'nullable|string',
            'sosmed' => 'nullable|string',
            'tautan_marketplace' => 'nullable|string',
            'informasi_kemitraan' => 'nullable|string',
            'pelatihan_usaha' => 'nullable|string',
            'bank_tujuan' => 'required|string',
            'nama_rekening' => 'required|string',
            'norek' => 'required|string',
        ]);

        $user = Auth::user();
        $toko = UserToko::create([
            'user_id' => $user ? $user->id : null,
            'nama_toko' => $request->nama_toko,
            'nik_penjual' => $request->nik_penjual,
            'nama_lengkap_penjual' => $request->nama_lengkap_penjual,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'alamat_penjual' => $request->alamat_penjual,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'kategori_usaha' => $request->kategori_usaha,
            'modal_usaha' => $request->modal_usaha,
            'omset_tahun' => $request->omset_tahun,
            'sertifikasi_halal' => $request->sertifikasi_halal,
            'sertifikasi_haki' => $request->sertifikasi_haki,
            'sosmed' => $request->sosmed,
            'tautan_marketplace' => $request->tautan_marketplace,
            'informasi_kemitraan' => $request->informasi_kemitraan,
            'pelatihan_usaha' => $request->pelatihan_usaha,
            'bank_tujuan' => $request->bank_tujuan,
            'nama_rekening' => $request->nama_rekening,
            'norek' => $request->norek,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi toko berhasil! Silakan login.');
    }
}
