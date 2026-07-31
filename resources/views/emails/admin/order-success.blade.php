@extends('emails.layouts.store')

@section('title', 'New Order')
@section('preheader', 'A new order was placed.')

@section('content')
    <h1 style="margin:0;color:#172033;font-size:24px;">New Order Received</h1>
    <p style="margin:10px 0 18px;color:#64748b;line-height:1.6;">Please review inventory, payment, and delivery preparation.</p>
    @include('emails.partials.order-table', ['order' => $order])
@endsection
