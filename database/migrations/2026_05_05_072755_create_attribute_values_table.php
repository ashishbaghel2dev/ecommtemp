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
        Schema::create('attribute_values', function (Blueprint $table) {
  
        $table->id();
        $table->foreignId('attribute_id')
          ->constrained()
          ->cascadeOnDelete();
        $table->string('value');
        $table->unique(['attribute_id', 'value']);
        $table->string('slug')->nullable();
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        
        $table->string('color_code')->nullable();
        $table->string('image')->nullable();

        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
