<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'reported_user_id')) {
                $table->foreignId('reported_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('complaints', 'reported_product_id')) {
                $table->foreignId('reported_product_id')->nullable()->constrained('products')->nullOnDelete();
            }
            if (!Schema::hasColumn('complaints', 'bukti')) {
                $table->string('bukti')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['reported_user_id']);
            $table->dropForeign(['reported_product_id']);
            $table->dropColumn(['reported_user_id', 'reported_product_id', 'bukti']);
        });
    }
};
