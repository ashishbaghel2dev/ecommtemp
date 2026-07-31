<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPart;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomeCarouselImage;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\SocialMediaLink;
use App\Models\Tag;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $start = now()->subDays(6)->startOfDay();

        $ordersByDay = Order::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders, COALESCE(SUM(grand_total), 0) as revenue')
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $dailySales = collect(range(0, 6))->map(function (int $offset) use ($start, $ordersByDay) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();
            $row = $ordersByDay->get($key);

            return [
                'label' => $date->format('d M'),
                'orders' => (int) ($row->orders ?? 0),
                'revenue' => (float) ($row->revenue ?? 0),
            ];
        });

        $maxRevenue = max(1, (float) $dailySales->max('revenue'));
        $linePoints = $dailySales
            ->values()
            ->map(function (array $row, int $index) use ($dailySales, $maxRevenue) {
                $x = $dailySales->count() === 1 ? 0 : ($index / ($dailySales->count() - 1)) * 100;
                $y = 100 - (($row['revenue'] / $maxRevenue) * 82) - 9;

                return round($x, 2).','.round($y, 2);
            })
            ->implode(' ');

        $paymentBreakdown = Order::query()
            ->select('payment_status', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        $topProducts = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as quantity'), DB::raw('SUM(total) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $stats = [
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'paid_revenue' => (float) Order::where('payment_status', 'paid')->sum('grand_total'),
            'today_revenue' => (float) Order::whereDate('created_at', $today)->sum('grand_total'),
            'customers' => User::where('role', 'user')->count(),
            'products' => Product::count(),
            'pending_orders' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'failed_payments' => Order::where('payment_status', 'failed')->count(),
        ];

        $contentStats = [
            'gallery' => [
                'total' => Gallery::count(),
                'active' => Gallery::where('status', true)->count(),
                'route' => route('gallery.index'),
            ],
            'blogs' => [
                'total' => Blog::count(),
                'active' => Blog::where('status', true)->count(),
                'route' => route('blogs.index'),
            ],
            'faqs' => [
                'total' => Faq::count(),
                'active' => Faq::count(),
                'route' => route('faqs.index'),
            ],
            'banners' => [
                'total' => Banner::count(),
                'active' => Banner::where('is_active', true)->count(),
                'route' => route('banners.index'),
            ],
            'carousel' => [
                'total' => HomeCarouselImage::count(),
                'active' => HomeCarouselImage::count(),
                'route' => route('home-carousel-images.index'),
            ],
            'coupons' => [
                'total' => Coupon::count(),
                'active' => Coupon::where('is_active', true)->count(),
                'route' => route('coupons.index'),
            ],
            'inquiries' => [
                'total' => Inquiry::count(),
                'active' => Inquiry::where('status', Inquiry::STATUS_PENDING)->count(),
                'route' => route('inquiries.index'),
            ],
            'reviews' => [
                'total' => Review::count(),
                'active' => Review::where('status', 'pending')->count(),
                'route' => route('admin.reviews.index'),
            ],
            'tags' => [
                'total' => Tag::count(),
                'active' => Tag::count(),
                'route' => route('tags.index'),
            ],
            'categories' => [
                'total' => Category::count(),
                'active' => Category::where('is_active', true)->count(),
                'route' => route('categories.index'),
            ],
            'social' => [
                'total' => SocialMediaLink::count(),
                'active' => SocialMediaLink::where('is_active', true)->count(),
                'route' => route('social-links.index'),
            ],
            'about' => [
                'total' => AboutPart::count(),
                'active' => AboutPart::where('status', true)->count(),
                'route' => route('about-parts.index'),
            ],
        ];

        $contentTotals = [
            'cms_items' => collect($contentStats)->sum('total'),
            'active_items' => collect($contentStats)->sum('active'),
            'pending_inquiries' => $contentStats['inquiries']['active'],
            'pending_reviews' => $contentStats['reviews']['active'],
            'wishlist_items' => Wishlist::count(),
            'trashed_items' => Gallery::onlyTrashed()->count()
                + Blog::onlyTrashed()->count()
                + Faq::onlyTrashed()->count()
                + Banner::onlyTrashed()->count()
                + Inquiry::onlyTrashed()->count()
                + Review::onlyTrashed()->count()
                + Tag::onlyTrashed()->count()
                + Category::onlyTrashed()->count()
                + SocialMediaLink::onlyTrashed()->count()
                + AboutPart::onlyTrashed()->count(),
        ];

        $latestContent = collect([
            ['label' => 'Latest Blog', 'title' => Blog::latest()->value('title') ?: 'No blogs yet', 'route' => route('blogs.index')],
            ['label' => 'Latest Gallery', 'title' => Gallery::latest()->value('title') ?: 'No gallery photos yet', 'route' => route('gallery.index')],
            ['label' => 'Latest FAQ', 'title' => Faq::latest()->value('question') ?: 'No FAQs yet', 'route' => route('faqs.index')],
            ['label' => 'Latest Inquiry', 'title' => Inquiry::latest()->value('name') ?: 'No inquiries yet', 'route' => route('inquiries.index')],
            ['label' => 'Latest Review', 'title' => Review::latest()->value('title') ?: 'No reviews yet', 'route' => route('admin.reviews.index')],
        ]);

        $recentOrders = Order::query()
            ->with('user')
            ->withCount('items')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.pages.home.home', [
            'stats' => $stats,
            'dailySales' => $dailySales,
            'linePoints' => $linePoints,
            'maxRevenue' => $maxRevenue,
            'paymentBreakdown' => $paymentBreakdown,
            'topProducts' => $topProducts,
            'contentStats' => $contentStats,
            'contentTotals' => $contentTotals,
            'latestContent' => $latestContent,
            'recentOrders' => $recentOrders,
            'refreshedAt' => now(),
        ]);
    }
}
