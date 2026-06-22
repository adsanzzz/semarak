<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (! Schema::hasColumn('complaints', 'complaint_type')) {
                $table->string('complaint_type')->nullable()->after('order_id');
            }

            if (! Schema::hasColumn('complaints', 'forwarded_from_id')) {
                $table->foreignId('forwarded_from_id')->nullable()->constrained('complaints')->nullOnDelete()->after('complaint_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'forwarded_from_id')) {
                $table->dropConstrainedForeignId('forwarded_from_id');
            }

            if (Schema::hasColumn('complaints', 'complaint_type')) {
                $table->dropColumn('complaint_type');
            }
        });
    }
};