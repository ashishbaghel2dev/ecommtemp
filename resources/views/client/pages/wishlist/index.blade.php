@extends('client.layouts.app')

@section('title', 'Wishlist')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/wishlist.css') }}">
@endpush

@section('content')
    <div class="wishlist-page">
        <section class="wishlist-shell">
            <div class="wishlist-head">
                <div>
                   
                    <h1>Your Wishlist</h1>
                </div>

                <a href="{{ route('home') }}" class="wishlist-continue">
                    <i class="ti ti-arrow-left"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>

            <div class="wishlist-grid" id="wishlist-grid">
                @forelse($products as $product)
                    <article class="wishlist-card" id="wishlist-product-{{ $product->id }}">
                        <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="wishlist-card-media">
                            <img src="{{ asset($product->image ?? 'images/no-image.png') }}" alt="{{ $product->name }}">
                        </a>

                        <div class="wishlist-card-body">
                          
                            <a href="{{ route('products.show', ['product' => $product->slug]) }}">
                                <h3>{{ $product->name }}</h3>
                        </a>


                        <p>
                            Details: {{ \Illuminate\Support\Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 42) }}
                        </p>

                        


                            <div class="wishlist-card-foot">
                                
                                <strong>Rs. {{ number_format((float) $product->final_price, 2) }}</strong>

                                <div>
                                    <button type="button"
                                            class="wishlist-cart-btn"
                                            onclick="addWishlistProductToCart({{ $product->id }})"
                                            aria-label="Add {{ $product->name }} to cart">
                                        <i class="ti ti-shopping-cart-plus"></i>
                                    </button>
                                    <button type="button"
                                            class="wishlist-remove-btn"
                                            onclick="removeWishlistProduct({{ $product->id }})"
                                            aria-label="Remove {{ $product->name }} from wishlist">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="wishlist-empty" data-wishlist-empty>
                        <i class="ti ti-heart-off"></i>
                        <strong>Your wishlist is empty</strong>
                        <p>Save products you like and they will appear here.</p>
                        <a href="{{ route('home') }}">Start Shopping</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const wishlistCsrf = document.querySelector('meta[name="csrf-token"]').content;

        const formatNavbarMoney = (value) => Number(value || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        function updateWishlistNavbar(count) {
            if (window.updateNavbarWishlistCount) {
                window.updateNavbarWishlistCount(count);
                return;
            }

            document.querySelectorAll('[data-navbar-wishlist-count]').forEach((badge) => {
                badge.innerText = count;
            });
        }

        function updateCartNavbar(cart) {
            if (window.updateNavbarCartSummary) {
                window.updateNavbarCartSummary(cart);
                return;
            }

            document.querySelectorAll('[data-navbar-cart-count]').forEach((count) => {
                count.innerText = cart?.total_quantity ?? 0;
            });
            document.querySelectorAll('[data-navbar-cart-total]').forEach((total) => {
                total.innerText = formatNavbarMoney(cart?.grand_total ?? cart?.subtotal ?? 0);
            });
        }

        function renderEmptyWishlist() {
            document.getElementById('wishlist-grid').innerHTML = `
                <div class="wishlist-empty" data-wishlist-empty>
                    <i class="ti ti-heart-off"></i>
                    <strong>Your wishlist is empty</strong>
                    <p>Save products you like and they will appear here.</p>
                    <a href="{{ route('home') }}">Start Shopping</a>
                </div>
            `;
        }

        async function removeWishlistProduct(productId) {
            const response = await fetch(`/wishlist/${productId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': wishlistCsrf,
                },
            });

            const data = await response.json();
            updateWishlistNavbar(data.count ?? 0);
            document.getElementById('wishlist-product-' + productId)?.remove();

            if (!document.querySelector('.wishlist-card')) {
                renderEmptyWishlist();
            }
        }

        async function addWishlistProductToCart(productId) {
            const response = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': wishlistCsrf,
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
                alert(data.message || 'Could not add product to cart');
                return;
            }

            updateCartNavbar(data.cart);
            alert(data.message || 'Product added to cart');
        }
    </script>
@endpush
