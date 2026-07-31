<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\StoreMailService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(CartService $cartService, CouponService $couponService)
    {
        $cart = $cartService->getCart();
        $cart->load(['items.product', 'items.variant']);
        $couponService->recalculateCart($cart, Auth::user());
        $cart->refresh()->load(['items.product', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        return view('client.pages.checkout.index', [
            'cart' => $cart,
            'user' => Auth::user(),
            'addresses' => Auth::check()
                ? Auth::user()->addresses()->latest('is_default')->latest()->get()
                : collect(),
            'availableCoupons' => $couponService->availableCoupons($cart, Auth::user()),
            'codAvailable' => true,
        ]);
    }

    public function checkoutLogin(
        Request $request,
        CartService $cartService,
        WishlistService $wishlistService
    ) {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $guestSessionId = session()->getId();
        $login = trim($validated['login']);
        $user = null;

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $login)->first();
        } else {
            $phone = preg_replace('/\D+/', '', $login);

            if (strlen($phone) !== 10) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile number must be exactly 10 digits.',
                ], 422);
            }

            $user = User::whereIn('phone', array_unique([$phone, '91'.$phone, '+91'.$phone, '0'.$phone]))->first();
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login details.',
            ], 422);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $cartService->claimGuestCartForUser((int) Auth::id(), $guestSessionId);
        $wishlistService->mergeGuestWishlist();
        $cartService->mergeGuestCart();

        Auth::user()->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Logged in. Continue with delivery address.',
            'reload' => true,
        ]);
    }

    public function checkoutRegister(
        Request $request,
        CartService $cartService,
        WishlistService $wishlistService,
        StoreMailService $storeMailService
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'digits:10'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (User::whereIn('phone', $this->phoneLookupValues($validated['phone']))->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'This mobile number is already linked to another account. Please login with password.',
                'errors' => [
                    'phone' => ['This mobile number is already linked to another account.'],
                ],
            ], 422);
        }

        $guestSessionId = session()->getId();

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $this->formatMobile($validated['phone']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => true,
            'email_verified_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $cartService->claimGuestCartForUser((int) $user->id, $guestSessionId);
        $wishlistService->mergeGuestWishlist();
        $cartService->mergeGuestCart();
        $storeMailService->userLoggedIn($user, 'new checkout registration', $request);

        return response()->json([
            'status' => true,
            'message' => 'Account created. Continue with delivery address.',
            'reload' => true,
        ]);
    }

    public function storeAddress(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $validated = $this->validateAddress($request);
        $validated['user_id'] = Auth::id();

        if (! empty($validated['is_default'])) {
            UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $address = UserAddress::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Address saved.',
            'address' => $address,
        ]);
    }

    public function updateAddress(Request $request, UserAddress $address)
    {
        abort_unless(Auth::check() && $address->user_id === Auth::id(), 403);

        $validated = $this->validateAddress($request);

        if (! empty($validated['is_default'])) {
            UserAddress::where('user_id', Auth::id())->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Address updated.',
            'address' => $address->fresh(),
        ]);
    }

    public function deleteAddress(UserAddress $address)
    {
        abort_unless(Auth::check() && $address->user_id === Auth::id(), 403);

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted.',
        ]);
    }

    public function setDefaultAddress(UserAddress $address)
    {
        abort_unless(Auth::check() && $address->user_id === Auth::id(), 403);

        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Default address updated.',
        ]);
    }

    public function review(Request $request, CartService $cartService, CouponService $couponService)
    {
        abort_unless(Auth::check(), 403);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'address_id' => ['required', 'integer', 'exists:user_addresses,id'],
            ]);

            session(['checkout_address_id' => $validated['address_id']]);
        }

        $addressId = session('checkout_address_id');
        if (! $addressId) {
            return redirect()->route('checkout.index')->with('error', 'Please select a delivery address.');
        }

        $address = UserAddress::where('user_id', Auth::id())->findOrFail($addressId);

        $cart = $cartService->getCart();
        $cart->load(['items.product', 'items.variant']);
        $couponService->recalculateCart($cart, Auth::user());
        $cart->refresh()->load(['items.product', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('client.pages.checkout.review', [
            'cart' => $cart,
            'address' => $address,
            'user' => Auth::user(),
            'codAvailable' => $this->isCodAvailable($address->postal_code),
            'estimatedDeliveryDate' => $this->estimatedDeliveryDate($address->postal_code),
            'availableCoupons' => $couponService->availableCoupons($cart, Auth::user()),
        ]);
    }

    public function store(Request $request, CartService $cartService, CouponService $couponService, StoreMailService $storeMailService)
    {
        $validated = $request->validate([
            'payment_method' => ['nullable', 'in:cod'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['payment_method'] = 'cod';

        abort_unless(Auth::check(), 403);

        $address = UserAddress::where('user_id', Auth::id())->findOrFail(session('checkout_address_id'));

        if ($validated['payment_method'] === 'cod' && ! $this->isCodAvailable($address->postal_code)) {
            return redirect()->route('checkout.index')->with('error', 'Cash on Delivery is unavailable for this PIN code.');
        }

        $cart = $cartService->getCart();
        $cart->load(['items.product', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartService->recalculate($cart);
        $couponService->recalculateCart($cart, Auth::user());
        $cart->refresh()->load(['items.product', 'items.variant']);

        $stockError = $this->validateStock($cart);
        if ($stockError) {
            return redirect()->route('checkout.index')->with('error', $stockError);
        }

        $order = DB::transaction(function () use ($cart, $validated, $address, $couponService) {
            $couponTotals = $couponService->recalculateCart($cart, Auth::user());
            $order = $this->createOrderFromCart($cart, $validated, $address, $couponTotals);
            $this->reserveInventory($cart);
            $this->recordCouponUsage($cart, $order);
            $cart->update(['status' => 'converted']);

            return $order;
        });

        $storeMailService->orderSuccess($order);

        return redirect()->route('checkout.success', $order);
    }

    public function success(Request $request, Order $order)
    {
        abort_unless($order->user_id && Auth::id() === $order->user_id, 403);

        $order->load('items');

        return view('client.pages.checkout.success', compact('order'));
    }

    public function invoice(Order $order)
    {
        abort_if($order->user_id && $order->user_id !== Auth::id(), 403);

        $order->load('items');

        return view('client.pages.checkout.invoice', compact('order'));
    }

    private function makeOrderNumber(): string
    {
        do {
            $number = 'GSW-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:120'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function createOrderFromCart($cart, array $validated, UserAddress $address, array $couponTotals): Order
    {
        $user = Auth::user();
        $coupon = $cart->coupon_code ? Coupon::where('code', $cart->coupon_code)->first() : null;

        $order = Order::create([
            'user_id' => Auth::id(),
            'cart_id' => $cart->id,
            'coupon_id' => $coupon?->id,
            'coupon_code' => $coupon?->code,
            'coupon_discount' => $couponTotals['coupon_discount'] ?? (float) $cart->discount_total,
            'order_number' => $this->makeOrderNumber(),
            'customer_name' => $address->name,
            'customer_email' => $address->email ?: $user->email,
            'customer_phone' => $address->phone,
            'shipping_address_line_1' => $address->address_line_1,
            'shipping_address_line_2' => $address->address_line_2,
            'shipping_city' => $address->city,
            'shipping_state' => $address->state,
            'shipping_postal_code' => $address->postal_code,
            'shipping_country' => $address->country,
            'shipping_address_snapshot' => $address->toArray(),
            'estimated_delivery_date' => $this->estimatedDeliveryDate($address->postal_code),
            'payment_method' => $validated['payment_method'],
            'payment_meta' => [
                'provider' => 'COD',
                'inventory_reserved' => true,
                'inventory_released' => false,
            ],
            'notes' => $validated['notes'] ?? null,
            'payment_status' => 'pending',
            'status' => 'pending',
            'subtotal' => $cart->subtotal,
            'discount_total' => $cart->discount_total,
            'tax_total' => $cart->tax_total,
            'shipping_total' => $cart->shipping_total,
            'total_savings' => $couponTotals['total_savings'] ?? (float) $cart->discount_total,
            'grand_total' => $cart->grand_total,
        ]);

        foreach ($cart->items as $item) {
            $meta = $item->meta ?? [];
            $meta['pricing'] = [
                'original_price' => (float) ($item->original_price ?: $item->price),
                'unit_price' => (float) $item->price,
                'product_discount' => (float) $item->discount_amount,
                'line_original_total' => (float) ($item->original_price ?: $item->price) * (int) $item->quantity,
                'line_subtotal' => (float) $item->subtotal,
            ];

            $order->items()->create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'product_image' => $item->product_image,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'total' => $item->total,
                'meta' => $meta,
            ]);
        }

        return $order;
    }

    private function validateStock($cart): ?string
    {
        foreach ($cart->items as $item) {
            $stockable = $item->variant ?: $item->product;
            if (! $stockable || ! $stockable->in_stock) {
                return "{$item->product_name} is unavailable.";
            }

            if ($stockable->manage_stock && (int) $stockable->stock < (int) $item->quantity) {
                return "Only {$stockable->stock} unit(s) of {$item->product_name} are available.";
            }
        }

        return null;
    }

    private function reserveInventory($cart): void
    {
        foreach ($cart->items as $item) {
            if ($item->product_variant_id) {
                $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                if ($variant && $variant->manage_stock) {
                    $variant->decrement('stock', $item->quantity);
                    $variant->update(['in_stock' => $variant->fresh()->stock > 0]);
                }
                continue;
            }

            $product = Product::lockForUpdate()->find($item->product_id);
            if ($product && $product->manage_stock) {
                $product->decrement('stock', $item->quantity);
                $product->update(['in_stock' => $product->fresh()->stock > 0]);
            }
        }
    }

    private function releaseReservedInventory(Order $order): void
    {
        $paymentMeta = $order->payment_meta ?? [];

        if (
            (empty($paymentMeta['inventory_reserved']) || ! empty($paymentMeta['inventory_released']))
            && empty($order->coupon_id)
        ) {
            return;
        }

        DB::transaction(function () use ($order, $paymentMeta) {
            $order->loadMissing('items');

            if (! empty($paymentMeta['inventory_reserved']) && empty($paymentMeta['inventory_released'])) {
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);

                        if ($variant && $variant->manage_stock) {
                            $variant->increment('stock', $item->quantity);
                            $variant->update(['in_stock' => true]);
                        }

                        continue;
                    }

                    $product = Product::lockForUpdate()->find($item->product_id);

                    if ($product && $product->manage_stock) {
                        $product->increment('stock', $item->quantity);
                        $product->update(['in_stock' => true]);
                    }
                }

                $paymentMeta['inventory_released'] = true;
                $paymentMeta['inventory_released_at'] = now()->toIso8601String();
            }

            if ($order->coupon_id && empty($paymentMeta['coupon_released'])) {
                $deleted = CouponUsage::where('order_id', $order->id)->delete();

                if ($deleted > 0) {
                    Coupon::whereKey($order->coupon_id)
                        ->where('used_count', '>', 0)
                        ->decrement('used_count');
                }

                $paymentMeta['coupon_released'] = true;
                $paymentMeta['coupon_released_at'] = now()->toIso8601String();
            }

            $order->update(['payment_meta' => $paymentMeta]);
        });
    }

    private function recordCouponUsage($cart, Order $order): void
    {
        if (! $cart->coupon_code || $order->coupon_discount <= 0) {
            return;
        }

        $coupon = Coupon::where('code', $cart->coupon_code)->lockForUpdate()->first();
        if (! $coupon) {
            return;
        }

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'coupon_code' => $coupon->code,
            'discount_amount' => $order->coupon_discount,
        ]);

        $coupon->increment('used_count');
    }

    private function isCodAvailable(string $postalCode): bool
    {
        return true;
    }

    private function formatMobile(string $phone): string
    {
        return substr(preg_replace('/\D+/', '', $phone), -10);
    }

    private function phoneLookupValues(string $phone): array
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $mobile = $this->formatMobile($digits);
        $national = Str::startsWith($mobile, '91') ? substr($mobile, 2) : $mobile;

        return array_values(array_unique(array_filter([
            $phone,
            $digits,
            $mobile,
            '+'.$mobile,
            $national,
            '0'.$national,
        ])));
    }

    private function estimatedDeliveryDate(string $postalCode)
    {
        $days = Str::startsWith(trim($postalCode), ['1', '2', '3']) ? 3 : 5;

        return now()->addWeekdays($days)->toDateString();
    }
}
