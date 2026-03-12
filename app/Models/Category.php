<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
    'kategori',
    'nama_kategori',
];

public function subcategories()
{
    return $this->hasMany(Subcategory::class);
}

}


