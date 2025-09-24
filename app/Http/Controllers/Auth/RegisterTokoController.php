<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisterTokoController extends Controller
{
    /**
     * Display the store registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/RegisterToko');
    }

    /**
     * Handle an incoming store registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'nik_penjual' => 'required|string|max:32',
            'nama_lengkap_penjual' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'alamat_penjual' => 'required|string|max:255',
            // tambahkan validasi lain sesuai kebutuhan
        ]);

        $user = User::create([
            'name' => $request->nama_lengkap_penjual,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 2, // 2 = user_toko
            'nama_toko' => $request->nama_toko,
            'nik_penjual' => $request->nik_penjual,
            'phone' => $request->phone,
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

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
