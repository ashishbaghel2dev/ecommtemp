<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistManagementController 
{
    public function index()
    {
        // Sabhi products fetch karein aur unke total wishlist count ko saath mein layein
        $products = Product::withCount('wishlists')
            ->orderBy('wishlists_count', 'desc') // Sabse zyada popular product upar dikhega
            ->paginate(20); // Data zyada hai toh pagination zaroori hai

        return view('admin.pages.wishlists.index', compact('products'));
    }

public function showUsers($product_id)
{
    $product = Product::findOrFail($product_id);

    $users = Wishlist::where('product_id', $product_id)
        ->with('user')
        ->latest()
        ->get();

    return view(
        'admin.pages.wishlists.users',
        compact('product', 'users')
    );
}


}
