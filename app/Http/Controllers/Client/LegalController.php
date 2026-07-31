<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AboutPart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

class LegalController extends Controller
{
    public function about()
    {
        return view('client.pages.about', [
            'about' => AboutPart::query()
                ->where('status', true)
                ->latest()
                ->first(),
            'productCount' => Product::query()->active()->count(),
            'categoryCount' => Category::query()->active()->count(),
            'reviewCount' => Review::query()->where('status', 'approved')->count(),
            'featuredProducts' => Product::query()
                ->active()
                ->whereNotNull('slug')
                ->with([
                    'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('sort_order'),
                ])
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }

    public function paymentPolicy()
    {
        return view('client.pages.legal.payment-policy');
    }

    public function privacyPolicy()
    {
        return view('client.pages.legal.privacy-policy');
    }

    public function returnRefundPolicy()
    {
        return view('client.pages.legal.return-refund-policy');
    }

    public function shippingPolicy()
    {
        return view('client.pages.legal.shipping-policy');
    }

    public function termsConditions()
    {
        return view('client.pages.legal.terms-conditions');
    }
}
