<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Default
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->integer('role')->default(2); // 1=admin, 2=user_toko, 3=user_buyer

            // Tambahan untuk kelola user / toko
            $table->string('nama_toko')->nullable();
            $table->string('nik_penjual')->nullable();
            $table->string('nama_lengkap_penjual')->nullable();
            $table->string('phone')->nullable();
            $table->string('alamat_penjual')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kategori_usaha')->nullable();
            $table->string('modal_usaha')->nullable();
            $table->string('omset_tahun')->nullable();
            $table->boolean('sertifikasi_halal')->default(false);
            $table->boolean('sertifikasi_haki')->default(false);
            $table->string('sosmed')->nullable();
            $table->string('tautan_marketplace')->nullable();
            $table->text('informasi_kemitraan')->nullable();
            $table->text('pelatihan_usaha')->nullable();
            $table->string('bank_tujuan')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->string('norek')->nullable();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
