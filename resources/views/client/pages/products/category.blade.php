@extends('client.layouts.app')

@section('title', $category->name . ' Products')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/category-products.css') }}">
@endpush

@section('content')
@php
    $minBound = floor((float) ($priceBounds->min_price ?? 0));
    $maxBound = ceil((float) ($priceBounds->max_price ?? 0));
    $selectedIds = $selectedAttributeValues->all();
    $activeFilterCount = count($selectedIds) + (request()->filled('min_price') ? 1 : 0) + (request()->filled('max_price') ? 1 : 0) + (request()->boolean('in_stock') ? 1 : 0);
@endphp

<section class="category-products-page">
    <header class="category-products-hero">
        <div>
            <nav class="category-products-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <i class="ti ti-chevron-right"></i>
                <span>{{ $category->name }}</span>
            </nav>
            <h1>{{ $category->name }}</h1>
            <p>{{ $category->description ?: 'Browse products, compare options, and narrow the list with useful filters.' }}</p>
        </div>

        <div class="category-products-summary">
            <strong>{{ $products->total() }}</strong>
            <span>Products found</span>
        </div>
    </header>

    <form action="{{ route('categories.show', $category->slug) }}" method="GET" class="category-products-layout" id="categoryFilterForm">
        <aside class="category-filter-panel" aria-label="Product filters">
            <div class="category-filter-head">
                <div>
                    <span>Filters</span>
                    <strong>{{ $category->name }}</strong>
                </div>
                @if($activeFilterCount)
                    <a href="{{ route('categories.show', $category->slug) }}">Clear all</a>
                @endif
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
            </div>

            @foreach($filterAttributes as $attribute)
                <div class="category-filter-group">
                    <button type="button" class="category-filter-toggle" data-filter-toggle>
                        <span>{{ $attribute->name }}</span>
                        <i class="ti ti-chevron-down"></i>
                    </button>

                    <div class="category-filter-body option-filter-list">
                        @foreach($attribute->values as $value)
                            <label class="option-filter-item">
                                <input type="checkbox"
                                       name="attributes[{{ $attribute->id }}][]"
                                       value="{{ $value->id }}"
                                       {{ in_array($value->id, $selectedIds, true) ? 'checked' : '' }}>
                                <span>
                                    @if($value->color_code)
                                        <em style="--swatch-color: {{ $value->color_code }}"></em>
                                    @endif
                                    {{ $value->value }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit" class="apply-filter-btn">
                <i class="ti ti-adjustments-horizontal"></i>
                Apply Filters
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
                    @if(request()->filled('min_price') || request()->filled('max_price'))
                        <span>₹{{ request('min_price', $minBound) }} - ₹{{ request('max_price', $maxBound) }}</span>
                    @endif
                    @if(request()->boolean('in_stock'))
                        <span>In stock</span>
                    @endif
                    @foreach($filterAttributes as $attribute)
                        @foreach($attribute->values as $value)
                            @if(in_array($value->id, $selectedIds, true))
                                <span>{{ $value->value }}</span>
                            @endif
                        @endforeach
                    @endforeach
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

            @if($products->count())
                <div class="category-product-grid">
                    @foreach($products as $product)
                        @php
                            $image = $product->image ?: optional($product->images->first())->image ?: 'images/no-image.png';
                            $finalPrice = (float) $product->final_price;
                            $listPrice = max((float) $product->price, $finalPrice);
                            $discountPercent = $listPrice > $finalPrice ? round((($listPrice - $finalPrice) / $listPrice) * 100) : 0;
                        @endphp

                        <article class="category-product-card">
                            <a href="{{ route('products.show', $product->slug) }}" class="category-product-media">
                                @if($product->labels->first())
                                    <span>{{ $product->labels->first()->name }}</span>
                                @endif
                                <img src="{{ asset($image) }}" alt="{{ $product->name }}" loading="lazy">
                            </a>

                            <div class="category-product-info">
                                <div class="category-product-meta">
                                    <span>{{ $product->sku }}</span>
                                    <span>{{ $product->stock > 0 && $product->in_stock ? 'In stock' : 'Out of stock' }}</span>
                                </div>

                                <a href="{{ route('products.show', $product->slug) }}" class="category-product-title">
                                    {{ $product->name }}
                                </a>

                                <p>{{ \Illuminate\Support\Str::limit($product->short_description ?? 'Reliable product for everyday use.', 92) }}</p>

                                @if($product->attributeValues->count())
                                    <div class="category-product-options">
                                        @foreach($product->attributeValues->take(4) as $productAttributeValue)
                                            <span>{{ $productAttributeValue->attributeValue->value ?? $productAttributeValue->value ?? '-' }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="category-product-footer">
                                    <div>
                                        @if($discountPercent)
                                            <del>₹{{ number_format($listPrice, 2) }}</del>
                                        @endif
                                        <strong>₹{{ number_format($finalPrice, 2) }}</strong>
                                    </div>

                                    @if($discountPercent)
                                        <span class="category-discount">{{ $discountPercent }}% OFF</span>
                                    @endif
                                </div>

                                <div class="category-product-actions">
                                    @if($product->attributeValues->isNotEmpty() || ($product->type === 'configurable' && $product->variants->isNotEmpty()))
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            <i class="ti ti-eye"></i>
                                            View
                                        </a>
                                    @else
                                        <button type="button" data-add-to-cart="{{ $product->id }}">
                                            <i class="ti ti-shopping-cart-plus"></i>
                                            Add
                                        </button>
                                    @endif
                                    <button type="button" data-wishlist="{{ $product->id }}" aria-label="Add {{ $product->name }} to wishlist">
                                        <i class="ti ti-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="category-products-pagination">
                    {{ $products->links() }}
                </div>
            @else
                <div class="category-empty-state">
                    <i class="ti ti-package-off"></i>
                    <h2>No products found</h2>
                    <p>Try clearing a filter or choosing a wider price range.</p>
                    <a href="{{ route('categories.show', $category->slug) }}">Reset filters</a>
                </div>
            @endif
        </section>
    </form>
</section>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('categoryFilterForm');
        const filterPanel = document.querySelector('.category-filter-panel');
        const mobileFilterButton = document.querySelector('[data-mobile-filter]');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        document.querySelectorAll('[data-filter-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                button.closest('.category-filter-group')?.classList.toggle('is-collapsed');
            });
        });

        mobileFilterButton?.addEventListener('click', () => {
            filterPanel?.classList.toggle('is-open');
        });

        document.addEventListener('click', (event) => {
            if (!filterPanel || !mobileFilterButton) return;
            if (window.innerWidth > 900) return;
            if (filterPanel.contains(event.target) || mobileFilterButton.contains(event.target)) return;
            filterPanel.classList.remove('is-open');
        });

        document.querySelectorAll('.option-filter-item input, .stock-filter input').forEach((input) => {
            input.addEventListener('change', () => form?.submit());
        });

        document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
            button.addEventListener('click', async () => {
                const productId = button.dataset.addToCart;
                button.disabled = true;

                try {
                    await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ product_id: productId, quantity: 1 }),
                    });
                    button.classList.add('is-added');
                    button.innerHTML = '<i class="ti ti-check"></i> Added';
                } finally {
                    window.setTimeout(() => {
                        button.disabled = false;
                        button.classList.remove('is-added');
                        button.innerHTML = '<i class="ti ti-shopping-cart-plus"></i> Add';
                    }, 1400);
                }
            });
        });

        document.querySelectorAll('[data-wishlist]').forEach((button) => {
            button.addEventListener('click', async () => {
                try {
                    await fetch(`/wishlist/${button.dataset.wishlist}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                    });
                    button.classList.toggle('is-active');
                } catch (error) {
                    button.classList.remove('is-active');
                }
            });
        });
    })();
</script>
@endpush
