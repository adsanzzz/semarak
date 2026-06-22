<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('review_status', ['pending', 'diterima', 'ditolak'])
                ->default('pending')
                ->after('status');
            $table->text('rejection_reason')->nullable()->after('review_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['review_status', 'rejection_reason']);
        });
    }
};