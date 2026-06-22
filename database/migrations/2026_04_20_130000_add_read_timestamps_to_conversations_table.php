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
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'buyer_last_read_at')) {
                $table->timestamp('buyer_last_read_at')->nullable()->after('seller_id');
            }

            if (!Schema::hasColumn('conversations', 'seller_last_read_at')) {
                $table->timestamp('seller_last_read_at')->nullable()->after('buyer_last_read_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('conversations', 'buyer_last_read_at')) {
                $dropColumns[] = 'buyer_last_read_at';
            }

            if (Schema::hasColumn('conversations', 'seller_last_read_at')) {
                $dropColumns[] = 'seller_last_read_at';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
