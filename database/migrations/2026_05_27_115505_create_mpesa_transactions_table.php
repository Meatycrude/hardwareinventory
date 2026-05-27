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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
    $table->id();
    $table->string('checkout_request_id')->unique();
    $table->string('merchant_request_id')->nullable();
    $table->string('phone');
    $table->decimal('amount', 12, 2);
    $table->string('status')->default('pending');
    $table->string('mpesa_receipt_number')->nullable();
    $table->json('cart_items')->nullable();
    $table->json('callback_payload')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
