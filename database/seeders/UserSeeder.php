<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User Toko
        User::create([
            'name' => 'User Toko',
            'email' => 'toko@example.com',
            'password' => Hash::make('password'),
            'role' => 2, // user_toko
            'nama_toko' => 'Toko Sukses Makmur',
            'nik_penjual' => '1234567890123456',
            'nama_lengkap_penjual' => 'Budi Santoso',
            'phone' => '081234567890',
            'alamat_penjual' => 'Jl. Raya Karanganyar No. 10',
            'provinsi' => 'Jawa Tengah',
            'kabupaten' => 'Karanganyar',
            'kecamatan' => 'Karanganyar',
            'kelurahan' => 'Bejen',
            'kategori_usaha' => 'Makanan & Minuman',
            'modal_usaha' => 'Rp 50.000.000',
            'omset_tahun' => 'Rp 200.000.000',
            'sertifikasi_halal' => true,
            'sertifikasi_haki' => true,
            'sosmed' => 'https://instagram.com/tokosukses',
            'tautan_marketplace' => 'https://tokopedia.com/tokosukses',
            'informasi_kemitraan' => 'Mitra dengan PT Maju Jaya',
            'pelatihan_usaha' => 'Pelatihan UMKM 2024',
            'bank_tujuan' => 'BCA',
            'nama_rekening' => 'Budi Santoso',
            'norek' => '1234567890',
        ]);

        // User Buyer
        User::create([
            'name' => 'User Buyer',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'role' => 3, // user_buyer
            // Kolom tambahan dibiarkan null
        ]);

        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1, // admin
            // Kolom tambahan dibiarkan null
        ]);
    }
}
