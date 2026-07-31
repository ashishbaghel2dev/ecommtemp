@extends('client.layouts.app')

@php
    $title = $aboutPart?->title ?: 'About Go Sowa';
    $description = $aboutPart?->description;
    $shortDescription = $aboutPart?->short_description ?: 'Natural wellness products made with care, clarity, and everyday trust.';
    $mainImage = $aboutPart?->image_1 ?: 'home-carousel-images/gosowa-tea-valley-hero.png';
    $supportImage = $aboutPart?->image_2 ?: $aboutPart?->image_3;
@endphp

@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/about.css') }}">
@endpush

@section('content')
<section class="about-page">
    <div class="about-hero">
        <div class="about-hero-copy">
            <span>About Us</span>
            <h1>{{ $title }}</h1>
            <p>{{ $shortDescription }}</p>
            <div class="about-hero-actions">
                <a href="{{ route('contact') }}">Contact Us</a>
                <a href="{{ route('home') }}#products">Explore Products</a>
            </div>
        </div>
        <div class="about-hero-media">
            <img src="{{ asset($mainImage) }}" alt="{{ $title }}">
        </div>
    </div>

    <div class="about-story">
        <div>
            <span>Our Story</span>
            <h2>Built around honest quality and dependable service.</h2>
        </div>
        <div class="about-story-content">
            @if($description)
                {!! $description !!}
            @else
                <p>Go Sowa brings carefully selected wellness products to customers who value purity, consistency, and a smooth buying experience. From product selection to delivery, every step is shaped around trust.</p>
                <p>We keep the shopping experience simple: clear prices, secure checkout, responsive support, and products that fit naturally into daily routines.</p>
            @endif
        </div>
    </div>

    <div class="about-values">
        <article>
            <i class="ti ti-leaf"></i>
            <h3>Natural Focus</h3>
            <p>Products are presented with clear information so customers can choose confidently.</p>
        </article>
        <article>
            <i class="ti ti-shield-check"></i>
            <h3>Trusted Checkout</h3>
            <p>Secure payment options, COD support, and transparent order totals from cart to delivery.</p>
        </article>
        <article>
            <i class="ti ti-headset"></i>
            <h3>Customer Care</h3>
            <p>Support is easy to reach for product questions, order help, and delivery updates.</p>
        </article>
    </div>

    <div class="about-service-band">
        @if($supportImage)
            <img src="{{ asset($supportImage) }}" alt="Go Sowa service">
        @endif
        <div>
            <span>Why Customers Choose Us</span>
            <h2>Every order is handled with care from product page to doorstep.</h2>
            <ul>
                <li><i class="ti ti-circle-check"></i> Carefully maintained product catalogue</li>
                <li><i class="ti ti-circle-check"></i> Clear discounts and final payable amount</li>
                <li><i class="ti ti-circle-check"></i> Reliable shipping information and order updates</li>
            </ul>
        </div>
    </div>
</section>
@endsection
