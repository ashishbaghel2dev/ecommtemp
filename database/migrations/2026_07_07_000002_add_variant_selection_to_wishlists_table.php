<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id']);

            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();
            $table->string('attribute_signature', 64)
                ->default('')
                ->after('product_variant_id');
            $table->json('meta')
                ->nullable()
                ->after('attribute_signature');

            $table->unique(
                ['user_id', 'product_id', 'product_variant_id', 'attribute_signature'],
                'wishlists_user_product_variant_attrs_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropUnique('wishlists_user_product_variant_attrs_unique');
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_variant_id', 'attribute_signature', 'meta']);
            $table->unique(['user_id', 'product_id']);
        });
    }
};
