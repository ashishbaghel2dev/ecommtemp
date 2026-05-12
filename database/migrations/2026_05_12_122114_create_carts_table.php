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
  Schema::create('carts', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->onDelete('cascade');
    $table->string('session_id')
        ->nullable()
        ->index();
    $table->enum('status', [

        'active',
        'converted',
        'abandoned'

    ])->default('active');

    $table->enum('type', [

        'normal',
        'buy_now',
        'saved'

    ])->default('normal');
    $table->unsignedInteger('total_items')
        ->default(0);
    $table->unsignedInteger('total_quantity')
        ->default(0);

    $table->decimal('subtotal', 12, 2)
        ->default(0);

    $table->decimal('discount_total', 12, 2)
        ->default(0);


    $table->decimal('tax_total', 12, 2)
        ->default(0);


    $table->decimal('shipping_total', 12, 2)
        ->default(0);


    $table->decimal('grand_total', 12, 2)
        ->default(0);


    $table->string('coupon_code')
        ->nullable();

    $table->string('currency', 10)
        ->default('INR');


    $table->timestamp('last_activity_at')
        ->nullable();

    $table->string('device_type')
        ->nullable();


    $table->ipAddress('ip_address')
        ->nullable();

    $table->text('user_agent')
        ->nullable();

    $table->timestamps();

    $table->index([
        'user_id',
        'status'
    ]);

    $table->index([
        'session_id',
        'status'
    ]);

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
