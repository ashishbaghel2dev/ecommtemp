@extends('client.layouts.app')

@php
$listPrice = (float) ($product->price ?? 0);
$finalPrice = (float) $product->final_price;
$hasDiscount = $listPrice > $finalPrice && $finalPrice > 0;
$discountPercent = $hasDiscount ? round((($listPrice - $finalPrice) / $listPrice) * 100) : null;
$galleryImages = collect([$product->image])
->merge($product->images->pluck('image'))
->filter()
->unique()
->values();
$mainImage = $galleryImages->first() ?? 'images/no-image.png';
$saleIsLive = $product->sale_price && $product->sale_start && $product->sale_end && now()->between($product->sale_start,
$product->sale_end);
$pricingPayload = [
'baseFinal' => $finalPrice,
'baseList' => $listPrice,
'variants' => $product->variants->map(function ($variant) {
return [
'id' => $variant->id,
'sku' => $variant->sku,
'price' => (float) $variant->final_price,
'stock' => (int) ($variant->stock ?? 0),
'in_stock' => (bool) $variant->in_stock,
'image' => $variant->image,
'attributes' => is_array($variant->attributes) ? $variant->attributes : [],
];
})->values()->all(),
];
@endphp

@section('title', $product->meta_title ?: $product->name)
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) ($product->short_description ?: $product->description)), 155, ''))
@section('canonical', route('products.show', $product->slug))
@section('seo_image', asset($mainImage))

@push('head')
@php
    $ratingCount = $visibleReviews->count();
    $ratingValue = $ratingCount ? round($visibleReviews->avg('rating'), 1) : null;
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => strip_tags($product->short_description ?: $product->description ?: ''),
        'sku' => $product->sku ?: (string) $product->id,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Go Sowa',
        ],
        'image' => $galleryImages->map(fn ($image) => asset($image))->values()->all(),
        'category' => $product->category?->name,
        'offers' => [
            '@type' => 'Offer',
            'url' => route('products.show', $product->slug),
            'priceCurrency' => 'INR',
            'price' => number_format($finalPrice, 2, '.', ''),
            'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ];
    if ($ratingValue) {
        $productSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $ratingValue,
            'reviewCount' => $ratingCount,
        ];
    }
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'How do I use '.$product->name.'?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Use one serving in hot water, steep for a few minutes, and enjoy as part of your daily wellness routine.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'What are the benefits of '.$product->name.'?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($product->short_description ?: 'This herbal tea is crafted to support mindful everyday wellness.'),
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/client/pages/product-show.css') }}">
@endpush

