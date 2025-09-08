<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'nama', 'harga', 'stok', 'kategori', 'deskripsi', 'image', 'terjual'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
