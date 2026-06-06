@extends('client.layouts.app')

@php
    $cartItems = $cart->items ?? collect();
    $subtotal = (float) ($cart->subtotal ?: $cartItems->sum('subtotal'));
    $grandTotal = (float) ($cart->grand_total ?: $subtotal);
@endphp

@section('title', 'Checkout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/checkout.css') }}">
@endpush

@section('content')
<section class="checkout-page">
    <header class="checkout-head">
        <div>
            <nav class="checkout-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <i class="ti ti-chevron-right"></i>
                <a href="{{ route('cart.index') }}">Cart</a>
                <i class="ti ti-chevron-right"></i>
                <span>Checkout</span>
            </nav>
            <h1>Checkout</h1>
            <p>Add your delivery address and confirm the order.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="checkout-back">
            <i class="ti ti-arrow-left"></i>
            Back to Cart
        </a>
    </header>

    @if($errors->any())
        <div class="checkout-alert">Please check the highlighted fields and try again.</div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="checkout-layout">
        @csrf

        <section class="checkout-panel">
            <div class="checkout-panel-head">
                <span>Delivery</span>
                <h2>Shipping Address</h2>
            </div>

            @if(($addresses ?? collect())->isNotEmpty())
                <div class="checkout-address-choice">
                    <label class="checkout-choice-card">
                        <input type="radio" name="address_mode" value="saved" checked>
                        <span>Use saved address</span>
                    </label>
                    <label class="checkout-choice-card">
                        <input type="radio" name="address_mode" value="new">
                        <span>Add another address</span>
                    </label>
                </div>

                <div class="checkout-saved-addresses" data-saved-addresses>
                    @foreach($addresses as $address)
                        <label class="checkout-saved-address">
                            <input type="radio"
                                   name="saved_address_id"
                                   value="{{ $address->id }}"
                                   data-name="{{ $address->name }}"
                                   data-phone="{{ $address->phone }}"
                                   data-line1="{{ $address->address_line_1 }}"
                                   data-line2="{{ $address->address_line_2 }}"
                                   data-city="{{ $address->city }}"
                                   data-state="{{ $address->state }}"
                                   data-postal="{{ $address->postal_code }}"
                                   data-country="{{ $address->country }}"
                                   {{ $loop->first ? 'checked' : '' }}>
                            <span>
                                <strong>{{ $address->label }} {{ $address->is_default ? '(Default)' : '' }}</strong>
                                <small>{{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
            @else
                <input type="hidden" name="address_mode" value="new">
            @endif

            <div class="checkout-form-grid">
                <label>
                    <span>Full Name</span>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $user->name ?? '') }}" required>
                    @error('customer_name') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Email</span>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $user->email ?? '') }}" required>
                    @error('customer_email') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Phone</span>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone', $user->phone ?? '') }}" required>
                    @error('customer_phone') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Country</span>
                    <input type="text" name="shipping_country" value="{{ old('shipping_country', 'India') }}" required>
                    @error('shipping_country') <small>{{ $message }}</small> @enderror
                </label>

                <label class="checkout-span-2">
                    <span>Address Line 1</span>
                    <input type="text" name="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}" placeholder="House no, building, street" required>
                    @error('shipping_address_line_1') <small>{{ $message }}</small> @enderror
                </label>

                <label class="checkout-span-2">
                    <span>Address Line 2</span>
                    <input type="text" name="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}" placeholder="Area, landmark, optional">
                    @error('shipping_address_line_2') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>City</span>
                    <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required>
                    @error('shipping_city') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>State</span>
                    <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" required>
                    @error('shipping_state') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Postal Code</span>
                    <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required>
                    @error('shipping_postal_code') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Payment Method</span>
                    <select name="payment_method" required>
                        <option value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                    </select>
                    @error('payment_method') <small>{{ $message }}</small> @enderror
                </label>

                <label class="checkout-span-2">
                    <span>Order Notes</span>
                    <textarea name="notes" rows="4" placeholder="Any delivery instructions">{{ old('notes') }}</textarea>
                    @error('notes') <small>{{ $message }}</small> @enderror
                </label>

                @auth
                    <div class="checkout-span-2 checkout-save-address" data-save-address-row>
                        <label>
                            <input type="checkbox" name="save_address" value="1" {{ old('save_address') ? 'checked' : '' }}>
                            <span>Save this address for next checkout</span>
                        </label>

                        <div class="checkout-save-address-fields">
                            <label>
                                <span>Address Label</span>
                                <input type="text" name="address_label" value="{{ old('address_label', 'Home') }}">
                            </label>

                            <label>
                                <span>Default</span>
                                <select name="make_default_address">
                                    <option value="0">No</option>
                                    <option value="1" {{ old('make_default_address') ? 'selected' : '' }}>Yes</option>
                                </select>
                            </label>
                        </div>
                    </div>
                @endauth
            </div>
        </section>

        <aside class="checkout-summary">
            <h2>Order Summary</h2>

            <div class="checkout-items">
                @foreach($cartItems as $item)
                    <div class="checkout-item">
                        <img src="{{ asset($item->product_image ?: $item->product?->image ?: 'images/no-image.png') }}" alt="{{ $item->product_name }}">
                        <div>
                            <strong>{{ $item->product_name }}</strong>
                            <span>Qty {{ $item->quantity }}</span>
                        </div>
                        <b>₹{{ number_format((float) $item->total, 2) }}</b>
                    </div>
                @endforeach
            </div>

            <dl>
                <div>
                    <dt>Subtotal</dt>
                    <dd>₹{{ number_format($subtotal, 2) }}</dd>
                </div>
                <div>
                    <dt>Shipping</dt>
                    <dd>₹{{ number_format((float) ($cart->shipping_total ?? 0), 2) }}</dd>
                </div>
                <div>
                    <dt>Discount</dt>
                    <dd>₹{{ number_format((float) ($cart->discount_total ?? 0), 2) }}</dd>
                </div>
                <div class="checkout-total">
                    <dt>Total</dt>
                    <dd>₹{{ number_format($grandTotal, 2) }}</dd>
                </div>
            </dl>

            <button type="submit">
                <i class="ti ti-package-export"></i>
                Place Order
            </button>
        </aside>
    </form>
