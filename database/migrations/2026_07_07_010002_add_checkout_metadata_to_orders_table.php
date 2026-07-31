<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('cart_id')->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->decimal('coupon_discount', 12, 2)->default(0)->after('coupon_code');
            $table->decimal('total_savings', 12, 2)->default(0)->after('shipping_total');
            $table->date('estimated_delivery_date')->nullable()->after('shipping_country');
            $table->json('shipping_address_snapshot')->nullable()->after('estimated_delivery_date');
            $table->json('payment_meta')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn([
                'coupon_code',
                'coupon_discount',
                'total_savings',
                'estimated_delivery_date',
                'shipping_address_snapshot',
                'payment_meta',
            ]);
        });
    }
};
