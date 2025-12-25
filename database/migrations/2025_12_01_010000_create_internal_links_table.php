<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internal_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('target_url');
            $table->string('anchor_text')->nullable();
            $table->boolean('nofollow')->default(false);
            $table->timestamps();

            $table->index(['source_page_id', 'target_url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_links');
    }
};