</section>
@endsection

@push('scripts')
<script>
    (() => {
        const savedInputs = document.querySelectorAll('input[name="saved_address_id"]');
        const modeInputs = document.querySelectorAll('input[name="address_mode"]');
        const savedList = document.querySelector('[data-saved-addresses]');
        const saveAddressRow = document.querySelector('[data-save-address-row]');
        const fields = {
            name: document.querySelector('input[name="customer_name"]'),
            phone: document.querySelector('input[name="customer_phone"]'),
            line1: document.querySelector('input[name="shipping_address_line_1"]'),
            line2: document.querySelector('input[name="shipping_address_line_2"]'),
            city: document.querySelector('input[name="shipping_city"]'),
            state: document.querySelector('input[name="shipping_state"]'),
            postal: document.querySelector('input[name="shipping_postal_code"]'),
            country: document.querySelector('input[name="shipping_country"]'),
        };

        const fillAddress = (input) => {
            if (!input) return;
            Object.entries(fields).forEach(([key, field]) => {
                if (field && input.dataset[key] !== undefined) {
                    field.value = input.dataset[key] || '';
                }
            });
        };

        const syncMode = () => {
            const mode = document.querySelector('input[name="address_mode"]:checked')?.value || 'new';
            const usingSaved = mode === 'saved';
            savedList?.classList.toggle('is-hidden', !usingSaved);
            saveAddressRow?.classList.toggle('is-hidden', usingSaved);
            if (usingSaved) {
                fillAddress(document.querySelector('input[name="saved_address_id"]:checked'));
            }
        };

        savedInputs.forEach((input) => input.addEventListener('change', () => fillAddress(input)));
        modeInputs.forEach((input) => input.addEventListener('change', syncMode));
        syncMode();
    })();
</script>
@endpush
