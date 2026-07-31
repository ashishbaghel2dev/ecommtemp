@extends('client.layouts.app')

@php
    $searchTerm = trim((string) request('q', ''));
@endphp

@section('title', $searchTerm ? 'Search results for '.$searchTerm.' | Go Sowa' : 'All Herbal Tea Products | Go Sowa')
@section('meta_description', $searchTerm ? 'Search Go Sowa herbal tea products for '.$searchTerm.' with filters for price, category, availability, and best sellers.' : 'Shop all Go Sowa herbal tea products with filters for price, category, availability, best sellers, and wellness tea blends.')
@section('meta_keywords', 'Go Sowa products, herbal tea products, wellness tea, best herbal tea, buy tea online')
@section('canonical', route('client.products.index'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/category-products.css') }}">
@endpush

@push('head')
    @php
        $itemList = $products->getCollection()->values()->map(fn ($product, $index) => [
            '@type' => 'ListItem',
            'position' => $products->firstItem() + $index,
            'url' => route('products.show', $product->slug),
            'name' => $product->name,
        ])->all();
    @endphp
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'All Go Sowa Products',
            'url' => route('client.products.index'),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $itemList,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
@php
    $minBound = floor((float) ($priceBounds->min_price ?? 0));
    $maxBound = ceil((float) ($priceBounds->max_price ?? 0));
    $activeFilterCount = (request()->filled('q') ? 1 : 0)
        + (request()->filled('category') ? 1 : 0)
        + (request()->filled('min_price') ? 1 : 0)
        + (request()->filled('max_price') ? 1 : 0)
        + (request()->boolean('in_stock') ? 1 : 0)
        + (request()->boolean('best_sellers') ? 1 : 0);
@endphp

<section class="category-products-page">
    <header class="category-products-hero all-products-hero">
        <div class="category-hero-copy">
            <span class="category-eyebrow">{{ $searchTerm ? 'Product search' : 'Go Sowa Store' }}</span>
            <h1>{{ $searchTerm ? 'Search results for "'.$searchTerm.'"' : 'Herbal Tea Products' }}</h1>
          
        </div>

        <div class="category-hero-side">
            <i class="ti ti-leaf"></i>
            <div class="category-products-summary">
                <strong>{{ $products->total() }}</strong>
                <span>Products found</span>
            </div>
        </div>
    </header>

    <form action="{{ route('client.products.index') }}" method="GET" class="category-products-layout" id="categoryFilterForm">
        <aside class="category-filter-panel" aria-label="Product filters">
            <div class="category-filter-head">
                <div>
                    <span>Filters</span>
                    <strong>Catalog</strong>
                </div>
                @if($activeFilterCount)
                    <a href="{{ route('client.products.index') }}">Clear all</a>
                @endif
            </div>

            <div class="category-filter-group">
                <button type="button" class="category-filter-toggle" data-filter-toggle>
                    <span>Search</span>
                    <i class="ti ti-chevron-down"></i>
                </button>
                <div class="category-filter-body">
                    <label class="catalog-search-field" id="productSearch">
                        <span>Product, SKU, benefit</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search herbal tea">
                    </label>
                </div>
            </div>

            <div class="category-filter-group">
                <button type="button" class="category-filter-toggle" data-filter-toggle>
                    <span>Category</span>
                    <i class="ti ti-chevron-down"></i>
                </button>
                <div class="category-filter-body option-filter-list">
                    @foreach($categories as $category)
                        <label class="option-filter-item">
                            <input type="radio" name="category" value="{{ $category->id }}" {{ (int) request('category') === (int) $category->id ? 'checked' : '' }}>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="category-filter-group">
                <button type="button" class="category-filter-toggle" data-filter-toggle>
                    <span>Price range</span>
                    <i class="ti ti-chevron-down"></i>
                </button>
                <div class="category-filter-body">
                    <div class="price-filter-grid">
                        <label>
                            <span>Min</span>
                            <input type="number" name="min_price" min="0" value="{{ request('min_price') }}" placeholder="{{ $minBound }}">
                        </label>
                        <label>
                            <span>Max</span>
                            <input type="number" name="max_price" min="0" value="{{ request('max_price') }}" placeholder="{{ $maxBound }}">
                        </label>
                    </div>
                    <div class="price-filter-range">
                        <span>₹{{ number_format($minBound) }}</span>
                        <span>₹{{ number_format($maxBound) }}</span>
                    </div>
                </div>
            </div>

            <div class="category-filter-group">
                <label class="stock-filter">
                    <input type="checkbox" name="in_stock" value="1" {{ request()->boolean('in_stock') ? 'checked' : '' }}>
                    <span>In stock only</span>
                </label>
                <label class="stock-filter">
                    <input type="checkbox" name="best_sellers" value="1" {{ request()->boolean('best_sellers') ? 'checked' : '' }}>
                    <span>Best sellers</span>
                </label>
            </div>

            <button type="submit" class="apply-filter-btn">
                <i class="ti ti-search"></i>
                Search Products
            </button>
        </aside>

        <section class="category-products-content">
            <div class="category-products-toolbar">
                <button type="button" class="mobile-filter-btn" data-mobile-filter>
                    <i class="ti ti-adjustments-horizontal"></i>
                    Filters
                    @if($activeFilterCount)
                        <span>{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <div class="active-filter-strip">
                    @if(request('q'))
                        <span>Search: {{ request('q') }}</span>
                    @endif
                    @if(request()->boolean('best_sellers'))
                        <span>Best sellers</span>
                    @endif
                    @if(request()->boolean('in_stock'))
                        <span>In stock</span>
                    @endif
                    @if(request()->filled('min_price') || request()->filled('max_price'))
                        <span>₹{{ request('min_price', $minBound) }} - ₹{{ request('max_price', $maxBound) }}</span>
                    @endif
                </div>

                <label class="sort-control">
                    <span>Sort by</span>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Newest</option>
                        <option value="popular" {{ $currentSort === 'popular' ? 'selected' : '' }}>Popular</option>
                        <option value="name_asc" {{ $currentSort === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ $currentSort === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                        <option value="price_low" {{ $currentSort === 'price_low' ? 'selected' : '' }}>Price Low to High</option>
                        <option value="price_high" {{ $currentSort === 'price_high' ? 'selected' : '' }}>Price High to Low</option>
                    </select>
                </label>
            </div>

            @include('client.pages.products.partials.catalog-grid', [
                'products' => $products,
                'resetUrl' => route('client.products.index'),
            ])
        </section>
    </form>
</section>
@endsection

@push('scripts')
    @include('client.pages.products.partials.catalog-grid-script')
@endpush
