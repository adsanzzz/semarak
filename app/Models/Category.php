<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
    'icon',
    'nama_kategori',
    'deskripsi',
    'jumlah_toko',
];
}
