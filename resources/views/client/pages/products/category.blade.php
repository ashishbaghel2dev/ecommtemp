@extends('client.layouts.app')

@section('title', $category->name . ' Products')
@section('meta_description', $category->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $category->description), 155, ''))
@section('canonical', route('categories.show', $category->slug))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/category-products.css') }}">
@endpush

@push('head')
    @php
        $categoryItems = $products->getCollection()->values()->map(fn ($product, $index) => [
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
            'name' => $category->name,
            'url' => route('categories.show', $category->slug),
            'description' => strip_tags($category->description ?? ''),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $categoryItems,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
@php
    $minBound = floor((float) ($priceBounds->min_price ?? 0));
    $maxBound = ceil((float) ($priceBounds->max_price ?? 0));
    $selectedIds = $selectedAttributeValues->all();
    $activeFilterCount = count($selectedIds) + (request()->filled('min_price') ? 1 : 0) + (request()->filled('max_price') ? 1 : 0) + (request()->boolean('in_stock') ? 1 : 0);
    $heroImage = $category->banner ?: $category->image;
@endphp

<section class="category-products-page">
    <header class="category-products-hero" >
        <div class="category-hero-copy">
            <span class="category-eyebrow">Pure Tea Collection</span>
            <h1>{{ $category->name }}</h1>
        
        </div>

        <div class="category-hero-side">
            @if($category->image)
                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
            @else
                <i class="ti ti-leaf"></i>
            @endif
            <div class="category-products-summary">
                <strong>{{ $products->total() }}</strong>
                <span>Products found</span>
            </div>
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
                            $hoverImage = optional($product->images->firstWhere('image', '!=', $image))->image
                                ?: optional($product->images->skip(1)->first())->image;
                            $finalPrice = (float) $product->final_price;
                            $listPrice = max((float) $product->price, $finalPrice);
                            $discountPercent = $listPrice > $finalPrice ? round((($listPrice - $finalPrice) / $listPrice) * 100) : 0;
                            $attributeGroups = $product->attributeValues
                                ->filter(fn ($item) => $item->attribute && ($item->attributeValue || $item->value))
                                ->groupBy('attribute_id');
                            $variantPayload = $product->variants
                                ->map(function ($variant) use ($product, $listPrice, $finalPrice) {
                                    $variantListPrice = (float) ($variant->price ?? $listPrice);
                                    $variantFinalPrice = (float) ($variant->sale_price ?: ($variant->price ?? $finalPrice));

                                    return [
                                        'id' => $variant->id,
                                        'attributes' => collect($variant->attributes ?? [])
                                            ->mapWithKeys(fn ($value, $key) => [(int) $key => (int) $value])
                                            ->all(),
                                        'list_price' => $variantListPrice,
                                        'final_price' => $variantFinalPrice,
                                        'in_stock' => (bool) $variant->in_stock,
                                    ];
                                })
                                ->values();
                            $pricingPayload = [
                                'type' => $product->type,
                                'base_list_price' => $listPrice,
                                'base_final_price' => $finalPrice,
                                'variants' => $variantPayload,
                            ];
                            $ingredientText = trim(strip_tags(html_entity_decode($product->short_description ?? $product->description ?? 'Herbal tea crafted for daily wellness.')));
                            $ingredientText = preg_replace('/^\s*Ingredients\s*:?\s*/i', '', $ingredientText);
                        @endphp

                        <article class="category-product-card"
                                 data-category-product-card
                                 data-pricing='@json($pricingPayload)'>
                            <a href="{{ route('products.show', $product->slug) }}" class="category-product-media {{ $hoverImage ? 'has-hover-image' : '' }}">
                                <img src="{{ asset($image) }}" alt="{{ $product->name }}" loading="lazy" class="product-card-image-primary">
                                @if($hoverImage)
                                    <img src="{{ asset($hoverImage) }}" alt="{{ $product->name }}" loading="lazy" class="product-card-image-hover">
                                @endif
                            </a>

                            <div class="category-product-info">
                                <a href="{{ route('products.show', $product->slug) }}" class="category-product-title">
                                    {{ $product->name }}
                                </a>

                                <p class="category-product-ingredients">
                                    Ingredients : {{ \Illuminate\Support\Str::limit($ingredientText, 54) }}
                                </p>

                                <div class="category-product-price">
                                    <strong data-category-final-price>₹ {{ number_format($finalPrice, 2) }}</strong>
                                    <del data-category-list-price {{ $discountPercent ? '' : 'hidden' }}>₹ {{ number_format($listPrice, 2) }}</del>
                                </div>

                                @if($attributeGroups->isNotEmpty())
                                    <div class="category-product-size">
                                        @foreach($attributeGroups as $attributeId => $values)
                                            @php
                                                $attribute = $values->first()->attribute;
                                            @endphp
                                            <div class="category-product-attribute" data-category-attribute-group data-attribute-id="{{ $attributeId }}">
                                                <span>Select {{ $attribute->name }}:</span>
                                                <div>
                                                    @foreach($values->sortBy(fn ($item) => $item->attributeValue->sort_order ?? $item->id) as $productAttributeValue)
                                                        <button type="button"
                                                                data-size-option
                                                                data-pav-id="{{ $productAttributeValue->id }}"
                                                                data-attribute-id="{{ $attributeId }}"
                                                                data-attribute-value-id="{{ $productAttributeValue->attribute_value_id }}"
                                                                class="{{ $loop->first ? 'is-selected' : '' }}">
                                                            {{ $productAttributeValue->attributeValue->value ?? $productAttributeValue->value }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="category-product-actions">
                                @if($product->attributeValues->isNotEmpty() || ($product->type === 'configurable' && $product->variants->isNotEmpty()))
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        ADD TO CART
                                    </a>
                                @else
                                    <button type="button" data-add-to-cart="{{ $product->id }}">
                                        ADD TO CART
                                    </button>
                                @endif
                                <button type="button" data-wishlist="{{ $product->id }}" aria-label="Add {{ $product->name }} to wishlist">
                                    <i class="ti ti-heart"></i>
                                </button>
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

        const money = (value) => '₹ ' + Number(value || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const normalizeMap = (map) => {
            const normalized = {};

            Object.keys(map || {})
                .map((key) => parseInt(key, 10))
                .filter(Number.isFinite)
                .sort((a, b) => a - b)
                .forEach((key) => {
                    const value = parseInt(map[key], 10);
                    if (Number.isFinite(value)) {
                        normalized[key] = value;
                    }
                });

            return JSON.stringify(normalized);
        };

        const readPricing = (card) => {
            try {
                return JSON.parse(card.dataset.pricing || '{}');
            } catch (error) {
                return {};
            }
        };

        const selectedAttributeMap = (card) => {
            const map = {};

            card.querySelectorAll('[data-size-option].is-selected').forEach((button) => {
                const attributeId = parseInt(button.dataset.attributeId, 10);
                const attributeValueId = parseInt(button.dataset.attributeValueId, 10);

                if (Number.isFinite(attributeId) && Number.isFinite(attributeValueId)) {
                    map[attributeId] = attributeValueId;
                }
            });

            return map;
        };

        const matchingVariant = (pricing, attributeMap, expectedGroupCount) => {
            if (!pricing.variants?.length || Object.keys(attributeMap).length !== expectedGroupCount) {
                return null;
            }

            const selected = normalizeMap(attributeMap);

            return pricing.variants.find((variant) => normalizeMap(variant.attributes || {}) === selected) || null;
        };

        const updateCategoryCardPrice = (card) => {
            const pricing = readPricing(card);
            const groups = card.querySelectorAll('[data-category-attribute-group]');
            const variant = matchingVariant(pricing, selectedAttributeMap(card), groups.length);
            const finalEl = card.querySelector('[data-category-final-price]');
            const listEl = card.querySelector('[data-category-list-price]');

            let finalPrice = Number(pricing.base_final_price || 0);
            let listPrice = Number(pricing.base_list_price || finalPrice);

            if (variant) {
                finalPrice = Number(variant.final_price || finalPrice);
                listPrice = Number(variant.list_price || listPrice || finalPrice);
            }

            if (finalEl) {
                finalEl.textContent = money(finalPrice);
            }
            if (listEl) {
                listEl.textContent = money(listPrice);
                listEl.hidden = !(listPrice > finalPrice);
            }
        };

        document.querySelectorAll('[data-category-product-card]').forEach((card) => {
            updateCategoryCardPrice(card);

            card.querySelectorAll('[data-size-option]').forEach((button) => {
                button.addEventListener('click', () => {
                    button.closest('[data-category-attribute-group]')?.querySelectorAll('[data-size-option]').forEach((item) => {
                        item.classList.toggle('is-selected', item === button);
                    });
                    updateCategoryCardPrice(card);
                });
            });
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
                        button.innerHTML = 'ADD TO CART';
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
