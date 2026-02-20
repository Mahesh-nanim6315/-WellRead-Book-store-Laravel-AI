<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('user_library', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('book_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // ebook / audio / paperback
            $table->enum('format', ['ebook', 'audio', 'paperback']);

            // null for paperback (owned)
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // 🔐 prevent duplicate same-format entries
            $table->unique(['user_id', 'book_id', 'format']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_library');
    }
};
