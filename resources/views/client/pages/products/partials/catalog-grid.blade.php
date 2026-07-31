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
                    ->map(function ($variant) use ($listPrice, $finalPrice) {
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
                    <span class="catalog-product-category">{{ $product->category->name ?? 'Herbal Tea' }}</span>
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
                                @php($attribute = $values->first()->attribute)
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
                    <button type="button" data-add-to-cart="{{ $product->id }}">ADD TO CART</button>
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
        <a href="{{ $resetUrl }}">Reset filters</a>
    </div>
@endif
