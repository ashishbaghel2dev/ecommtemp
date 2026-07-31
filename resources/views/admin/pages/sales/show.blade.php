@extends('admin.layouts.app')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="main-content sales-admin-page">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Order {{ $order->order_number }}</h2>
            <p class="page-subtitle">{{ $order->customer_name }} · {{ $order->created_at?->format('d M Y h:i A') ?: 'Date unavailable' }}</p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn-primary"><i class="ti ti-arrow-left"></i> Back to Sales</a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    <div class="sales-detail-grid">
        <section class="table-card sales-detail-card">
            <h3>Order Summary</h3>
            <dl class="sales-definition-list">
                <div><dt>Total</dt><dd>₹{{ number_format((float) $order->grand_total, 2) }}</dd></div>
                <div><dt>Subtotal</dt><dd>₹{{ number_format((float) $order->subtotal, 2) }}</dd></div>
                <div><dt>Discount</dt><dd>₹{{ number_format((float) $order->discount_total, 2) }}</dd></div>
                <div><dt>Tax</dt><dd>₹{{ number_format((float) $order->tax_total, 2) }}</dd></div>
                <div><dt>Shipping</dt><dd>₹{{ number_format((float) $order->shipping_total, 2) }}</dd></div>
                <div><dt>Payment</dt><dd>{{ strtoupper($order->payment_method) }} / {{ ucfirst($order->payment_status) }}</dd></div>
                <div><dt>Order Status</dt><dd>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</dd></div>
                <div><dt>Coupon</dt><dd>{{ $order->coupon_code ?: '-' }}</dd></div>
            </dl>
        </section>

        <section class="table-card sales-detail-card">
            <h3>Customer Info</h3>
            <dl class="sales-definition-list">
                <div><dt>Name</dt><dd>{{ $order->customer_name }}</dd></div>
                <div><dt>Email</dt><dd>{{ $order->customer_email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $order->customer_phone }}</dd></div>
                <div><dt>Account</dt><dd>{{ $order->user ? 'Registered' : 'Guest' }}</dd></div>
            </dl>
            @if($order->user)
                <a class="sales-link-btn" href="{{ route('sales.customers.show', $order->user) }}">View customer history</a>
            @endif
        </section>

        <section class="table-card sales-detail-card">
            <h3>Update Order</h3>
            <form action="{{ route('sales.orders.update', $order) }}" method="POST" class="sales-update-form">
                @csrf
                @method('PUT')
                <label>
                    <span>Order Status</span>
                    <select class="input-field" name="status">
                        @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'payment_failed'] as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Payment Status</span>
                    <select class="input-field" name="payment_status">
                        @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                            <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Internal Notes</span>
                    <textarea class="input-field" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                </label>
                <button type="submit" class="btn-filter"><i class="ti ti-device-floppy"></i> Save Order</button>
            </form>
        </section>
    </div>

    <div class="sales-detail-grid single-wide">
        <section class="table-card sales-detail-card">
            <h3>Shipping Address</h3>
            <p class="sales-address">{{ $order->shipping_address_line_1 }}, {{ $order->shipping_address_line_2 }}{{ data_get($order->shipping_address_snapshot, 'landmark') ? ', Landmark: '.data_get($order->shipping_address_snapshot, 'landmark') : '' }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}</p>
        </section>
    </div>

    <div class="table-card">
        <table class="custom-table sales-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Options</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="sales-product-cell">
                                <img src="{{ asset($item->product_image ?: 'images/no-image.png') }}" alt="{{ $item->product_name }}">
                                <strong>{{ $item->product_name }}</strong>
                            </div>
                        </td>
                        <td>{{ $item->product_sku ?: '-' }}</td>
                        <td>{{ \App\Services\StoreMailService::itemOptions($item->meta ?? []) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format((float) $item->price, 2) }}</td>
                        <td><strong>₹{{ number_format((float) $item->total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
