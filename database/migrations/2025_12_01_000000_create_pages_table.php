<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->string('h1')->nullable();
            $table->string('language', 5)->nullable()->index();
            $table->longText('body_text')->nullable();
            $table->unsignedInteger('word_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
