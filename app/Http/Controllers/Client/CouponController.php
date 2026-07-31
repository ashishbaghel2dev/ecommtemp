<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function apply(Request $request, CartService $cartService, CouponService $couponService)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:80'],
        ]);

        $cart = $cartService->getCart();
        $cart->load('items.product');

        $result = $couponService->apply($cart, $validated['code'], Auth::user());
        $status = $result['valid'] ? 200 : 422;

        return response()->json([
            'status' => $result['valid'],
            'message' => $result['message'],
            'cart' => $this->cartPayload($cartService),
            'totals' => $result['totals'] ?? null,
            'coupon' => isset($result['coupon']) ? [
                'code' => $result['coupon']->code,
                'type' => $result['coupon']->type,
            ] : null,
        ], $status);
    }

    public function remove(CartService $cartService, CouponService $couponService)
    {
        $cart = $cartService->getCart();
        $result = $couponService->remove($cart);

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'cart' => $this->cartPayload($cartService),
            'totals' => $result['totals'],
        ]);
    }

    private function cartPayload(CartService $cartService): array
    {
        $cart = $cartService->getCart()->fresh('items');
        $productDiscount = (float) $cart->items->sum('discount_amount');

        return [
            'total_items' => (int) $cart->total_items,
            'total_quantity' => (int) $cart->total_quantity,
            'product_total' => (float) $cart->subtotal + $productDiscount,
            'product_discount_total' => $productDiscount,
            'subtotal' => (float) $cart->subtotal,
            'discount_total' => (float) $cart->discount_total,
            'shipping_total' => (float) $cart->shipping_total,
            'tax_total' => (float) $cart->tax_total,
            'grand_total' => (float) $cart->grand_total,
        ];
    }
}
