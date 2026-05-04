<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');

            $table->string('sku')->unique();

            $table->string('brand')->nullable();

            $table->string('unit');

            $table->decimal('buying_price', 12, 2)->default(0);

            $table->decimal('selling_price', 12, 2)->default(0);

            $table->integer('stock_quantity')->default(0);

            $table->integer('minimum_stock')->default(5);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
