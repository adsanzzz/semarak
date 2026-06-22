<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure historical orders point to the correct seller (product owner).
        DB::statement('
            UPDATE orders o
            INNER JOIN products p ON p.id = o.product_id
            SET o.user_id = p.user_id
            WHERE o.user_id IS NULL OR o.user_id <> p.user_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No safe rollback for historical data backfill.
    }
};
