@extends('emails.layouts.store')

@section('title', 'Order Confirmed')
@section('preheader', 'Your order has been placed successfully.')

@section('content')
    <h1 style="margin:0;color:#15803d;font-size:24px;">Thank you, {{ $order->customer_name }}</h1>
    <p style="margin:10px 0 18px;color:#64748b;line-height:1.6;">Your order has been received successfully. We will keep you updated as it moves through delivery.</p>
    @include('emails.partials.order-table', ['order' => $order])
@endsection
