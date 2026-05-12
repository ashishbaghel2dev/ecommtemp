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
        Schema::create('cart_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('cart_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->string('attribute_signature', 64)->default('');

            $table->string('product_name');
            $table->string('product_sku')
                ->nullable();
            $table->string('product_image')
                ->nullable();
            $table->unsignedInteger('quantity')
                ->default(1);
            $table->decimal('price', 12, 2);
            $table->decimal('original_price', 12, 2)
                ->nullable();
            $table->decimal('discount_amount', 12, 2)
                ->default(0);
            $table->decimal('tax_amount', 12, 2)
                ->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->boolean('is_selected')
                ->default(true);
            $table->boolean('is_available')
                ->default(true);
            $table->unsignedInteger('stock_at_time')
                ->nullable();
            $table->json('meta')
                ->nullable();

            $table->timestamps();

            $table->unique(
                ['cart_id', 'product_id', 'product_variant_id', 'attribute_signature'],
                'cart_items_cart_product_variant_attrs_unique'
            );

            $table->index([
                'cart_id',
                'product_id',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
