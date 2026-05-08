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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

   $table->foreignId('product_id')
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->tinyInteger('rating')->unsigned()->comment('1 to 5 stars');
            $table->text('comment')->nullable();
            $table->text('admin_reply')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->unsignedInteger('unhelpful_votes')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
             $table->softDeletes();
 $table->unique(['product_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
