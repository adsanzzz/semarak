<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->timestamps();
        });

        // Seed default dangerous/suspicious keywords
        $defaults = [
            'senjata', 'narkoba', 'sabu', 'ganja', 'bom', 'hack', 'porn', 
            'racun', 'senjata api', 'obat terlarang', 'sianida', 'hacking',
            'mutilasi', 'senapan', 'peluru', 'ekstasi', 'heroin', 'kokain'
        ];

        foreach ($defaults as $keyword) {
            DB::table('moderation_keywords')->insert([
                'keyword' => $keyword,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_keywords');
    }
};
