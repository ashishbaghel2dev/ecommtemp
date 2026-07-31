@extends('client.layouts.app')

@section('title', 'Order Placed')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/checkout.css') }}">
@endpush

@section('content')
<section class="checkout-page">
    <div class="checkout-progress">
        @foreach(['Cart', 'Account', 'Address', 'Review', 'Success'] as $step)
            <span class="{{ $step === 'Success' ? 'is-current' : 'is-complete' }} step-{{ strtolower($step) }}">{{ $step }}</span>
        @endforeach
    </div>

    <div class="checkout-success">
        <i class="ti ti-circle-check"></i>
        <span>Order Placed</span>
        <h1>Thank you for your order</h1>
        <p>Your order <strong>{{ $order->order_number }}</strong> has been received.</p>

        <div class="checkout-success-grid">
            <div><span>Total</span><strong>₹{{ number_format((float) $order->grand_total, 2) }}</strong></div>
            <div><span>Payment</span><strong>Cash on Delivery</strong></div>
            <div><span>Payment Status</span><strong>{{ ucfirst($order->payment_status) }}</strong></div>
            <div><span>Order Status</span><strong>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</strong></div>
            <div><span>Delivery ETA</span><strong>{{ $order->estimated_delivery_date?->format('d M Y') ?? 'Soon' }}</strong></div>
            <div><span>Ship To</span><strong>{{ $order->shipping_city }} - {{ $order->shipping_postal_code }}</strong></div>
        </div>

        <div class="success-details">
            <section>
                <h2>Delivery Address</h2>
                <p>{{ $order->customer_name }} · {{ $order->customer_phone }}</p>
                <p>{{ $order->shipping_address_line_1 }}, {{ $order->shipping_address_line_2 }}{{ data_get($order->shipping_address_snapshot, 'landmark') ? ', Landmark: '.data_get($order->shipping_address_snapshot, 'landmark') : '' }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}</p>
            </section>

            <section>
                <h2>Ordered Products</h2>
                @foreach($order->items as $item)
                    <div class="success-item">
                        <img src="{{ asset($item->product_image ?: 'images/no-image.png') }}" alt="{{ $item->product_name }}">
                        <span>{{ $item->product_name }} <small>SKU {{ $item->product_sku ?: '-' }} · Qty {{ $item->quantity }}</small></span>
                        <strong>₹{{ number_format((float) $item->total, 2) }}</strong>
                    </div>
                @endforeach
            </section>
        </div>

        <div class="success-actions">
            <a href="{{ route('home') }}">Continue Shopping</a>
            <a href="{{ route('orders.invoice', $order) }}">Download Invoice</a>
            <a href="#" aria-disabled="true">Track Order</a>
            <a href="{{ route('dashboard') }}">View My Orders</a>
        </div>
    </div>
</section>
@endsection
