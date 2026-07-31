@extends('admin.layouts.app')

@section('title', 'Sales Orders')

@section('content')
<div class="main-content sales-admin-page">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Sales Orders</h2>
            <p class="page-subtitle">Track orders, payments, customers, and fulfilment from one place.</p>
        </div>
        <a href="{{ route('sales.customers') }}" class="btn-primary"><i class="ti ti-users"></i> Customers</a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    <div class="sales-stats-grid">
        <article><span>Total Orders</span><strong>{{ number_format($stats['orders']) }}</strong></article>
        <article><span>Paid Revenue</span><strong>₹{{ number_format($stats['revenue'], 2) }}</strong></article>
        <article><span>Open Orders</span><strong>{{ number_format($stats['pending']) }}</strong></article>
        <article><span>Failed Payments</span><strong>{{ number_format($stats['failed']) }}</strong></article>
    </div>

    <div class="filter-card">
        <form method="GET" class="filter-form sales-filter-form">
            <input class="input-field" type="search" name="search" value="{{ request('search') }}" placeholder="Order, customer, email, phone">
            <select class="input-field" name="status">
                <option value="">All order statuses</option>
                @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'payment_failed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <select class="input-field" name="payment_status">
                <option value="">All payment statuses</option>
                @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="btn-filter" type="submit"><i class="ti ti-search"></i> Filter</button>
            <a href="{{ route('sales.index') }}" class="sales-link-btn">Reset</a>
        </form>
    </div>

    <div class="table-card sales-table-card">
        <div class="admin-table-scroll">
            <table class="custom-table sales-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong><small>{{ strtoupper($order->payment_method) }}</small></td>
                            <td>
                                <strong>{{ $order->customer_name }}</strong>
                                <small>{{ $order->customer_email }} · {{ $order->customer_phone }}</small>
                            </td>
                            <td>{{ $order->items_count }}</td>
                            <td><strong>₹{{ number_format((float) $order->grand_total, 2) }}</strong></td>
                            <td><span class="sales-pill payment-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td><span class="sales-pill status-{{ $order->status }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                            <td>{{ $order->created_at->format('d M Y') }}<small>{{ $order->created_at->format('h:i A') }}</small></td>
                            <td>
                                <div class="product-image-box">
                                    <a href="{{ route('sales.orders.show', $order) }}" class="btn-icon edit" title="View order"><i class="ti ti-eye"></i></a>
                                    @if($order->user)
                                        <a href="{{ route('sales.customers.show', $order->user) }}" class="btn-icon edit" title="View customer"><i class="ti ti-user-search"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
</div>
@endsection
