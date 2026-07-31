@php
    $productItems = $carouselProducts ?? $products ?? collect();
    $carouselLimit = $carouselLimit ?? 12;
    if ($carouselLimit !== null) {
        $productItems = $productItems->take($carouselLimit);
    }
    $carouselEyebrow = $carouselEyebrow ?? 'Pick Our';
    $carouselTitle = $carouselTitle ?? 'Top Products';
    $carouselViewAllUrl = $carouselViewAllUrl ?? route('home') . '#products';
@endphp

@if($productItems->count())
<section class="top-products-section">

        <div class="top-products-head">
            <div>
                <span>{{ $carouselEyebrow }}</span>
                <h2>{{ $carouselTitle }}</h2>
            </div>

            @if($carouselViewAllUrl)
                <a href="{{ $carouselViewAllUrl }}" class="view-all-products">
                    View All Products
                </a>
            @endif
        </div>

        <div class="top-products-grid">
            @foreach($productItems as $product)
                @php
                    $listPrice = (float) ($product->price ?? 0);
                    $finalPrice = (float) ($product->final_price ?? $product->price ?? 0);
                    $hasDiscount = $listPrice > $finalPrice && $finalPrice > 0;
                    $minOrderQty = max(1, (int) ($product->min_order_qty ?? 1));
                    $primaryImage = $product->image ?: optional($product->images->first())->image ?: 'images/no-image.png';
                    $hoverImage = optional($product->images->firstWhere('image', '!=', $primaryImage))->image
                        ?: optional($product->images->skip(1)->first())->image;
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
                        'product_id' => $product->id,
                        'type' => $product->type,
                        'base_list_price' => $listPrice,
                        'base_final_price' => $finalPrice,
                        'variants' => $variantPayload,
                    ];
                @endphp

                <article class="simple-product-card"
                         data-home-product-card
                         data-product-id="{{ $product->id }}"
                         data-min-order-qty="{{ $minOrderQty }}"
                         data-pricing='@json($pricingPayload)'>
                    <div class="simple-product-img {{ $hoverImage ? 'has-hover-image' : '' }}">
                        <img src="{{ asset($primaryImage) }}"
                             alt="{{ $product->name }}"
                             loading="lazy"
                             class="product-card-image-primary">
                        @if($hoverImage)
                            <img src="{{ asset($hoverImage) }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 class="product-card-image-hover">
                        @endif

                        <div class="product-card-actions" aria-label="{{ $product->name }} quick actions">
                            <button type="button"
                                    class="wishlist-btn"
                                    data-home-wishlist
                                    aria-label="Add {{ $product->name }} to wishlist">
                                <i class="ti ti-heart"></i>
                            </button>
                        </div>
                    </div>

                    <div class="simple-product-body">
                        <h3>
                            @if($product->slug)
                                <a href="{{ route('products.show', ['product' => $product->slug]) }}">
                                    {{ $product->name }}
                                </a>
                            @else
                                {{ $product->name }}
                            @endif
                        </h3>

                        <div class="product-price">
                            <span data-product-final-price>₹ {{ number_format($finalPrice, 2) }}</span>

                            <del data-product-list-price {{ $hasDiscount ? '' : 'hidden' }}>₹ {{ number_format($listPrice, 2) }}</del>
                        </div>

                        @if($attributeGroups->isNotEmpty())
                            <div class="product-size">
                                @foreach($attributeGroups as $attributeId => $values)
                                    @php
                                        $attribute = $values->first()->attribute;
                                    @endphp
                                    <div class="product-attribute-group" data-attribute-group data-attribute-id="{{ $attributeId }}">
                                        <strong>Select {{ $attribute->name }}:</strong>

                                        <div class="size-options">
                                            @foreach($values->sortBy(fn ($item) => $item->attributeValue->sort_order ?? $item->id) as $productAttributeValue)
                                                <button type="button"
                                                        class="{{ $loop->first ? 'active' : '' }}"
                                                        data-attribute-option
                                                        data-pav-id="{{ $productAttributeValue->id }}"
                                                        data-attribute-id="{{ $attributeId }}"
                                                        data-attribute-value-id="{{ $productAttributeValue->attribute_value_id }}">
                                                    {{ $productAttributeValue->attributeValue->value ?? $productAttributeValue->value }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="product-card-footer">
                        <small class="product-min-order">
                            <i class="ti ti-package"></i>
                            <span>Min order {{ $minOrderQty }} pcs</span>
                        </small>
                        <div class="product-card-buy-row">
                            <div class="product-qty-stepper" data-product-qty-stepper>
                                <button type="button" data-product-qty-decrease aria-label="Decrease {{ $product->name }} quantity">
                                    <i class="ti ti-minus"></i>
                                </button>
                                <input type="number"
                                       inputmode="numeric"
                                       min="{{ $minOrderQty }}"
                                       value="{{ $minOrderQty }}"
                                       data-product-card-quantity
                                       aria-label="{{ $product->name }} quantity">
                                <button type="button" data-product-qty-increase aria-label="Increase {{ $product->name }} quantity">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>

                            <button type="button" class="add-cart-btn" data-add-home-cart aria-label="Add {{ $product->name }} to cart">
                                <i class="ti ti-shopping-bag-plus"></i>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

</section>

@endif
