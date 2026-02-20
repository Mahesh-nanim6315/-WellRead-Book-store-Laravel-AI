<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            /* Basic Info */
            $table->string('name');
            $table->text('description');
            $table->string('language')->nullable();
            $table->string('image');

            /* Relations */
            $table->foreignId('author_id')
                  ->constrained('authors')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->foreignId('genre_id')
                  ->constrained('genres')
                  ->cascadeOnDelete();

            /* Ebook */
            $table->boolean('has_ebook')->default(false);
            $table->decimal('ebook_price', 8, 2)->nullable();
            $table->string('ebook_pdf')->nullable();
            $table->integer('ebook_pages')->nullable();

            /* Audio */
            $table->boolean('has_audio')->default(false);
            $table->decimal('audio_price', 8, 2)->nullable();
            $table->string('audio_file')->nullable();
            $table->integer('audio_minutes')->nullable();

            /* Paperback */
            $table->boolean('has_paperback')->default(false);
            $table->decimal('paperback_price', 8, 2)->nullable();
            $table->integer('paperback_pages')->nullable();
            $table->integer('stock')->default(0);

            /* Fallback price (used in sorting / legacy) */
            $table->decimal('price', 8, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
