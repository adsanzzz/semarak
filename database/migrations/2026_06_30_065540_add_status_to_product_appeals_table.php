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
        Schema::table('product_appeals', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('admin_reply'); // pending, approved, rejected, completed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_appeals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
