<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('total_amount', 10, 2);
            $table->foreignId('address_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

            $table->string('payment_method')->nullable(); // stripe / razorpay
            $table->string('payment_id')->nullable();     // stripe_payment_id
            $table->string('payment_status')->default('pending'); // pending|paid|failed

            $table->string('status')->default('pending'); // pending|completed|cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
