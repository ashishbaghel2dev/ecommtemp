@extends('client.layouts.app')

@section('title', $labelTitle)
@section('meta_description', 'Shop '.$labelTitle.' from Go Sowa, including herbal tea blends, wellness teas, and carefully selected products for daily routines.')
@section('meta_keywords', $labelTitle.', Go Sowa products, herbal tea, wellness tea, buy tea online')
@section('canonical', route('labels.show', $label?->slug ?? request()->route('label')))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/category-products.css') }}">
@endpush

@push('head')
    @php
        $labelItems = method_exists($products, 'getCollection')
            ? $products->getCollection()->values()->map(fn ($product, $index) => [
                '@type' => 'ListItem',
                'position' => $products->firstItem() + $index,
                'url' => route('products.show', $product->slug),
                'name' => $product->name,
            ])->all()
            : [];
    @endphp
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $labelTitle,
            'url' => route('labels.show', $label?->slug ?? request()->route('label')),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $labelItems,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
<section class="category-products-page">
    <header class="category-products-hero all-products-hero">
        <div class="category-hero-copy">
            <span class="category-eyebrow">Shop by label</span>
            <h1>{{ $labelTitle }}</h1>
          
        </div>

        <div class="category-hero-side">
            <i class="ti ti-award"></i>
            <div class="category-products-summary">
                <strong>{{ method_exists($products, 'total') ? $products->total() : $products->count() }}</strong>
                <span>Products found</span>
            </div>
        </div>
    </header>

    <section class="category-products-content label-products-content">
        @include('client.pages.products.partials.catalog-grid', [
            'products' => $products,
            'resetUrl' => route('labels.show', $label?->slug ?? request()->route('label')),
        ])
    </section>
</section>
@endsection

@push('scripts')
    @include('client.pages.products.partials.catalog-grid-script')
@endpush
