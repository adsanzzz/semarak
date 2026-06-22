<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('orders', 'review_text')) {
                $table->text('review_text')->nullable()->after('rating');
            }

            if (!Schema::hasColumn('orders', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('review_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('orders', 'rating')) {
                $dropColumns[] = 'rating';
            }

            if (Schema::hasColumn('orders', 'review_text')) {
                $dropColumns[] = 'review_text';
            }

            if (Schema::hasColumn('orders', 'reviewed_at')) {
                $dropColumns[] = 'reviewed_at';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};