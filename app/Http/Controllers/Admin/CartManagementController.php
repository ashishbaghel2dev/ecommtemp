<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartManagementController 
{
    /*
    |--------------------------------------------------------------------------
    | LIST ALL CARTS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $carts = Cart::with('items.product', 'user')
            ->latest()
            ->paginate(20);

        return view('admin.carts.index', compact('carts'));
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW SINGLE CART DETAILS
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $cart = Cart::with('items.product', 'user')
            ->findOrFail($id);

        return view('admin.carts.show', compact('cart'));
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE CART (ADMIN CONTROL)
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);

        $cart->delete();

        return back()->with('success', 'Cart deleted successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE SINGLE ITEM FROM CART
    |--------------------------------------------------------------------------
    */
    public function removeItem($itemId)
    {
        $item = CartItem::findOrFail($itemId);

        $cart = $item->cart;

        $item->delete();

        // optional: recalc totals
        $cart->refresh();

        return back()->with('success', 'Item removed');
    }
}