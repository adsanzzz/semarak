<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();         // icon / logo (bisa path file)
            $table->string('nama_kategori');            // nama kategori
            $table->text('deskripsi')->nullable();      // deskripsi kategori
            $table->unsignedInteger('jumlah_toko')->default(0); // jumlah toko yg memakai kategori
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
