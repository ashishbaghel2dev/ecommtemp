@extends('admin.layouts.app')

@section('title', $adminSettings['dashboard_label'] ?? 'Admin Dashboard')

@section('content')
@php
    $paid = (int) ($paymentBreakdown['paid'] ?? 0);
    $pending = (int) ($paymentBreakdown['pending'] ?? 0);
    $failed = (int) ($paymentBreakdown['failed'] ?? 0);
    $paymentTotal = max(1, $paid + $pending + $failed);
@endphp

<div class="main-content admin-overview-page">
    <div class="admin-overview-head">
        <div>
            <span>{{ $adminSettings['app_name'] ?? 'Go Sowa' }}</span>
            <h1>{{ $adminSettings['dashboard_label'] ?? 'Admin Dashboard' }}</h1>
            <p>Live store overview updated {{ $refreshedAt->format('d M Y, h:i A') }}</p>
        </div>
        <div class="overview-head-actions">
            <a href="{{ route('sales.index') }}"><i class="ti ti-shopping-cart"></i> Orders</a>
            <a href="{{ route('settings.index') }}"><i class="ti ti-settings"></i> Settings</a>
        </div>
    </div>

    <div class="overview-kpi-grid">
        <article>
            <i class="ti ti-receipt"></i>
            <span>Total Orders</span>
            <strong>{{ number_format($stats['total_orders']) }}</strong>
            <small>{{ number_format($stats['today_orders']) }} today</small>
        </article>
        <article>
            <i class="ti ti-currency-rupee"></i>
            <span>Paid Revenue</span>
            <strong>₹{{ number_format($stats['paid_revenue'], 2) }}</strong>
            <small>₹{{ number_format($stats['today_revenue'], 2) }} today</small>
        </article>
        <article>
            <i class="ti ti-users"></i>
            <span>Customers</span>
            <strong>{{ number_format($stats['customers']) }}</strong>
            <small>Registered accounts</small>
        </article>
        <article>
            <i class="ti ti-package"></i>
            <span>Products</span>
            <strong>{{ number_format($stats['products']) }}</strong>
            <small>{{ number_format($stats['pending_orders']) }} open orders</small>
        </article>
    </div>

    <div class="overview-kpi-grid overview-content-summary">
        <article>
            <i class="ti ti-layout-grid"></i>
            <span>CMS Items</span>
            <strong>{{ number_format($contentTotals['cms_items']) }}</strong>
            <small>{{ number_format($contentTotals['active_items']) }} active or visible</small>
        </article>
        <article>
            <i class="ti ti-message-question"></i>
            <span>Pending Inquiries</span>
            <strong>{{ number_format($contentTotals['pending_inquiries']) }}</strong>
            <small>Customer messages waiting</small>
        </article>
        <article>
            <i class="ti ti-star"></i>
            <span>Pending Reviews</span>
            <strong>{{ number_format($contentTotals['pending_reviews']) }}</strong>
            <small>Need approval or reply</small>
        </article>
        <article>
            <i class="ti ti-heart"></i>
            <span>Wishlist Items</span>
            <strong>{{ number_format($contentTotals['wishlist_items']) }}</strong>
            <small>{{ number_format($contentTotals['trashed_items']) }} items in trash</small>
        </article>
    </div>

    @php
        $contentCards = [
            ['key' => 'gallery', 'label' => 'Gallery Photos', 'meta' => 'visible photos', 'icon' => 'ti-photo'],
            ['key' => 'blogs', 'label' => 'Blogs', 'meta' => 'published posts', 'icon' => 'ti-article'],
            ['key' => 'faqs', 'label' => 'FAQ', 'meta' => 'answers live', 'icon' => 'ti-help-circle'],
            ['key' => 'banners', 'label' => 'Banners', 'meta' => 'active banners', 'icon' => 'ti-carousel-horizontal'],
            ['key' => 'carousel', 'label' => 'Home Gallery', 'meta' => 'carousel images', 'icon' => 'ti-slideshow'],
            ['key' => 'coupons', 'label' => 'Coupons', 'meta' => 'active offers', 'icon' => 'ti-ticket'],
            ['key' => 'inquiries', 'label' => 'Inquiries', 'meta' => 'pending messages', 'icon' => 'ti-mail-question'],
            ['key' => 'reviews', 'label' => 'Reviews', 'meta' => 'pending reviews', 'icon' => 'ti-message-star'],
            ['key' => 'categories', 'label' => 'Categories', 'meta' => 'active categories', 'icon' => 'ti-category'],
            ['key' => 'tags', 'label' => 'Tags', 'meta' => 'tag records', 'icon' => 'ti-tags'],
            ['key' => 'social', 'label' => 'Social Links', 'meta' => 'active links', 'icon' => 'ti-share'],
            ['key' => 'about', 'label' => 'About Sections', 'meta' => 'visible sections', 'icon' => 'ti-info-circle'],
        ];
    @endphp

    <section class="overview-panel">
        <div class="overview-panel-head">
            <div>
                <span>Website content</span>
                <h2>Content Library</h2>
            </div>
            <a href="{{ route('trash.index') }}">Open trash</a>
        </div>
        <div class="overview-cms-grid">
            @foreach($contentCards as $card)
                @php($content = $contentStats[$card['key']])
                <a href="{{ $content['route'] }}" class="overview-cms-card">
                    <i class="ti {{ $card['icon'] }}"></i>
                    <span>{{ $card['label'] }}</span>
                    <strong>{{ number_format($content['total']) }}</strong>
                    <small>{{ number_format($content['active']) }} {{ $card['meta'] }}</small>
                </a>
            @endforeach
        </div>
    </section>

    <div class="overview-grid">
        <section class="overview-panel overview-chart-panel">
            <div class="overview-panel-head">
                <div>
                    <span>Last 7 days</span>
                    <h2>Revenue Trend</h2>
                </div>
                <strong>Peak ₹{{ number_format($maxRevenue, 2) }}</strong>
            </div>
            <div class="overview-line-chart">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" role="img" aria-label="Seven day revenue line chart">
                    <polyline points="0,91 100,91" class="chart-baseline"></polyline>
                    <polyline points="{{ $linePoints }}" class="chart-line"></polyline>
                </svg>
            </div>
            <div class="overview-chart-labels">
                @foreach($dailySales as $day)
                    <span>{{ $day['label'] }}</span>
                @endforeach
            </div>
        </section>

        <section class="overview-panel">
            <div class="overview-panel-head">
                <div>
                    <span>Payment Health</span>
                    <h2>Status Split</h2>
                </div>
            </div>
            <div class="payment-donut" style="--paid: {{ ($paid / $paymentTotal) * 100 }}; --pending: {{ ($pending / $paymentTotal) * 100 }};">
                <strong>{{ $paymentTotal }}</strong>
                <span>Orders</span>
            </div>
            <div class="overview-legend">
                <span><b class="paid"></b>Paid {{ $paid }}</span>
                <span><b class="pending"></b>Pending {{ $pending }}</span>
                <span><b class="failed"></b>Failed {{ $failed }}</span>
            </div>
        </section>
    </div>

    <div class="overview-grid overview-content-row">
        <section class="overview-panel">
            <div class="overview-panel-head">
                <div>
                    <span>Fresh content</span>
                    <h2>Latest Updates</h2>
                </div>
            </div>
            <div class="overview-list overview-content-list">
                @foreach($latestContent as $item)
                    <a href="{{ $item['route'] }}">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['title'] }}</strong>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="overview-panel">
            <div class="overview-panel-head">
                <div>
                    <span>Needs attention</span>
                    <h2>Action Queue</h2>
                </div>
            </div>
            <div class="overview-action-list">
                <a href="{{ route('inquiries.index') }}">
                    <i class="ti ti-message-question"></i>
                    <span>Reply to inquiries</span>
                    <strong>{{ number_format($contentTotals['pending_inquiries']) }}</strong>
                </a>
                <a href="{{ route('admin.reviews.index') }}">
                    <i class="ti ti-star"></i>
                    <span>Moderate reviews</span>
                    <strong>{{ number_format($contentTotals['pending_reviews']) }}</strong>
                </a>
                <a href="{{ route('sales.index', ['payment_status' => 'failed']) }}">
                    <i class="ti ti-credit-card-off"></i>
                    <span>Failed payments</span>
                    <strong>{{ number_format($stats['failed_payments']) }}</strong>
                </a>
                <a href="{{ route('trash.index') }}">
                    <i class="ti ti-trash"></i>
                    <span>Trash items</span>
                    <strong>{{ number_format($contentTotals['trashed_items']) }}</strong>
                </a>
            </div>
        </section>
    </div>

    <div class="overview-grid lower">
        <section class="overview-panel">
            <div class="overview-panel-head">
                <div>
                    <span>Daily volume</span>
                    <h2>Orders by Day</h2>
                </div>
            </div>
            <div class="overview-bar-chart">
                @php($maxOrders = max(1, (int) $dailySales->max('orders')))
                @foreach($dailySales as $day)
                    <div>
                        <span style="height: {{ max(8, ($day['orders'] / $maxOrders) * 100) }}%"></span>
                        <small>{{ $day['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="overview-panel">
            <div class="overview-panel-head">
                <div>
                    <span>Products</span>
                    <h2>Top Items</h2>
                </div>
                <a href="{{ route('sales.items') }}">View all</a>
            </div>
            <div class="overview-list">
                @forelse($topProducts as $product)
                    <div>
                        <span>{{ $product->product_name }}</span>
                        <strong>₹{{ number_format((float) $product->revenue, 2) }}</strong>
                        <small>{{ (int) $product->quantity }} sold</small>
                    </div>
                @empty
                    <p>No product sales yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="overview-panel">
        <div class="overview-panel-head">
            <div>
                <span>Latest activity</span>
                <h2>Recent Orders</h2>
            </div>
            <a href="{{ route('sales.index') }}">Open sales</a>
        </div>
        <table class="custom-table overview-orders-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong><small>{{ $order->items_count }} item(s)</small></td>
                        <td>{{ $order->customer_name }}<small>{{ $order->customer_phone }}</small></td>
                        <td>₹{{ number_format((float) $order->grand_total, 2) }}</td>
                        <td><span class="sales-pill payment-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td><span class="sales-pill status-{{ $order->status }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                        <td>{{ $order->created_at->format('h:i A') }}<small>{{ $order->created_at->format('d M') }}</small></td>
                        <td><a href="{{ route('sales.orders.show', $order) }}" class="sales-link-btn">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
