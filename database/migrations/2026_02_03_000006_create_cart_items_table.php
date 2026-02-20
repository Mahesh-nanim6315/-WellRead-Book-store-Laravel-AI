<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('book_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // IMPORTANT
            $table->enum('format', ['ebook', 'audio', 'paperback']);

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);

            $table->timestamps();

            // Prevent duplicate same-format book in cart
            $table->unique(['cart_id', 'book_id', 'format']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
