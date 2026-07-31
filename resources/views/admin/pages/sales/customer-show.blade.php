@extends('admin.layouts.app')

@section('title', 'Customer '.$user->name)

@section('content')
<div class="main-content sales-admin-page">
    <div class="top-bar">
        <div>
            <h2 class="page-title">{{ $user->name }}</h2>
            <p class="page-subtitle">{{ $user->email }} · {{ $user->phone ?: 'No phone' }}</p>
        </div>
        <a href="{{ route('sales.customers') }}" class="btn-primary"><i class="ti ti-arrow-left"></i> Back to Customers</a>
    </div>

    <div class="sales-stats-grid">
        <article><span>Total Orders</span><strong>{{ $summary['orders'] }}</strong></article>
        <article><span>Paid Orders</span><strong>{{ $summary['paid'] }}</strong></article>
        <article><span>Total Spent</span><strong>₹{{ number_format($summary['spent'], 2) }}</strong></article>
        <article><span>Wishlist</span><strong>{{ $summary['wishlist'] }}</strong></article>
    </div>

    <div class="sales-detail-grid">
        <section class="table-card sales-detail-card">
            <h3>Profile</h3>
            <dl class="sales-definition-list">
                <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $user->phone ?: '-' }}</dd></div>
                <div><dt>Status</dt><dd>{{ $user->status ? 'Active' : 'Inactive' }}</dd></div>
                <div><dt>Last Login</dt><dd>{{ $user->last_login_at?->format('d M Y h:i A') ?: '-' }}</dd></div>
                <div><dt>Last IP</dt><dd>{{ $user->last_login_ip ?: '-' }}</dd></div>
            </dl>
        </section>

        <section class="table-card sales-detail-card">
            <h3>Addresses</h3>
            @forelse($user->addresses as $address)
                <p class="sales-address"><strong>{{ $address->label }}</strong><br>{{ $address->address_line_1 }}, {{ $address->city }} - {{ $address->postal_code }}</p>
            @empty
                <p class="sales-address">No saved addresses.</p>
            @endforelse
        </section>

        <section class="table-card sales-detail-card">
            <h3>Cart & Wishlist</h3>
            <dl class="sales-definition-list">
                <div><dt>Active Cart Items</dt><dd>{{ $summary['cart_items'] }}</dd></div>
                <div><dt>Wishlist Products</dt><dd>{{ $summary['wishlist'] }}</dd></div>
            </dl>
            @if($user->wishlists->isNotEmpty())
                <div class="sales-mini-list">
                    @foreach($user->wishlists->take(5) as $wishlist)
                        <span>{{ $wishlist->product?->name ?? 'Product #'.$wishlist->product_id }}</span>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <div class="table-card">
        <table class="custom-table sales-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->items_count }}</td>
                        <td>₹{{ number_format((float) $order->grand_total, 2) }}</td>
                        <td><span class="sales-pill payment-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td><span class="sales-pill status-{{ $order->status }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td><a href="{{ route('sales.orders.show', $order) }}" class="btn-icon edit"><i class="ti ti-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="7">This customer has no orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