@section('content')
<div class="product-detail-page" data-product-detail data-pricing='@json($pricingPayload)'>
    <section class="product-detail-shell">
        <div class="product-detail-grid">
            <section class="product-gallery" aria-label="{{ $product->name }} gallery">
                <div class="product-gallery-main">
                    @if($product->labels->isNotEmpty())
                    <div class="product-label-stack">
                        @foreach($product->labels as $label)
                        <span class="product-label" style="--label-color: {{ $label->color ?: '#111827' }}">
                            {{ $label->name }}
                        </span>
                        @endforeach
                    </div>
                    @endif



                    <button type="button" class="product-image-zoom-trigger" data-image-zoom-open
                        aria-label="Zoom {{ $product->name }} image">
                        <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" data-main-product-image>
                        <span><i class="ti ti-zoom-in"></i> Click to zoom</span>
                    </button>
                </div>

                @if($galleryImages->count() > 1)
                <div class="product-gallery-thumbs" aria-label="Product images">
                    @foreach($galleryImages as $image)
                    <button type="button" class="product-thumb {{ $loop->first ? 'is-active' : '' }}"
                        data-gallery-image="{{ asset($image) }}" aria-label="View image {{ $loop->iteration }}">
                        <img src="{{ asset($image) }}" alt="">
                    </button>
                    @endforeach
                </div>
                @endif
            </section>

            <section class="product-summary">
                <div class="product-summary-top">
                    @if($product->category)
                    <span class="product-category-pill">{{ $product->category->name }}</span>
                    @endif

                    <span class="product-status {{ $product->in_stock ? 'is-in' : 'is-out' }}">
                        {{ $product->in_stock ? 'In stock' : 'Out of stock' }}
                    </span>
                </div>

                <h1>{{ $product->name }}</h1>

                <div class="product-submeta">
                    @if($product->sku)
                    <span>SKU: <strong data-product-sku>{{ $product->sku }}</strong></span>
                    @endif

                </div>

                @if($product->short_description)
                <div class="product-short-description">{!! $product->short_description !!}</div>
                @endif

                <div class="product-price-block">
                    <div>
                        @if($hasDiscount)
                        <del data-product-list-price>Rs. {{ number_format($listPrice, 2) }}</del>
                        @endif
                        <strong data-product-final-price>Rs. {{ number_format($finalPrice, 2) }}</strong>
                    </div>

                    @if($saleIsLive)
                    <span>Sale ends {{ $product->sale_end->format('M d, Y') }}</span>
                    @elseif($product->discount_price)
                    <span>Discount</span>
                    @endif
                </div>

                @if($attributeGroups->isNotEmpty())
                <div class="product-options" aria-label="Product options">
                    @foreach($attributeGroups as $group)
                    @php
                    $attribute = $group['attribute'];
                    $values = $group['values'];
                    @endphp

                    <fieldset class="product-option-group" data-attribute-id="{{ $attribute->id ?? '' }}">
                        <legend>
                            {{ $attribute->name ?? 'Option' }}
                            @if($attribute?->is_required)
                            <span>Required</span>
                            @endif
                        </legend>

                        <div class="product-option-values">
                            @foreach($values as $productAttributeValue)
                            @php
                            $attributeValue = $productAttributeValue->attributeValue;
                            $displayValue = $attributeValue->value ?? $productAttributeValue->value ?? 'Option';
                            $isColor = ($attribute?->type === 'color') || filled($attributeValue?->color_code);
                            @endphp

                            <label class="product-option {{ $isColor ? 'has-swatch' : '' }}">
                                <input type="radio" name="attribute_{{ $attribute->id }}"
                                    value="{{ $productAttributeValue->id }}"
                                    data-attribute-id="{{ $productAttributeValue->attribute_id }}"
                                    data-attribute-value-id="{{ $productAttributeValue->attribute_value_id }}" {{
                                    $values->count() === 1 ? 'checked' : '' }}>
                                @if($isColor)
                                <span class="product-color-swatch"
                                    style="--swatch: {{ $attributeValue->color_code ?: '#d1d5db' }}"></span>
                                @elseif($attributeValue?->image)
                                <img src="{{ asset($attributeValue->image) }}" alt="">
                                @endif
                                <span>{{ $displayValue }}</span>
                            </label>
                            @endforeach
                        </div>
                    </fieldset>
                    @endforeach
                </div>
                @elseif($product->type === 'configurable' && $product->variants->isNotEmpty())
                <label class="product-variant-select-wrap">
                    <span>Variant</span>
                    <select data-variant-select>
                        @foreach($product->variants as $variant)
                        @php
                        $variantName = collect($variant->attributes ?? [])
                        ->map(fn ($valueId, $attributeId) => ($variantAttributes[$attributeId] ?? 'Option') . ': ' .
                        ($variantValues[$valueId] ?? 'Value'))
                        ->implode(', ');
                        @endphp
                        <option value="{{ $variant->id }}">
                            {{ $variantName ?: $variant->sku }} - Rs. {{ number_format((float) $variant->final_price, 2)
                            }}
                        </option>
                        @endforeach
                    </select>
                </label>
                @endif

                <div class="product-purchase-row">
                    <label class="product-quantity">
                        <span>Qty</span>
                        <div class="product-quantity-stepper" data-quantity-stepper>
                            <button type="button" data-quantity-decrease aria-label="Decrease quantity">
                                <i class="ti ti-minus"></i>
                            </button>
                            <input type="number" min="1" value="1" data-product-quantity aria-label="Product quantity">
                            <button type="button" data-quantity-increase aria-label="Increase quantity">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>
                    </label>

                    <div class="product-action-buttons">
                        <button type="button" class="product-add-cart" data-add-product-to-cart {{ $product->in_stock ? '' :
                            'disabled' }}>
                            <i class="ti ti-shopping-cart-plus"></i>
                            <span>Add to Cart</span>
                        </button>

                        <button type="button" class="product-buy-now" data-buy-now {{ $product->in_stock ? '' :
                            'disabled' }}>
                            <i class="ti ti-bolt"></i>
                            <span>Buy Now</span>
                        </button>

                        <button type="button" class="product-wishlist" data-product="{{ $product->id }}"
                            data-product-wishlist aria-label="Toggle wishlist">
                            <i class="ti ti-heart"></i>
                        </button>
                    </div>

                </div>
   <div class="product-review-box mb-4">
                <div class="product-review-box2">





                    <div>
                        <h5 class="mb-0">Customer Reviews</h5>
                        <div class="product-rating my-3">
                            @for ($i = 1; $i <= 5; $i++) <span>
                                <i class="ti ti-star"></i>
                                </span>
                                @endfor
                        </div>
                    </div>

                    <div>
                        <h5 class="mb-2 fw-bold">Share on Social Media</h5>
                        <div class="social-share">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}"
                                target="_blank" class="facebook" title="Share on Facebook">
                                <i class="ti ti-brand-facebook"></i>
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}&text={{ urlencode($product->name) }}"
                                target="_blank" class="twitter" title="Share on X">
                                <i class="ti ti-brand-x"></i>
                            </a>

                            <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' ' . Request::fullUrl()) }}"
                                target="_blank" class="whatsapp" title="Share on WhatsApp">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>

                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}&title={{ urlencode($product->name) }}"
                                target="_blank" class="linkedin" title="Share on LinkedIn">
                                <i class="ti ti-brand-linkedin"></i>
                            </a>

                            <a href="mailto:?subject={{ urlencode($product->name) }}&body={{ urlencode(Request::fullUrl()) }}"
                                class="email" title="Share via Email">
                                <i class="ti ti-mail"></i>
                            </a>
                        </div>
                    </div>
                </div>


            </div>

            </section>
        </div>
    </section>

    <section class="product-info-band">
        <div class="product-info-grid">
            <article>
                <h2>Description</h2>
                <div class="product-description">
                    {!! $product->description ?: $product->short_description ?: 'Product information will be updated
                    soon.' !!}
                </div>
            </article>

            <article>
                <h2>Ingredients</h2>
                <div class="product-description">
                    {!! $product->short_description ?: 'A carefully balanced herbal tea blend made for natural daily wellness.' !!}
                </div>
            </article>

            <article>
                <h2>Benefits</h2>
                <div class="product-description">
                    <ul class="product-education-list">
                        <li>Supports a calm, mindful tea routine.</li>
                        <li>Crafted for everyday wellness and refreshment.</li>
                        <li>Made for easy gifting, pantry use, and repeat brewing.</li>
                    </ul>
                </div>
            </article>

            <article>
                <h2>How to Use</h2>
                <div class="product-description">
                    <ol class="product-education-list">
                        <li>Add one serving to a cup.</li>
                        <li>Pour hot water and steep for 3 to 5 minutes.</li>
                        <li>Enjoy warm. Add honey or lemon if preferred.</li>
                    </ol>
                </div>
            </article>

        </div>
    </section>

    <section class="product-review-band">
        <div class="product-review-grid">
            <div>
                <div class="product-section-head">
                    <span>Customer feedback</span>
                    <h2>Reviews for {{ $product->name }}</h2>
                </div>

                <div class="product-review-list">
                    @forelse($visibleReviews as $review)
                    <article class="product-review-card">
                        <div class="product-review-card-head">
                            <div>
                                <div class="product-review-title-line">
                                    <strong>{{ $review->title ?: 'Customer review' }}</strong>
                                </div>
                                <span>{{ $review->user->name ?? 'Customer' }} / {{ $review->created_at?->format('M d,
                                    Y') }}</span>
                            </div>

                            <div class="product-review-stars" aria-label="{{ $review->rating }} out of 5 stars">
                                @for($i = 1; $i <= 5; $i++) <i
                                    class="ti {{ $i <= $review->rating ? 'ti-star-filled' : 'ti-star' }}"></i>
                                    @endfor
                            </div>
                        </div>

                        @if($review->comment)
                        <p>{{ $review->comment }}</p>
                        @endif

                        @if($review->images->isNotEmpty())
                        <div class="product-review-images">
                            @foreach($review->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                alt="{{ $image->alt_text ?: 'Review image' }}">
                            @endforeach
                        </div>
                        @endif

                        @if($review->admin_reply)
                        <div class="product-review-reply">
                            <strong>Seller reply</strong>
                            <p>{{ $review->admin_reply }}</p>
                        </div>
                        @endif

                        <button type="button" class="product-review-helpful" data-review-helpful="{{ $review->id }}">
                            <i class="ti ti-thumb-up"></i>
                            <span>Helpful</span>
                            <strong data-review-helpful-count="{{ $review->id }}">{{ (int) $review->helpful_votes
                                }}</strong>
                        </button>
                    </article>
                    @empty
                    <div class="product-review-empty">
                        <strong>No reviews yet</strong>
                        <p>Be the first customer to review this product.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <aside class="product-review-form-panel">
                <h2>Add Review</h2>

                @if(session('success'))
                <div class="product-form-alert is-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                <div class="product-form-alert is-error">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                @auth
                <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data"
                    class="product-review-form">
                    @csrf

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <label>
                        <span>Review Title</span>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Short summary">
                    </label>

                    <label>
                        <span>Rating</span>
                        <select name="rating" required>
                            <option value="">Select rating</option>
                            @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ (int) old('rating')===$i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                            @endfor
                        </select>
                    </label>

                    <label>
                        <span>Comment</span>
                        <textarea name="comment" rows="5"
                            placeholder="Share your experience with this product">{{ old('comment') }}</textarea>
                    </label>

                    <label>
                        <span>Images</span>
                        <input type="file" name="images[]" multiple accept="image/*">
                        <small>JPG or PNG, up to 2MB each.</small>
                    </label>

                    <button type="submit">
                        <i class="ti ti-message-star"></i>
                        <span>Submit Review</span>
                    </button>
                </form>
                @else
                <div class="product-login-review">
                    <p>Please login to add your review for this product.</p>
                    <a href="{{ route('login') }}">Login to Review</a>
                </div>
                @endauth
            </aside>
        </div>
    </section>

    @if($relatedProducts->isNotEmpty())
    <section class="product-related-band">
        <div class="product-section-head">
            <span>More from {{ $product->category->name ?? 'this category' }}</span>
            <h2>Related Products</h2>
        </div>

        <div class="product-related-grid">
            @foreach($relatedProducts as $relatedProduct)
            @php
                $relatedPrimaryImage = $relatedProduct->image ?: optional($relatedProduct->images->first())->image ?: 'images/no-image.png';
                $relatedHoverImage = optional($relatedProduct->images->firstWhere('image', '!=', $relatedPrimaryImage))->image
                    ?: optional($relatedProduct->images->skip(1)->first())->image;
            @endphp
            <a href="{{ route('products.show', ['product' => $relatedProduct->slug]) }}" class="related-product">
                <span class="related-product-media {{ $relatedHoverImage ? 'has-hover-image' : '' }}">
                    <img src="{{ asset($relatedPrimaryImage) }}"
                        alt="{{ $relatedProduct->name }}" class="product-card-image-primary">
                    @if($relatedHoverImage)
                        <img src="{{ asset($relatedHoverImage) }}"
                            alt="{{ $relatedProduct->name }}" class="product-card-image-hover">
                    @endif
                </span>
                <span>{{ $relatedProduct->category->name ?? 'Product' }}</span>
                <strong>{{ $relatedProduct->name }}</strong>
                <small>Rs. {{ number_format((float) $relatedProduct->final_price, 2) }}</small>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    @if($recentlyViewedProducts->isNotEmpty())
    <section class="product-related-band">
        <div class="product-section-head">
            <span>Your browsing</span>
            <h2>Recently Viewed Products</h2>
        </div>

        <div class="product-related-grid">
            @foreach($recentlyViewedProducts as $recentProduct)
            @php
                $recentPrimaryImage = $recentProduct->image ?: optional($recentProduct->images->first())->image ?: 'images/no-image.png';
                $recentHoverImage = optional($recentProduct->images->firstWhere('image', '!=', $recentPrimaryImage))->image
                    ?: optional($recentProduct->images->skip(1)->first())->image;
            @endphp
            <a href="{{ route('products.show', ['product' => $recentProduct->slug]) }}" class="related-product">
                <span class="related-product-media {{ $recentHoverImage ? 'has-hover-image' : '' }}">
                    <img src="{{ asset($recentPrimaryImage) }}" alt="{{ $recentProduct->name }}" class="product-card-image-primary">
                    @if($recentHoverImage)
                        <img src="{{ asset($recentHoverImage) }}" alt="{{ $recentProduct->name }}" class="product-card-image-hover">
                    @endif
                </span>
                <span>{{ $recentProduct->category->name ?? 'Product' }}</span>
                <strong>{{ $recentProduct->name }}</strong>
                <small>Rs. {{ number_format((float) $recentProduct->final_price, 2) }}</small>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <div class="product-zoom-modal" data-product-zoom-modal hidden>
        <button type="button" class="product-zoom-close" data-image-zoom-close aria-label="Close image zoom">
            <i class="ti ti-x"></i>
        </button>
        <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" data-zoom-image>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const page = document.querySelector('[data-product-detail]');
        if (!page) {
            return;
        }

        const pricing = JSON.parse(page.dataset.pricing || '{"baseFinal":0,"baseList":0,"variants":[]}');
        const finalPriceEl = page.querySelector('[data-product-final-price]');
        const listPriceEl = page.querySelector('[data-product-list-price]');
        const skuEl = page.querySelector('[data-product-sku]');
        const stockEl = page.querySelector('[data-product-stock]');
        const mainImage = page.querySelector('[data-main-product-image]');
        const zoomModal = page.querySelector('[data-product-zoom-modal]');
        const zoomImage = page.querySelector('[data-zoom-image]');

        const formatPrice = (amount) => 'Rs. ' + Number(amount || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const formatNavbarAmount = (amount) => Number(amount || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const updateNavbarCart = (cart) => {
            if (window.updateNavbarCartSummary) {
                window.updateNavbarCartSummary(cart);
                return;
            }

            document.querySelectorAll('[data-navbar-cart-count]').forEach((count) => {
                count.textContent = cart?.total_quantity ?? 0;
            });
            document.querySelectorAll('[data-navbar-cart-total]').forEach((total) => {
                total.textContent = formatNavbarAmount(cart?.grand_total ?? cart?.subtotal ?? 0);
            });
        };

        const updateNavbarWishlist = (countValue) => {
            if (window.updateNavbarWishlistCount) {
                window.updateNavbarWishlistCount(countValue);
                return;
            }

            document.querySelectorAll('[data-navbar-wishlist-count]').forEach((count) => {
                count.textContent = countValue ?? 0;
            });
        };

        const normalizeAttributes = (attributes) => {
            const normalized = {};
            Object.keys(attributes || {}).sort((a, b) => Number(a) - Number(b)).forEach((key) => {
                normalized[Number(key)] = Number(attributes[key]);
            });
            return JSON.stringify(normalized);
        };

        const selectedAttributes = () => {
            const selected = {};
            page.querySelectorAll('.product-option input:checked').forEach((input) => {
                selected[input.dataset.attributeId] = input.dataset.attributeValueId;
            });
            return selected;
        };

        const selectedProductAttributeValueIds = () => {
            return Array.from(page.querySelectorAll('.product-option input:checked')).map((input) => Number(input.value));
        };

        const findSelectedVariant = () => {
            const variantSelect = page.querySelector('[data-variant-select]');
            if (variantSelect && variantSelect.value) {
                return pricing.variants.find((variant) => Number(variant.id) === Number(variantSelect.value));
            }

            const groups = page.querySelectorAll('.product-option-group');
            const attributes = selectedAttributes();
            if (!groups.length || Object.keys(attributes).length !== groups.length) {
                return null;
            }

            const selectedSignature = normalizeAttributes(attributes);
            return pricing.variants.find((variant) => normalizeAttributes(variant.attributes) === selectedSignature);
        };

        const updateDisplayedVariant = () => {
            const variant = findSelectedVariant();
            if (!variant) {
                if (finalPriceEl) {
                    finalPriceEl.textContent = formatPrice(pricing.baseFinal);
                }
                return;
            }

            if (finalPriceEl) {
                finalPriceEl.textContent = formatPrice(variant.price);
            }
            if (listPriceEl) {
                listPriceEl.textContent = formatPrice(Math.max(pricing.baseList, variant.price));
            }
            if (skuEl && variant.sku) {
                skuEl.textContent = variant.sku;
            }
            if (stockEl) {
                stockEl.textContent = variant.in_stock ? `${variant.stock} available` : 'Out of stock';
            }
            if (mainImage && variant.image) {
                mainImage.src = '/' + variant.image.replace(/^\/+/, '');
                if (zoomImage) {
                    zoomImage.src = mainImage.src;
                }
            }
        };

        page.querySelectorAll('.product-thumb').forEach((button) => {
            button.addEventListener('click', () => {
                page.querySelectorAll('.product-thumb').forEach((thumb) => thumb.classList.remove('is-active'));
                button.classList.add('is-active');
                if (mainImage) {
                    mainImage.src = button.dataset.galleryImage;
                    if (zoomImage) {
                        zoomImage.src = button.dataset.galleryImage;
                    }
                }
            });
        });

        page.querySelector('[data-image-zoom-open]')?.addEventListener('click', () => {
            if (!zoomModal || !zoomImage || !mainImage) {
                return;
            }
            zoomImage.src = mainImage.src;
            zoomModal.hidden = false;
            document.body.classList.add('product-zoom-open');
        });

        const closeZoom = () => {
            if (!zoomModal) {
                return;
            }
            zoomModal.hidden = true;
            document.body.classList.remove('product-zoom-open');
        };

        page.querySelector('[data-image-zoom-close]')?.addEventListener('click', closeZoom);
        zoomModal?.addEventListener('click', (event) => {
            if (event.target === zoomModal) {
                closeZoom();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && zoomModal && !zoomModal.hidden) {
                closeZoom();
            }
        });

        page.addEventListener('change', (event) => {
            if (event.target.matches('.product-option input, [data-variant-select]')) {
                updateDisplayedVariant();
            }
        });

        const quantityInput = page.querySelector('[data-product-quantity]');
        const normalizeQuantity = () => {
            if (!quantityInput) {
                return 1;
            }

            const quantity = Math.max(1, Number.parseInt(quantityInput.value || '1', 10) || 1);
            quantityInput.value = quantity;
            return quantity;
        };

        page.querySelector('[data-quantity-decrease]')?.addEventListener('click', () => {
            if (!quantityInput) {
                return;
            }

            quantityInput.value = Math.max(1, normalizeQuantity() - 1);
        });

        page.querySelector('[data-quantity-increase]')?.addEventListener('click', () => {
            if (!quantityInput) {
                return;
            }

            quantityInput.value = normalizeQuantity() + 1;
        });

        quantityInput?.addEventListener('change', normalizeQuantity);

        const addCurrentProductToCart = async () => {
            const variant = findSelectedVariant();
            const quantity = normalizeQuantity();

            const response = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    quantity: quantity > 0 ? quantity : 1,
                    product_variant_id: variant ? variant.id : null,
                    selected_product_attribute_value_ids: selectedProductAttributeValueIds(),
                }),
            });

            const data = await response.json();
            if (response.ok && data.status) {
                updateNavbarCart(data.cart);
            }
            return { response, data };
        };

        page.querySelector('[data-add-product-to-cart]')?.addEventListener('click', async () => {
            const { response, data } = await addCurrentProductToCart();
            if (response.ok && data.status !== false) {
                return;
            } else {
                if (!window.AppToast) alert(data.message || 'Could not add product to cart');
            }
        });

        page.querySelector('[data-buy-now]')?.addEventListener('click', async () => {
            const { response, data } = await addCurrentProductToCart();

            if (!response.ok || !data.status) {
                alert(data.message || 'Could not add product to cart');
                return;
            }

            window.location.href = '{{ route('checkout.index') }}';
        });

        page.querySelector('[data-product-wishlist]')?.addEventListener('click', async (event) => {
            const variant = findSelectedVariant();
            const response = await fetch(`/wishlist/${event.currentTarget.dataset.product}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    product_variant_id: variant ? variant.id : null,
                    selected_product_attribute_value_ids: selectedProductAttributeValueIds(),
                }),
            });

            const data = await response.json();
            updateNavbarWishlist(data.count);
            if (response.ok && data.success !== false) {
                window.AppToast?.success(data.message || (data.added ? 'Added to wishlist' : 'Removed from wishlist'));
                event.currentTarget.classList.toggle('is-active', Boolean(data.added));
            } else {
                window.AppToast?.error(data.message || 'Could not update wishlist');
            }
        });

    page.querySelectorAll('[data-review-helpful]').forEach((button) => {
        button.addEventListener('click', async () => {
            const reviewId = button.dataset.reviewHelpful;
            const response = await fetch(`/reviews/${reviewId}/helpful`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            const data = await response.json();
            if (!response.ok || !data.status) {
                alert(data.message || 'Could not mark this review as helpful');
                return;
            }

            const count = page.querySelector(`[data-review-helpful-count="${reviewId}"]`);
            if (count) {
                count.textContent = data.helpful_votes;
            }
            button.classList.toggle('is-marked', Boolean(data.marked));
        });
    });

    updateDisplayedVariant();
        }) ();
</script>
@endpush
