<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'nama_toko',
        'nik_penjual',
        'nama_lengkap_penjual',
        'alamat_penjual',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'kategori_usaha',
        'modal_usaha',
        'omset_tahun',
        'sertifikasi_jenis',
        'sertifikasi_file',
        'sertifikasi_status',
        'sertifikasi_halal',
        'sertifikasi_haki',
        'sosmed',
        'sosmed_instagram',
        'sosmed_tiktok',
        'sosmed_platform',
        'tautan_marketplace',
        'informasi_kemitraan',
        'pelatihan_usaha',
        'bank_tujuan',
        'nama_rekening',
        'norek',
        'qris_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
