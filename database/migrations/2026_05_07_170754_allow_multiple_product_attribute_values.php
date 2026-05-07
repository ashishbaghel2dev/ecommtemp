<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->index('product_id', 'product_attribute_values_product_id_index');
            $table->index('attribute_id', 'product_attribute_values_attribute_id_index');
            $table->dropUnique(['product_id', 'attribute_id']);
            $table->unique(
                ['product_id', 'attribute_id', 'attribute_value_id'],
                'product_attribute_selected_value_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropUnique('product_attribute_selected_value_unique');
            $table->dropIndex('product_attribute_values_product_id_index');
            $table->dropIndex('product_attribute_values_attribute_id_index');
            $table->unique(['product_id', 'attribute_id']);
        });
    }
};
