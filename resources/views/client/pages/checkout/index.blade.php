@extends('client.layouts.app')

@php
    $cartItems = $cart->items ?? collect();
    $subtotal = (float) $cart->subtotal;
    $productTotal = (float) $cartItems->sum(fn ($item) => (float) ($item->original_price ?: $item->price) * (int) $item->quantity);
    $productDiscount = (float) $cartItems->sum('discount_amount');
    $grandTotal = (float) $cart->grand_total;
@endphp

@section('title', 'Checkout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/checkout.css') }}">
@endpush

@section('content')
<section class="checkout-page" data-checkout>
    <header class="checkout-head compact">
        <div>
            <h1>Checkout</h1>
            <p>Confirm your account and delivery address before reviewing the order.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="checkout-back"><i class="ti ti-arrow-left"></i> Back to Cart</a>
    </header>

    <div class="checkout-progress">
        @foreach(['Cart', 'Account', 'Address', 'Review', 'Success'] as $step)
            @php
                $state = in_array($step, ['Cart', 'Account']) ? 'is-complete' : ($step === 'Address' ? 'is-current' : 'is-pending');
            @endphp
            <span class="{{ $state }} step-{{ strtolower($step) }}">{{ $step }}</span>
        @endforeach
    </div>

    @if(session('error'))
        <div class="checkout-alert">{{ session('error') }}</div>
    @endif

    <div class="checkout-layout">
        <main class="checkout-stack">
            @guest
                <section class="checkout-panel">
                    <div class="checkout-panel-head">
                        <span>Step 1</span>
                        <h2>Login to Continue</h2>
                        <p>Use your email/mobile password or continue securely with Google.</p>
                    </div>

                    <div class="checkout-login-box">
                        <div class="checkout-auth-panel is-active" id="checkout-password-panel">
                            <form action="{{ route('login') }}" method="POST">
                            @csrf
                                <input type="hidden" name="redirect_to" value="checkout">
                                <label>
                                    <span>Email or 10-digit Mobile Number</span>
                                    <div class="checkout-auth-input">
                                        <i class="ti ti-user-circle"></i>
                                        <input type="text" name="login" placeholder="you@example.com or 9876543210" required>
                                    </div>
                                </label>
                                <label>
                                    <span>Password</span>
                                    <div class="checkout-auth-input">
                                        <i class="ti ti-lock"></i>
                                        <input type="password" name="password" placeholder="Enter password" required>
                                    </div>
                                </label>
                                <button type="submit" class="checkout-auth-submit"><i class="ti ti-login-2"></i> Login</button>
                            </form>
                        </div>

                        <div class="checkout-social-auth">
                            <a href="{{ route('auth.google', ['redirect' => 'checkout']) }}" class="auth-google-btn">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="">
                                Continue with Google
                            </a>
                            <p>First create an account with email/mobile password, or continue with Google. <a href="{{ route('register', ['redirect' => 'checkout']) }}">Create Account</a></p>
                        </div>
                    </div>
                </section>
            @else
                <section class="checkout-panel">
                    <div class="checkout-panel-head checkout-panel-head-row">
                        <div>
                            <span>Step 1</span>
                            <h2>Account</h2>
                        </div>
                        <strong>{{ $user->name }} · {{ $user->email }}</strong>
                    </div>
                </section>

                <section class="checkout-panel">
                    <div class="checkout-panel-head checkout-panel-head-row">
                        <div>
                            <span>Step 2</span>
                            <h2>Delivery Address</h2>
                        </div>
                        <button type="button" class="checkout-secondary-btn" data-toggle-address>
                            <i class="ti ti-plus"></i> Add Address
                        </button>
                    </div>

                    <form action="{{ route('checkout.review') }}" method="POST">
                        @csrf
                        <div class="checkout-address-cards">
                            @forelse($addresses as $address)
                                <label class="checkout-address-card" data-address-card="{{ $address->id }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" {{ $address->is_default || $loop->first ? 'checked' : '' }} required>
                                    <span class="address-type">{{ $address->label }} {{ $address->is_default ? 'Default' : '' }}</span>
                                    <strong>{{ $address->name }} · {{ $address->phone }}</strong>
                                    <small>{{ $address->address_line_1 }}, {{ $address->address_line_2 }}{{ $address->landmark ? ', Landmark: '.$address->landmark : '' }}, {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}, {{ $address->country }}</small>
                                    <span class="address-actions">
                                        <button type="button" data-default-address="{{ $address->id }}">Set default</button>
                                        <button type="button" data-delete-address="{{ $address->id }}">Delete</button>
                                    </span>
                                </label>
                            @empty
                                <div class="checkout-empty-address">
                                    <strong>No saved address yet</strong>
                                    <span>Add a delivery address to continue.</span>
                                </div>
                            @endforelse
                        </div>
                        <button class="checkout-primary-btn" type="submit" {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                            Save & Proceed to Review
                            <i class="ti ti-arrow-right"></i>
                        </button>
                    </form>

                    <form class="checkout-address-form {{ $addresses->isNotEmpty() ? 'is-hidden' : '' }}" data-address-form action="{{ route('checkout.addresses.store') }}" method="POST">
                        @csrf
                        <div class="checkout-form-grid">
                            <label>
                                <span>Address Type</span>
                                <select name="label" required>
                                    <option>Home</option>
                                    <option>Office</option>
                                    <option>Other</option>
                                </select>
                            </label>
                            <label>
                                <span>Full Name</span>
                                <input type="text" name="name" value="{{ $user->name }}" required>
                            </label>
                            <label>
                                <span>Mobile</span>
                                <input type="tel" name="phone" value="{{ $user->phone }}" required>
                            </label>
                            <label>
                                <span>Alternate Mobile</span>
                                <input type="tel" name="alternate_phone">
                            </label>
                            <label class="checkout-span-2">
                                <span>Email</span>
                                <input type="email" name="email" value="{{ $user->email }}">
                            </label>
                            <label>
                                <span>House / Building</span>
                                <input type="text" name="address_line_1" required>
                            </label>
                            <label>
                                <span>Street / Area</span>
                                <input type="text" name="address_line_2">
                            </label>
                            <label>
                                <span>Landmark</span>
                                <input type="text" name="landmark">
                            </label>
                            <label>
                                <span>City</span>
                                <input type="text" name="city" required>
                            </label>
                            <label>
                                <span>State</span>
                                <input type="text" name="state" required>
                            </label>
                            <label>
                                <span>PIN Code</span>
                                <input type="text" name="postal_code" required>
                            </label>
                            <label>
                                <span>Country</span>
                                <input type="text" name="country" value="India" required>
                            </label>
                            <label class="checkout-check">
                                <input type="checkbox" name="is_default" value="1">
                                <span>Set as default delivery address</span>
                            </label>
                        </div>
                        <button type="submit" class="checkout-primary-btn"><i class="ti ti-device-floppy"></i> Save Address</button>
                    </form>
                </section>
            @endguest
        </main>

        <aside class="checkout-summary">
            <h2>Order Summary</h2>
            <div class="checkout-items">
                @foreach($cartItems as $item)
                    <div class="checkout-item">
                        <img src="{{ asset($item->product_image ?: $item->product?->image ?: 'images/no-image.png') }}" alt="{{ $item->product_name }}">
                        <div>
                            <strong>{{ $item->product_name }}</strong>
                            <span>SKU {{ $item->product_sku ?: '-' }} · Qty {{ $item->quantity }}</span>
                        </div>
                        <b>₹{{ number_format((float) $item->total, 2) }}</b>
                    </div>
                @endforeach
            </div>

            <dl data-summary>
                <div><dt>Product Total</dt><dd>₹<span data-product-total>{{ number_format($productTotal, 2) }}</span></dd></div>
                <div><dt>Product Discount</dt><dd>-₹<span data-product-discount>{{ number_format($productDiscount, 2) }}</span></dd></div>
                <div><dt>Subtotal</dt><dd>₹<span data-subtotal>{{ number_format($subtotal, 2) }}</span></dd></div>
                <div><dt>Coupon Discount</dt><dd>-₹<span data-discount>{{ number_format((float) $cart->discount_total, 2) }}</span></dd></div>
                <div><dt>Shipping</dt><dd>₹<span data-shipping>{{ number_format((float) $cart->shipping_total, 2) }}</span></dd></div>
                <div><dt>Tax</dt><dd>₹<span data-tax>{{ number_format((float) $cart->tax_total, 2) }}</span></dd></div>
                <div class="checkout-total"><dt>Payable</dt><dd>₹<span data-total>{{ number_format($grandTotal, 2) }}</span></dd></div>
            </dl>
        </aside>
    </div>

    <div class="checkout-toast" data-toast></div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const toast = document.querySelector('[data-toast]');
    const money = value => Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const showToast = (message, ok = true) => {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.toggle('is-error', !ok);
        toast.classList.add('is-visible');
        setTimeout(() => toast.classList.remove('is-visible'), 2800);
    };

    async function submitAjax(form) {
        const response = await fetch(form.action, {
            method: form.method || 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: new FormData(form),
        });
        const data = await response.json();
        showToast(data.message || (response.ok ? 'Done' : 'Please check the form'), response.ok && data.status !== false);
        if (data.reload) window.location.reload();
        return data;
    }

    document.querySelectorAll('[data-ajax-form], [data-address-form]').forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const data = await submitAjax(form);
            if (data.status && data.address) window.location.reload();
        });
    });

    document.querySelector('[data-toggle-address]')?.addEventListener('click', () => {
        document.querySelector('[data-address-form]')?.classList.toggle('is-hidden');
    });

    document.querySelectorAll('[data-delete-address]').forEach(button => {
        button.addEventListener('click', async () => {
            const response = await fetch(`/checkout/addresses/${button.dataset.deleteAddress}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const data = await response.json();
            showToast(data.message || 'Address removed', response.ok);
            if (response.ok) window.location.reload();
        });
    });

    document.querySelectorAll('[data-default-address]').forEach(button => {
        button.addEventListener('click', async () => {
            const response = await fetch(`/checkout/addresses/${button.dataset.defaultAddress}/default`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const data = await response.json();
            showToast(data.message || 'Default updated', response.ok);
            if (response.ok) window.location.reload();
        });
    });

})();
</script>
@endpush
