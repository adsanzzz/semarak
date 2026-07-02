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
        'satuan',
        'is_active',
        'deactivated_reason',
        'variations',
    ];

    protected $casts = [
        'variations' => 'array',
    ];

    protected $appends = [
        'images',
        'images_url',
        'cover_image_url',
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'reported_product_id');
    }

    public function appeals()
    {
        return $this->hasMany(ProductAppeal::class);
    }

    public function latestAppeal()
    {
        return $this->hasOne(ProductAppeal::class)->latestOfMany();
    }

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return count($decoded) > 0 ? $decoded[0] : null;
        }

        return $value;
    }

    public function getImagesAttribute()
    {
        $value = $this->attributes['image'] ?? null;
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return [$value];
    }

    public function getImagesUrlAttribute()
    {
        return array_map(fn($img) => asset('storage/' . $img), $this->images);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
