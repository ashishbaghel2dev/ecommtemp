<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cart_items', 'attribute_signature')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'product_id', 'product_variant_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('attribute_signature', 64)->default('')->after('product_variant_id');
            $table->unique(
                ['cart_id', 'product_id', 'product_variant_id', 'attribute_signature'],
                'cart_items_cart_product_variant_attrs_unique'
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('cart_items', 'attribute_signature')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_product_variant_attrs_unique');
            $table->dropColumn('attribute_signature');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id', 'product_variant_id']);
        });
    }
};
