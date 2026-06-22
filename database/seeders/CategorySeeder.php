<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $fashion = Category::firstOrCreate([
            'nama_kategori' => 'Fashion',
        ], [
            'deskripsi' => 'Kategori produk pakaian dan aksesori',
            'jumlah_toko' => 0,
        ]);

        $elektronik = Category::firstOrCreate([
            'nama_kategori' => 'Elektronik',
        ], [
            'deskripsi' => 'Kategori produk elektronik dan gadget',
            'jumlah_toko' => 0,
        ]);

        $rumahTangga = Category::firstOrCreate([
            'nama_kategori' => 'Rumah Tangga',
        ], [
            'deskripsi' => 'Kategori kebutuhan rumah tangga',
            'jumlah_toko' => 0,
        ]);

        SubCategory::firstOrCreate([
            'category_id' => $fashion->id,
            'nama_subkategori' => 'Pakaian Pria',
        ]);

        SubCategory::firstOrCreate([
            'category_id' => $fashion->id,
            'nama_subkategori' => 'Pakaian Wanita',
        ]);

        SubCategory::firstOrCreate([
            'category_id' => $elektronik->id,
            'nama_subkategori' => 'Smartphone',
        ]);

        SubCategory::firstOrCreate([
            'category_id' => $elektronik->id,
            'nama_subkategori' => 'Aksesori Gadget',
        ]);

        SubCategory::firstOrCreate([
            'category_id' => $rumahTangga->id,
            'nama_subkategori' => 'Peralatan Dapur',
        ]);

        SubCategory::firstOrCreate([
            'category_id' => $rumahTangga->id,
            'nama_subkategori' => 'Kebersihan Rumah',
        ]);
    }
}
