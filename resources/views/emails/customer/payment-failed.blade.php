@extends('emails.layouts.store')

@section('title', 'Payment Failed')
@section('preheader', 'Your payment could not be completed.')

@section('content')
    <h1 style="margin:0;color:#b42318;font-size:24px;">Payment could not be completed</h1>
    <p style="margin:10px 0 18px;color:#64748b;line-height:1.6;">Hi {{ $order->customer_name }}, we could not complete payment for your order. {{ $reason ?: 'Please try again or contact support if money was debited.' }}</p>
    @include('emails.partials.order-table', ['order' => $order])
@endsection
