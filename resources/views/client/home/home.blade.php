@extends('client.layouts.app')

@section('title', 'Home Page')

@section('content')

@include('client.home.components.showcase-banner-carousel')

@include('client.home.components.category-carousel')

@foreach($labelProductCarousels ?? [] as $carousel)
    @include('client.home.components.product-carousel', [
        'carouselKey' => $carousel['key'],
        'carouselEyebrow' => $carousel['eyebrow'],
        'carouselTitle' => $carousel['title'],
        'carouselPromoImage' => $carousel['promoImage'],
        'carouselProducts' => $carousel['products'],
    ])
@endforeach

@foreach($categoryProductCarousels ?? [] as $carousel)
    @include('client.home.components.product-carousel', [
        'carouselKey' => $carousel['key'],
        'carouselEyebrow' => $carousel['eyebrow'],
        'carouselTitle' => $carousel['title'],
        'carouselPromoImage' => $carousel['promoImage'],
        'carouselProducts' => $carousel['products'],
    ])
@endforeach

@include('client.home.components.product-carousel', [
    'carouselKey' => 'featured-products',
    'carouselEyebrow' => 'Featured products',
    'carouselTitle' => 'Products You May Like',
    'carouselPromoImage' => $featuredProductsPromoImage ?? 'products/images/featured-products.svg',
    'carouselProducts' => $products,
])

@include('client.home.components.review-carousel')

@include('client.home.components.why-choose')

@endsection
