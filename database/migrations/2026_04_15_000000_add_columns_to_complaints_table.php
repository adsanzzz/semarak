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
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('complaints', 'order_id')) {
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('complaints', 'sender_name')) {
                $table->string('sender_name')->nullable();
            }

            if (!Schema::hasColumn('complaints', 'issue_description')) {
                $table->text('issue_description')->nullable();
            }

            if (!Schema::hasColumn('complaints', 'input')) {
                $table->text('input')->nullable();
            }

            if (!Schema::hasColumn('complaints', 'subject')) {
                $table->string('subject')->nullable();
            }

            if (!Schema::hasColumn('complaints', 'message')) {
                $table->text('message')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['order_id']);
            $table->dropColumn([
                'user_id',
                'order_id',
                'sender_name',
                'issue_description',
                'input',
                'subject',
                'message',
            ]);
        });
    }
};