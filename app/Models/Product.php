<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'harga',
        'stok',
        'kategori_id',
        'kategori_nama',
        'deskripsi',
        'sub_kategori_id',
        'image',
        'terjual',
        'warna',
        'ukuran',
        'berat',
    ];

    // Relasi ke User (pemilik produk)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Category
    public function category()
{
    return $this->belongsTo(Category::class, 'kategori_id');
}

public function subCategory()
{
    return $this->belongsTo(SubCategory::class, 'sub_kategori_id');
}
}
