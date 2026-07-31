<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GuestCartClaimTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->index();
            $table->string('password');
            $table->rememberToken();
            $table->string('role')->default('user')->index();
            $table->boolean('status')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('session_id')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->string('type')->default('normal');
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_quantity')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('currency', 10)->default('INR');
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id');
            $table->foreignId('product_id');
            $table->foreignId('product_variant_id')->nullable();
            $table->string('attribute_signature', 64)->default('');
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 12, 2);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->boolean('is_selected')->default(true);
            $table->boolean('is_available')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_guest_cart_items_are_claimed_for_user_by_previous_session_id(): void
    {
        $guestSessionId = 'guest-cart-session';
        $this->app['session']->setId('new-auth-session');

        $user = User::create([
            'name' => 'Cart User',
            'email' => 'cart@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => true,
        ]);

        $product = Product::create([
            'name' => 'Green Tea',
            'slug' => 'green-tea',
            'sku' => 'GT-1',
            'price' => 250,
        ]);

        $guestCart = Cart::create([
            'session_id' => $guestSessionId,
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $guestCart->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 2,
            'price' => 250,
            'original_price' => 250,
            'subtotal' => 500,
            'total' => 500,
        ]);

        app(CartService::class)->claimGuestCartForUser($user->id, $guestSessionId);

        $userCart = Cart::where('user_id', $user->id)->where('status', 'active')->first();

        $this->assertNotNull($userCart);
        $this->assertSame(1, $userCart->items()->count());
        $this->assertSame(2, (int) $userCart->fresh()->total_quantity);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
