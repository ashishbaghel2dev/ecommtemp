@php
    $productCarouselItems = ($products ?? collect())->take(12);
@endphp

@if($productCarouselItems->count())
    <section class="product-carousel-section container-fluid" aria-label="Featured products">
        <div class="product-carousel-head">
            <div>
                <span>Featured products</span>
                <h2>Products You May Like</h2>
            </div>

            <div class="product-carousel-actions" aria-label="Product carousel controls">
                <button type="button" class="product-carousel-control" data-product-carousel-prev aria-label="Previous products">
                    <i class="ti ti-chevron-left"></i>
                </button>
                <button type="button" class="product-carousel-control" data-product-carousel-next aria-label="Next products">
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

                            <button type="button" class="home-product-cart" onclick="handleAddToCart({{ $product->id }})" aria-label="Add {{ $product->name }} to cart">
                                <i class="ti ti-shopping-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <script>
        (() => {
            const track = document.querySelector('[data-product-carousel-track]');
            const previousButton = document.querySelector('[data-product-carousel-prev]');
            const nextButton = document.querySelector('[data-product-carousel-next]');

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

        window.handleAddToCart = window.handleAddToCart || async function (productId) {
            const response = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                    product_variant_id: null,
                    selected_product_attribute_value_ids: [],
                }),
            });

            const data = await response.json();
            if (!response.ok || !data.status) {
                alert(data.message || 'Could not add to cart');
                return;
            }

            const navCount = document.querySelector('[data-navbar-cart-count]');
            const navTotal = document.querySelector('[data-navbar-cart-total]');
            if (navCount) {
                navCount.textContent = data.cart?.total_quantity ?? 0;
            }
            if (navTotal) {
                navTotal.textContent = Number(data.cart?.grand_total ?? data.cart?.subtotal ?? 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }
            alert(data.message || 'Added to cart');
        };
    </script>
@endif
