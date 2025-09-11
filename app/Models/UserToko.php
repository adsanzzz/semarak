<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToko extends Model
{
    use HasFactory;
    protected $table = 'usertoko';
    protected $fillable = [
        'user_id',
        'nama_toko',
        'nik_penjual',
        'nama_lengkap_penjual',
        'email',
        'phone',
        'password',
        'alamat_penjual',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'kategori_usaha',
        'modal_usaha',
        'omset_tahun',
        'sertifikasi_halal',
        'sertifikasi_haki',
        'sosmed',
        'tautan_marketplace',
        'informasi_kemitraan',
        'pelatihan_usaha',
        'bank_tujuan',
        'nama_rekening',
        'norek',
    ];
    protected $hidden = ['password'];
}
