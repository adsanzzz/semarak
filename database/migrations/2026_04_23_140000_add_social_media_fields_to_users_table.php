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
            if (!Schema::hasColumn('users', 'sosmed_instagram')) {
                $table->string('sosmed_instagram')->nullable()->after('sosmed');
            }

            if (!Schema::hasColumn('users', 'sosmed_tiktok')) {
                $table->string('sosmed_tiktok')->nullable()->after('sosmed_instagram');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sosmed_tiktok')) {
                $table->dropColumn('sosmed_tiktok');
            }

            if (Schema::hasColumn('users', 'sosmed_instagram')) {
                $table->dropColumn('sosmed_instagram');
            }
        });
    }
};
