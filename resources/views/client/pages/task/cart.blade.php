@extends('client.layouts.app')

@php
    $cartItems = $cart->items ?? collect();
    $itemCount = (int) ($cart->total_quantity ?: $cartItems->sum('quantity'));
    $subtotal = (float) ($cart->subtotal ?: $cartItems->sum('subtotal'));
    $grandTotal = (float) ($cart->grand_total ?: $subtotal);
@endphp

@section('title', 'Shopping Cart')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/cart.css') }}">
@endpush

@section('content')
    <div class="cart-page" data-cart-page>
        <section class="cart-shell">
            <div class="cart-head">
                <div>
                    <span>Shopping cart</span>
                    <h1>Your Cart</h1>
                </div>

                <a href="{{ route('home') }}" class="cart-continue">
                    <i class="ti ti-arrow-left"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>

            <div class="cart-layout">
                <section class="cart-items-panel">
                    <div class="cart-items-top">
                        <strong>Items</strong>
                        <span data-cart-count>{{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }}</span>
                    </div>

                    <div class="cart-items" id="cart-wrapper">
                        @forelse($cartItems as $item)
                            @php
                                $productUrl = $item->product?->slug
                                    ? route('products.show', ['product' => $item->product->slug])
                                    : null;
                                $image = $item->product_image ?: $item->product?->image ?: 'images/no-image.png';
                                $lineAttributes = $item->meta['product_attribute_values'] ?? [];
                            @endphp

                            <article class="cart-item" id="item-{{ $item->id }}" data-cart-item>
                                <a class="cart-item-image" href="{{ $productUrl ?: '#' }}" aria-label="{{ $item->product_name }}">
                                    <img src="{{ asset($image) }}" alt="{{ $item->product_name }}">
                                </a>

                                <div class="cart-item-main">
                                    <div class="cart-item-title-row">
                                        <div>
                                            @if($productUrl)
                                                <a href="{{ $productUrl }}" class="cart-item-title">{{ $item->product_name }}</a>
                                            @else
                                                <strong class="cart-item-title">{{ $item->product_name }}</strong>
                                            @endif

                                            @if($item->product_sku)
                                                <span class="cart-item-sku">SKU: {{ $item->product_sku }}</span>
                                            @endif
                                        </div>

                                        <button type="button"
                                                class="cart-remove"
                                                onclick="removeItem({{ $item->id }})"
                                                aria-label="Remove {{ $item->product_name }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>

                                    @if(!empty($lineAttributes))
                                        <div class="cart-line-attrs">
                                            @foreach($lineAttributes as $row)
                                                <span>
                                                    {{ $row['attribute_name'] ?? 'Option' }}:
                                                    <strong>{{ $row['attribute_value_label'] ?? $row['value'] ?? '-' }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($item->product_variant_id && $item->variant)
                                        <div class="cart-line-attrs">
                                            <span>Variant: <strong>{{ $item->product_sku ?: $item->variant->sku }}</strong></span>
                                        </div>
                                    @endif

                                    <div class="cart-item-bottom">
                                        <div class="cart-price">
                                            @if($item->original_price && (float) $item->original_price > (float) $item->price)
                                                <del>Rs. {{ number_format((float) $item->original_price, 2) }}</del>
                                            @endif
                                            <strong>Rs. {{ number_format((float) $item->price, 2) }}</strong>
                                        </div>

                                        <div class="cart-quantity" aria-label="Quantity controls">
                                            <button type="button" onclick="decrement({{ $item->id }})" aria-label="Decrease quantity">
                                                <i class="ti ti-minus"></i>
                                            </button>
                                            <span id="qty-{{ $item->id }}">{{ $item->quantity }}</span>
                                            <button type="button" onclick="increment({{ $item->id }})" aria-label="Increase quantity">
                                                <i class="ti ti-plus"></i>
                                            </button>
                                        </div>

                                        <div class="cart-line-total">
                                            <span>Subtotal</span>
                                            <strong>Rs. <b id="subtotal-{{ $item->id }}">{{ number_format((float) $item->subtotal, 2) }}</b></strong>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="cart-empty" data-cart-empty>
                                <i class="ti ti-shopping-cart-off"></i>
                                <strong>Your cart is empty</strong>
                                <p>Add products to your cart and they will appear here.</p>
                                <a href="{{ route('home') }}">Start Shopping</a>
                            </div>
                        @endforelse
                    </div>
                </section>

                <aside class="cart-summary">
                    <h2>Order Summary</h2>

                    <dl>
                        <div>
                            <dt>Subtotal</dt>
                            <dd>Rs. <span id="cart-subtotal">{{ number_format($subtotal, 2) }}</span></dd>
                        </div>
                        <div>
                            <dt>Shipping</dt>
                            <dd>Calculated at checkout</dd>
                        </div>
                        <div>
                            <dt>Discount</dt>
                            <dd>Rs. <span id="cart-discount">{{ number_format((float) ($cart->discount_total ?? 0), 2) }}</span></dd>
                        </div>
                        <div class="cart-summary-total">
                            <dt>Total</dt>
                            <dd>Rs. <span id="cart-total">{{ number_format($grandTotal, 2) }}</span></dd>
                        </div>
                    </dl>

                    <button type="button"
                            class="cart-checkout"
                            {{ $cartItems->isEmpty() ? 'disabled' : '' }}>
                        <i class="ti ti-credit-card"></i>
                        <span>Checkout</span>
                    </button>

                    <button type="button"
                            class="cart-clear"
                            onclick="clearCart()"
                            {{ $cartItems->isEmpty() ? 'disabled' : '' }}>
                        Clear Cart
                    </button>

                    <p class="cart-summary-note">Taxes, shipping, and final discounts are confirmed during checkout.</p>
                </aside>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const money = (value) => Number(value || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function cartRequest(url, options = {}) {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    ...(options.headers || {}),
                },
            });

            return response.json();
        }

        function updateCartSummary(cart) {
            const total = cart?.grand_total ?? cart?.subtotal ?? 0;
            const subtotal = cart?.subtotal ?? total;
            const quantity = cart?.total_quantity ?? 0;

            document.getElementById('cart-total').innerText = money(total);
            document.getElementById('cart-subtotal').innerText = money(subtotal);
            document.querySelector('[data-cart-count]').innerText = `${quantity} ${Number(quantity) === 1 ? 'item' : 'items'}`;

            const navCount = document.querySelector('[data-navbar-cart-count]');
            const navTotal = document.querySelector('[data-navbar-cart-total]');
            if (navCount) {
                navCount.innerText = quantity;
            }
            if (navTotal) {
                navTotal.innerText = money(total);
            }

            const isEmpty = Number(quantity) <= 0;
            document.querySelector('.cart-checkout').disabled = isEmpty;
            document.querySelector('.cart-clear').disabled = isEmpty;
        }

        function renderEmptyCart() {
            document.getElementById('cart-wrapper').innerHTML = `
                <div class="cart-empty" data-cart-empty>
                    <i class="ti ti-shopping-cart-off"></i>
                    <strong>Your cart is empty</strong>
                    <p>Add products to your cart and they will appear here.</p>
                    <a href="{{ route('home') }}">Start Shopping</a>
                </div>
            `;
        }

        async function increment(itemId) {
            const data = await cartRequest('/cart/increment/' + itemId, { method: 'POST' });
            if (!data.status) {
                alert(data.message || 'Could not update cart');
                return;
            }

            document.getElementById('qty-' + itemId).innerText = data.item.quantity;
            document.getElementById('subtotal-' + itemId).innerText = money(data.item.subtotal);
            updateCartSummary(data.cart);
        }

        async function decrement(itemId) {
            const data = await cartRequest('/cart/decrement/' + itemId, { method: 'POST' });
            if (!data.status) {
                alert(data.message || 'Could not update cart');
                return;
            }

            if (data.item) {
                document.getElementById('qty-' + itemId).innerText = data.item.quantity;
                document.getElementById('subtotal-' + itemId).innerText = money(data.item.subtotal);
            } else {
                document.getElementById('item-' + itemId)?.remove();
            }

            updateCartSummary(data.cart);

            if (!document.querySelector('[data-cart-item]')) {
                renderEmptyCart();
            }
        }

        async function removeItem(itemId) {
            const data = await cartRequest('/cart/remove/' + itemId, { method: 'DELETE' });
            if (!data.status) {
                alert(data.message || 'Remove failed');
                return;
            }

            document.getElementById('item-' + itemId)?.remove();
            updateCartSummary(data.cart);

            if (!document.querySelector('[data-cart-item]')) {
                renderEmptyCart();
            }
        }

        async function clearCart() {
            const data = await cartRequest('/cart/clear', { method: 'DELETE' });
            if (!data.status) {
                alert(data.message || 'Clear failed');
                return;
            }

            renderEmptyCart();
            updateCartSummary(data.cart);
        }
    </script>
@endpush
