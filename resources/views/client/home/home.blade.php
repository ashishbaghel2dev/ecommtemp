@extends('client.layouts.app')

@section('title', 'Home Page')

@section('content')

@include('client.home.components.showcase-banner-carousel')

@include('client.home.components.category-carousel')

@include('client.home.components.product-carousel')

<h1>home</h1>

@endsection
