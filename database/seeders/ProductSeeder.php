<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'toko@example.com')->first();

        if (! $seller) {
            return;
        }

        $fashion = Category::where('nama_kategori', 'Fashion')->first();
        $elektronik = Category::where('nama_kategori', 'Elektronik')->first();
        $rumahTangga = Category::where('nama_kategori', 'Rumah Tangga')->first();

        $products = [
            [
                'nama' => 'Kaos Polos Premium',
                'harga' => 85000,
                'stok' => 25,
                'kategori_id' => $fashion?->id,
                'kategori_nama' => $fashion?->nama_kategori,
                'sub_kategori_id' => SubCategory::where('category_id', $fashion?->id)->where('nama_subkategori', 'Pakaian Pria')->value('id'),
                'deskripsi' => 'Kaos katun nyaman dipakai harian.',
                'warna' => 'Hitam',
                'ukuran' => 'L',
                'berat' => '250 gram',
            ],
            [
                'nama' => 'Smartphone Entry Level',
                'harga' => 1750000,
                'stok' => 12,
                'kategori_id' => $elektronik?->id,
                'kategori_nama' => $elektronik?->nama_kategori,
                'sub_kategori_id' => SubCategory::where('category_id', $elektronik?->id)->where('nama_subkategori', 'Smartphone')->value('id'),
                'deskripsi' => 'HP untuk kebutuhan harian dan bisnis ringan.',
                'warna' => 'Biru',
                'ukuran' => null,
                'berat' => '180 gram',
            ],
            [
                'nama' => 'Panci Serbaguna',
                'harga' => 120000,
                'stok' => 18,
                'kategori_id' => $rumahTangga?->id,
                'kategori_nama' => $rumahTangga?->nama_kategori,
                'sub_kategori_id' => SubCategory::where('category_id', $rumahTangga?->id)->where('nama_subkategori', 'Peralatan Dapur')->value('id'),
                'deskripsi' => 'Panci serbaguna untuk kebutuhan dapur rumah tangga.',
                'warna' => 'Silver',
                'ukuran' => null,
                'berat' => '900 gram',
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                [
                    'user_id' => $seller->id,
                    'nama' => $productData['nama'],
                ],
                [
                    'harga' => $productData['harga'],
                    'stok' => $productData['stok'],
                    'kategori_id' => $productData['kategori_id'],
                    'kategori_nama' => $productData['kategori_nama'],
                    'sub_kategori_id' => $productData['sub_kategori_id'],
                    'deskripsi' => $productData['deskripsi'],
                    'warna' => $productData['warna'],
                    'ukuran' => $productData['ukuran'],
                    'berat' => $productData['berat'],
                    'image' => null,
                    'terjual' => 0,
                ]
            );
        }
    }
}