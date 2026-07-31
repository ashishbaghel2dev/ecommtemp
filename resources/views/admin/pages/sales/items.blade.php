@extends('admin.layouts.app')

@section('title', 'Order Items')

@section('content')
<div class="main-content sales-admin-page">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Order Items</h2>
            <p class="page-subtitle">Line-item view for fulfilment and product sales review.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn-primary"><i class="ti ti-shopping-cart"></i> Orders</a>
    </div>

    <div class="filter-card">
        <form method="GET" class="filter-form sales-filter-form">
            <input class="input-field" type="search" name="search" value="{{ request('search') }}" placeholder="Product, SKU, order, customer">
            <button class="btn-filter" type="submit"><i class="ti ti-search"></i> Search</button>
            <a href="{{ route('sales.items') }}" class="sales-link-btn">Reset</a>
        </form>
    </div>

    <div class="table-card sales-table-card">
        <div class="admin-table-scroll">
            <table class="custom-table sales-table sales-items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="sales-product-cell">
                                    <img src="{{ asset($item->product_image ?: 'images/no-image.png') }}" alt="{{ $item->product_name }}">
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        <small>{{ $item->product_sku ?: '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><a href="{{ route('sales.orders.show', $item->order_id) }}">{{ $item->order_number }}</a></td>
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format((float) $item->price, 2) }}</td>
                            <td><strong>₹{{ number_format((float) $item->total, 2) }}</strong></td>
                            <td><span class="sales-pill status-{{ $item->status }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span></td>
                            <td>{{ \Illuminate\Support\Carbon::parse($item->ordered_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No order items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
