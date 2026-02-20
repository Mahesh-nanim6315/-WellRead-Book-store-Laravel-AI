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
       Schema::create('user_book_access', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('book_id')->constrained();
        $table->enum('type', ['ebook', 'audio', 'paperback']);
        $table->enum('access', ['rent', 'buy']);
        $table->timestamp('expires_at')->nullable(); // null = lifetime
        $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_book_access');
    }
};