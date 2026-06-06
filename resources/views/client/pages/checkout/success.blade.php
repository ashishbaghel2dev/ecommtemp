@extends('client.layouts.app')

@section('title', 'Order Placed')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/checkout.css') }}">
@endpush

@section('content')
<section class="checkout-page">
    <div class="checkout-success">
        <i class="ti ti-circle-check"></i>
        <span>Order Placed</span>
        <h1>Thank you for your order</h1>
        <p>Your order <strong>{{ $order->order_number }}</strong> has been received. We will contact you on {{ $order->customer_phone }} for delivery confirmation.</p>

        <div class="checkout-success-grid">
            <div>
                <span>Total</span>
                <strong>₹{{ number_format((float) $order->grand_total, 2) }}</strong>
            </div>
            <div>
                <span>Payment</span>
                <strong>{{ strtoupper($order->payment_method) }}</strong>
            </div>
            <div>
                <span>Status</span>
                <strong>{{ ucfirst($order->status) }}</strong>
            </div>
        </div>

        <a href="{{ route('home') }}">Continue Shopping</a>
    </div>
</section>
@endsection
