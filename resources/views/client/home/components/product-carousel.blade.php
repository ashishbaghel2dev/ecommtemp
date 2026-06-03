@php
    $productCarouselItems = ($carouselProducts ?? $products ?? collect())->take(12);
    $carouselKey = $carouselKey ?? 'products-' . uniqid();
    $carouselEyebrow = $carouselEyebrow ?? 'Featured products';
    $carouselTitle = $carouselTitle ?? 'Products You May Like';
    $carouselPromoImage = $carouselPromoImage ?? optional($productCarouselItems->first())->image ?? 'images/no-image.png';
@endphp

@if($productCarouselItems->count())
    <section class="product-carousel-section container-fluid" aria-label="{{ $carouselTitle }}" data-product-carousel="{{ $carouselKey }}">
        <div class="product-carousel-shell">
            <aside class="product-carousel-promo">
                <img src="{{ asset($carouselPromoImage) }}"
                     alt="{{ $carouselTitle }}"
                     loading="lazy">

                <div class="product-carousel-promo-content">
                    <span>{{ $carouselEyebrow }}</span>
                    <strong>{{ $carouselTitle }}</strong>
                </div>
            </aside>

            <div class="product-carousel-products">
                <div class="product-carousel-head">
                    <div>
                        <span>{{ $carouselEyebrow }}</span>
                        <h2>{{ $carouselTitle }}</h2>
                    </div>

                    <div class="product-carousel-actions" aria-label="Product carousel controls">
                        <button type="button" class="product-carousel-control" data-product-carousel-prev aria-label="Previous {{ $carouselTitle }}">
                            <i class="ti ti-chevron-left"></i>
                        </button>
                        <button type="button" class="product-carousel-control" data-product-carousel-next aria-label="Next {{ $carouselTitle }}">
                            <i class="ti ti-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="product-carousel-track" data-product-carousel-track>
                    @foreach($productCarouselItems as $product)
                        @php
                            $listPrice = (float) ($product->price ?? 0);
                            $finalPrice = (float) $product->final_price;
                            $hasDiscount = $listPrice > $finalPrice && $finalPrice > 0;
                            $discountPercent = $hasDiscount ? round((($listPrice - $finalPrice) / $listPrice) * 100) : null;
                        @endphp

                        <article class="home-product-card">
                            <div class="home-product-media">
                                @if($product->category)
                                    <span class="home-product-category">{{ $product->category->name }}</span>
                                @endif

                                @if($hasDiscount)
                                    <span class="home-product-discount">{{ $discountPercent }}% OFF</span>
                                @endif

                                <img src="{{ asset($product->image ?? 'images/no-image.png') }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy">
                            </div>

                            <div class="home-product-content">
                                <h3>
                                    @if($product->slug)
                                        <a href="{{ route('products.show', ['product' => $product->slug]) }}">{{ $product->name }}</a>
                                    @else
                                        {{ $product->name }}
                                    @endif
                                </h3>

                                <p>{{ \Illuminate\Support\Str::limit($product->short_description ?? $product->description ?? 'Quality product for your setup.', 72) }}</p>

                                <div class="home-product-meta">
                                    <span class="{{ $product->in_stock ? 'is-available' : 'is-unavailable' }}">
                                        {{ $product->in_stock ? 'In stock' : 'Out of stock' }}
                                    </span>

                                    @if(!is_null($product->stock))
                                        <small>{{ $product->stock }} left</small>
                                    @endif
                                </div>

                                <div class="home-product-price-row">
                                    <div>
                                        @if($hasDiscount)
                                            <del>Rs. {{ number_format($listPrice, 2) }}</del>
                                        @endif
                                        <strong>Rs. {{ number_format($finalPrice, 2) }}</strong>
                                    </div>

                                    @if($product->slug)
                                        <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="home-product-cart" aria-label="View {{ $product->name }} details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <script>
        (() => {
            const section = document.querySelector('[data-product-carousel="{{ $carouselKey }}"]');
            const track = section?.querySelector('[data-product-carousel-track]');
            const previousButton = section?.querySelector('[data-product-carousel-prev]');
            const nextButton = section?.querySelector('[data-product-carousel-next]');

            if (!track || !previousButton || !nextButton) {
                return;
            }

            const scrollByCard = (direction) => {
                const card = track.querySelector('.home-product-card');
                const cardWidth = card ? card.getBoundingClientRect().width : 280;

                track.scrollBy({
                    left: direction * (cardWidth + 18),
                    behavior: 'smooth',
                });
            };

            previousButton.addEventListener('click', () => scrollByCard(-1));
            nextButton.addEventListener('click', () => scrollByCard(1));
        })();
    </script>
@endif
