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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sertifikasi_jenis')) {
                $table->string('sertifikasi_jenis')->nullable()->after('omset_tahun');
            }

            if (!Schema::hasColumn('users', 'sertifikasi_file')) {
                $table->string('sertifikasi_file')->nullable()->after('sertifikasi_jenis');
            }

            if (!Schema::hasColumn('users', 'sosmed_platform')) {
                $table->string('sosmed_platform')->nullable()->after('sosmed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sertifikasi_file')) {
                $table->dropColumn('sertifikasi_file');
            }

            if (Schema::hasColumn('users', 'sertifikasi_jenis')) {
                $table->dropColumn('sertifikasi_jenis');
            }

            if (Schema::hasColumn('users', 'sosmed_platform')) {
                $table->dropColumn('sosmed_platform');
            }
        });
    }
};
