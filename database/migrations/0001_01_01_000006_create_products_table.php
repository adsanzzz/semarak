<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->integer('harga');
            $table->integer('stok');

            // Relasi ke kategori
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->foreign('kategori_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');

            $table->text('deskripsi')->nullable();
            $table->string('image')->nullable();
            $table->integer('terjual')->default(0);

            // Tambahan atribut
            $table->string('warna')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('berat')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
        });

        Schema::dropIfExists('products');
    }
};
