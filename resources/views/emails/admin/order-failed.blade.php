@extends('emails.layouts.store')

@section('title', 'Payment Failed')
@section('preheader', 'An order payment failed or needs attention.')

@section('content')
    <h1 style="margin:0;color:#b42318;font-size:24px;">Payment Failed / Warning</h1>
    <p style="margin:10px 0 18px;color:#64748b;line-height:1.6;">This order needs attention. Reason: <strong>{{ $reason ?: 'Payment could not be completed.' }}</strong></p>
    @include('emails.partials.order-table', ['order' => $order])
@endsection
