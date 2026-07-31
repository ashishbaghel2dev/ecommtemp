@extends('client.layouts.app')

@php
    $productTotal = (float) $cart->items->sum(fn ($item) => (float) ($item->original_price ?: $item->price) * (int) $item->quantity);
    $productDiscount = (float) $cart->items->sum('discount_amount');
@endphp

@section('title', 'Review Order')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/checkout.css') }}">
@endpush

@section('content')
<section class="checkout-page">
    <header class="checkout-head compact">
        <div>
            <h1>Review Order</h1>
            <p>Check delivery, products, coupon, and payment before placing the order.</p>
        </div>
    </header>

    <div class="checkout-progress">
        @foreach(['Cart', 'Account', 'Address', 'Review', 'Success'] as $step)
            @php
                $state = in_array($step, ['Cart', 'Account', 'Address']) ? 'is-complete' : ($step === 'Review' ? 'is-current' : 'is-pending');
            @endphp
            <span class="{{ $state }} step-{{ strtolower($step) }}">{{ $step }}</span>
        @endforeach
    </div>

    @if(session('error'))
        <div class="checkout-alert">{{ session('error') }}</div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="checkout-layout" data-checkout-submit-form>
        @csrf
        <main class="checkout-stack">
            <section class="checkout-panel checkout-accordion is-open">
                <button type="button" class="checkout-accordion-trigger" aria-expanded="true">
                    <span><b>Customer Information</b><small>{{ $address->name }} · {{ $address->email ?: $user->email }}</small></span>
                    <i class="ti ti-chevron-down"></i>
                </button>
                <div class="checkout-accordion-panel">
                <div class="checkout-accordion-body">
                    <div class="checkout-panel-head checkout-panel-head-row">
                        <div>
                            <span>Customer</span>
                            <h2>{{ $address->name }}</h2>
                        </div>
                        <a href="{{ route('dashboard.profile.edit') }}" class="checkout-link-btn">Edit</a>
                    </div>
                    <p class="checkout-muted">{{ $address->email ?: $user->email }} · {{ $address->phone }}</p>
                </div>
                </div>
            </section>

            <section class="checkout-panel checkout-accordion">
                <button type="button" class="checkout-accordion-trigger" aria-expanded="false">
                    <span><b>Delivery Address</b><small>{{ $address->city }} - {{ $address->postal_code }}</small></span>
                    <i class="ti ti-chevron-down"></i>
                </button>
                <div class="checkout-accordion-panel">
                <div class="checkout-accordion-body">
                    <div class="checkout-panel-head checkout-panel-head-row">
                        <div>
                            <span>Delivery</span>
                            <h2>{{ $address->label }} Address</h2>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="checkout-link-btn">Change</a>
                    </div>
                    <p class="checkout-muted">{{ $address->name }} · {{ $address->phone }}</p>
                    <p class="checkout-address-line">{{ $address->address_line_1 }}, {{ $address->address_line_2 }}{{ $address->landmark ? ', Landmark: '.$address->landmark : '' }}, {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}, {{ $address->country }}</p>
                    <strong class="checkout-delivery-date">Estimated delivery: {{ \Illuminate\Support\Carbon::parse($estimatedDeliveryDate)->format('D, d M Y') }}</strong>
                </div>
                </div>
            </section>

            <section class="checkout-panel checkout-accordion">
                <button type="button" class="checkout-accordion-trigger" aria-expanded="false">
                    <span><b>Order Items</b><small>{{ $cart->items->sum('quantity') }} item(s)</small></span>
                    <i class="ti ti-chevron-down"></i>
                </button>
                <div class="checkout-accordion-panel">
                <div class="checkout-accordion-body">
                <div class="review-products">
                    @foreach($cart->items as $item)
                        @php
                            $attrs = $item->meta['product_attribute_values'] ?? [];
                            $discount = $item->original_price && (float) $item->original_price > (float) $item->price
                                ? ((float) $item->original_price - (float) $item->price) * $item->quantity
                                : 0;
                        @endphp
                        <article class="review-product">
                            <img src="{{ asset($item->product_image ?: $item->product?->image ?: 'images/no-image.png') }}" alt="{{ $item->product_name }}">
                            <div>
                                <strong>{{ $item->product_name }}</strong>
                                <span>SKU: {{ $item->product_sku ?: '-' }}</span>
                                @if($attrs)
                                    <small>
                                        @foreach($attrs as $row)
                                            {{ $row['attribute_name'] ?? 'Option' }}: {{ $row['attribute_value_label'] ?? $row['value'] ?? '-' }}{{ ! $loop->last ? ' · ' : '' }}
                                        @endforeach
                                    </small>
                                @endif
                            </div>
                            <div><span>Qty</span><strong>{{ $item->quantity }}</strong></div>
                            <div><span>Unit</span><strong>₹{{ number_format((float) $item->price, 2) }}</strong></div>
                            <div><span>Discount</span><strong>₹{{ number_format($discount, 2) }}</strong></div>
                            <div><span>Subtotal</span><strong>₹{{ number_format((float) $item->subtotal, 2) }}</strong></div>
                        </article>
                    @endforeach
                </div>
                </div>
                </div>
            </section>
        </main>

        <aside class="checkout-summary">
            <h2>Price Details</h2>

            <div class="coupon-box">
                <div class="coupon-row">
                    <input type="text" data-coupon-input value="{{ $cart->coupon_code }}" placeholder="Coupon code">
                    <button type="button" data-apply-coupon>Apply</button>
                </div>
                @if($cart->coupon_code)
                    <button type="button" class="coupon-remove" data-remove-coupon>Remove {{ $cart->coupon_code }}</button>
                @endif
     
                <p data-coupon-message></p>
            </div>

            <dl data-summary>
                <div><dt>Product Total</dt><dd>₹<span data-product-total>{{ number_format($productTotal, 2) }}</span></dd></div>
                <div><dt>Product Discount</dt><dd>-₹<span data-product-discount>{{ number_format($productDiscount, 2) }}</span></dd></div>
                <div><dt>Subtotal</dt><dd>₹<span data-subtotal>{{ number_format((float) $cart->subtotal, 2) }}</span></dd></div>
                <div><dt>Coupon Discount</dt><dd>-₹<span data-discount>{{ number_format((float) $cart->discount_total, 2) }}</span></dd></div>
                <div><dt>Shipping</dt><dd>₹<span data-shipping>{{ number_format((float) $cart->shipping_total, 2) }}</span></dd></div>
                <div><dt>Tax</dt><dd>₹<span data-tax>{{ number_format((float) $cart->tax_total, 2) }}</span></dd></div>
                <div><dt>Total Savings</dt><dd>₹<span data-total-savings>{{ number_format($productDiscount + (float) $cart->discount_total, 2) }}</span></dd></div>
                <div class="checkout-total"><dt>Payable</dt><dd>₹<span data-total>{{ number_format((float) $cart->grand_total, 2) }}</span></dd></div>
            </dl>

            <div class="right-payment-box">
                <div class="checkout-panel-head">
                    <span>Payment</span>
                    <h2>Cash on Delivery</h2>
                </div>
                <input type="hidden" name="payment_method" value="cod">
                <div class="payment-options">
                    <label class="is-selected">
                        <input type="radio" name="payment_method_display" value="cod" checked disabled>
                        <span><strong>COD Available</strong><small>Pay by cash when your order arrives.</small></span>
                    </label>
                </div>
                <label class="checkout-notes">
                    <span>Delivery Notes</span>
                    <textarea name="notes" rows="3" placeholder="Optional instructions"></textarea>
                </label>
            </div>

        

            <button type="submit" data-checkout-submit>
                <i class="ti ti-package-export"></i>
                <span>Place Order</span>
            </button>
        </aside>
    </form>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const money = value => Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const message = document.querySelector('[data-coupon-message]');
    const setTotals = cart => {
        const productDiscount = Number(cart.product_discount_total || 0);
        const productTotal = Number(cart.product_total || (Number(cart.subtotal || 0) + productDiscount));
        document.querySelector('[data-product-total]').textContent = money(productTotal);
        document.querySelector('[data-product-discount]').textContent = money(productDiscount);
        document.querySelector('[data-subtotal]').textContent = money(cart.subtotal);
        document.querySelector('[data-discount]').textContent = money(cart.discount_total);
        document.querySelector('[data-shipping]').textContent = money(cart.shipping_total);
        document.querySelector('[data-tax]').textContent = money(cart.tax_total);
        document.querySelector('[data-total-savings]').textContent = money(productDiscount + Number(cart.discount_total || 0));
        document.querySelector('[data-total]').textContent = money(cart.grand_total);
    };
    const couponRequest = async (url, method, code = null) => {
        const response = await fetch(url, {
            method,
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: code ? JSON.stringify({ code }) : null,
        });
        const data = await response.json();
        message.textContent = data.message || '';
        message.classList.toggle('is-error', !response.ok || data.status === false);
        if (data.cart) setTotals(data.cart);
    };
    document.querySelector('[data-apply-coupon]')?.addEventListener('click', () => {
        couponRequest('{{ route('coupon.apply') }}', 'POST', document.querySelector('[data-coupon-input]').value);
    });
    document.querySelector('[data-remove-coupon]')?.addEventListener('click', () => couponRequest('{{ route('coupon.remove') }}', 'DELETE'));
    document.querySelectorAll('[data-coupon-chip]').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelector('[data-coupon-input]').value = button.dataset.couponChip;
            couponRequest('{{ route('coupon.apply') }}', 'POST', button.dataset.couponChip);
        });
    });

    const accordions = Array.from(document.querySelectorAll('.checkout-accordion'));
    const setAccordion = (accordion, open) => {
        const trigger = accordion.querySelector('.checkout-accordion-trigger');
        if (!trigger) return;

        accordion.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    accordions.forEach((accordion) => {
        setAccordion(accordion, accordion.classList.contains('is-open'));
        accordion.querySelector('.checkout-accordion-trigger')?.addEventListener('click', () => {
            const shouldOpen = !accordion.classList.contains('is-open');
            accordions.forEach((other) => setAccordion(other, false));
            if (shouldOpen) {
                setAccordion(accordion, true);
            }
        });
    });

    window.addEventListener('resize', () => accordions.forEach((accordion) => {
        setAccordion(accordion, accordion.classList.contains('is-open'));
    }));

    document.querySelector('[data-checkout-submit-form]')?.addEventListener('submit', event => {
        const button = event.currentTarget.querySelector('[data-checkout-submit]');
        if (!button || button.disabled) return;

        button.disabled = true;
        button.querySelector('span').textContent = 'Processing...';
    });
})();
</script>
@endpush
