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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('status');
            }

            if (!Schema::hasColumn('orders', 'delivery_location')) {
                $table->text('delivery_location')->nullable()->after('shipping_method');
            }

            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('delivery_location');
            }

            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('orders', 'shipping_method')) {
                $dropColumns[] = 'shipping_method';
            }

            if (Schema::hasColumn('orders', 'delivery_location')) {
                $dropColumns[] = 'delivery_location';
            }

            if (Schema::hasColumn('orders', 'payment_method')) {
                $dropColumns[] = 'payment_method';
            }

            if (Schema::hasColumn('orders', 'payment_status')) {
                $dropColumns[] = 'payment_status';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
