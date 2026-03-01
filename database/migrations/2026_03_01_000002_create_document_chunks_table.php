<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->unsignedInteger('page_no')->nullable();
            $table->longText('chunk_text');
            $table->longText('embedding')->nullable();
            $table->string('embedding_provider')->nullable();
            $table->timestamps();

            $table->index(['document_id', 'chunk_index']);
            $table->index('embedding_provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};

