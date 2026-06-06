<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(CartService $cartService)
    {
        $cart = $cartService->getCart();
        $cart->load(['items.product', 'items.variant']);

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
        ]);
    }

    public function store(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address_line_1' => ['required', 'string', 'max:255'],
            'shipping_address_line_2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_state' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:120'],
            'address_mode' => ['nullable', 'in:saved,new'],
            'saved_address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'save_address' => ['nullable', 'boolean'],
            'address_label' => ['nullable', 'string', 'max:80'],
            'make_default_address' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'in:cod'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (Auth::check() && ($validated['address_mode'] ?? null) === 'saved' && ! empty($validated['saved_address_id'])) {
            $address = UserAddress::query()
                ->where('user_id', Auth::id())
                ->findOrFail($validated['saved_address_id']);

            $validated['customer_name'] = $address->name;
            $validated['customer_phone'] = $address->phone;
            $validated['shipping_address_line_1'] = $address->address_line_1;
            $validated['shipping_address_line_2'] = $address->address_line_2;
            $validated['shipping_city'] = $address->city;
            $validated['shipping_state'] = $address->state;
            $validated['shipping_postal_code'] = $address->postal_code;
            $validated['shipping_country'] = $address->country;
        }

        $cart = $cartService->getCart();
        $cart->load(['items']);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartService->recalculate($cart);
        $cart->refresh()->load('items');

        $order = DB::transaction(function () use ($cart, $validated) {
            if (Auth::check() && ($validated['address_mode'] ?? 'new') === 'new' && ! empty($validated['save_address'])) {
                if (! empty($validated['make_default_address'])) {
                    UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
                }

                UserAddress::create([
                    'user_id' => Auth::id(),
                    'label' => $validated['address_label'] ?: 'Home',
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'address_line_1' => $validated['shipping_address_line_1'],
                    'address_line_2' => $validated['shipping_address_line_2'] ?? null,
                    'city' => $validated['shipping_city'],
                    'state' => $validated['shipping_state'],
                    'postal_code' => $validated['shipping_postal_code'],
                    'country' => $validated['shipping_country'],
                    'is_default' => ! empty($validated['make_default_address']),
                ]);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'cart_id' => $cart->id,
                'order_number' => $this->makeOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address_line_1' => $validated['shipping_address_line_1'],
                'shipping_address_line_2' => $validated['shipping_address_line_2'] ?? null,
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'shipping_country' => $validated['shipping_country'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'payment_status' => 'pending',
                'status' => 'pending',
                'subtotal' => $cart->subtotal,
                'discount_total' => $cart->discount_total,
                'tax_total' => $cart->tax_total,
                'shipping_total' => $cart->shipping_total,
                'grand_total' => $cart->grand_total,
            ]);

            foreach ($cart->items as $item) {
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
                    'meta' => $item->meta,
                ]);
            }

            $cart->update(['status' => 'converted']);

            return $order;
        });

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        abort_if($order->user_id && $order->user_id !== Auth::id(), 403);

        $order->load('items');

        return view('client.pages.checkout.success', compact('order'));
    }

    private function makeOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
