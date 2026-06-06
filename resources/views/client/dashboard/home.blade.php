@extends('client.layouts.app')

@section('title', 'My Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/account-dashboard.css') }}">
@endpush

@section('content')
@php
    $avatar = $user->avatar ?: null;
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
    $cartItemsCount = $cart?->items?->sum('quantity') ?? 0;
    $cartTotal = $cart?->grand_total ?? 0;
@endphp

<section class="account-dashboard-page">
    <header class="account-hero">
        <div class="account-identity">
            <div class="account-avatar">
                @if($avatar)
                    <img src="{{ asset($avatar) }}" alt="{{ $user->name }}">
                @else
                    <span>{{ $initials ?: 'U' }}</span>
                @endif
            </div>

            <div>
                <nav class="account-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <i class="ti ti-chevron-right"></i>
                    <span>Dashboard</span>
                </nav>
                <h1>Hello, {{ $user->name }}</h1>
                <p>Manage your personal details, wishlist, cart, reviews, and account security from one place.</p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="account-logout-btn">
                <i class="ti ti-logout"></i>
                Logout
            </button>
        </form>
    </header>

    @if(session('success'))
        <div class="account-alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="account-alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <div class="account-page-shell">
        <nav class="account-page-nav" aria-label="Account navigation">
            <a href="{{ route('dashboard') }}" class="active"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
            <a href="{{ route('dashboard.profile') }}"><i class="ti ti-user-circle"></i> Profile</a>
            <a href="{{ route('cart.index') }}"><i class="ti ti-shopping-cart"></i> Cart</a>
            <a href="{{ route('wishlist.index') }}"><i class="ti ti-heart"></i> Wishlist</a>
            <a href="{{ route('reviews.index') }}"><i class="ti ti-message-star"></i> Reviews</a>
        </nav>

        <div class="account-page-content">
            <div class="account-stats-grid">
                <a href="{{ route('cart.index') }}" class="account-stat">
                    <i class="ti ti-shopping-cart"></i>
                    <span>Cart Items</span>
                    <strong>{{ $cartItemsCount }}</strong>
                </a>
                <a href="{{ route('wishlist.index') }}" class="account-stat">
                    <i class="ti ti-heart"></i>
                    <span>Wishlist</span>
                    <strong>{{ $user->wishlists_count ?? $user->wishlists()->count() }}</strong>
                </a>
                <a href="{{ route('reviews.index') }}" class="account-stat">
                    <i class="ti ti-message-star"></i>
                    <span>Reviews</span>
                    <strong>{{ $user->reviews_count ?? $user->reviews()->count() }}</strong>
                </a>
                <div class="account-stat">
                    <i class="ti ti-wallet"></i>
                    <span>Cart Total</span>
                    <strong>₹{{ number_format((float) $cartTotal, 2) }}</strong>
                </div>
            </div>

            <div class="account-layout">
                <main class="account-main-panel">
            <section class="account-panel account-welcome-panel">
                <div class="account-panel-head">
                    <div>
                        <span>Welcome Dashboard</span>
                        <h2>Your account overview</h2>
                    </div>
                    <a href="{{ route('dashboard.profile') }}">View profile</a>
                </div>

                <div class="account-welcome-grid">
                    <a href="{{ route('dashboard.profile') }}">
                        <i class="ti ti-user-circle"></i>
                        <strong>Profile Details</strong>
                        <span>View your personal information and account status.</span>
                    </a>
                    <a href="{{ route('dashboard.profile.edit') }}">
                        <i class="ti ti-pencil"></i>
                        <strong>Edit Profile</strong>
                        <span>Update your name, email, phone and photo.</span>
                    </a>
                    <a href="{{ route('cart.index') }}">
                        <i class="ti ti-shopping-cart"></i>
                        <strong>Your Cart</strong>
                        <span>Open products currently waiting in cart.</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}">
                        <i class="ti ti-heart"></i>
                        <strong>Saved Products</strong>
                        <span>Open your wishlist product list.</span>
                    </a>
                </div>
            </section>

            <section class="account-panel">
                <div class="account-panel-head">
                    <div>
                        <span>Shopping</span>
                        <h2>Quick actions</h2>
                    </div>
                </div>

                <div class="account-action-grid">
                    <a href="{{ route('cart.index') }}">
                        <i class="ti ti-shopping-cart"></i>
                        <strong>View Cart</strong>
                        <span>{{ $cartItemsCount }} item{{ $cartItemsCount === 1 ? '' : 's' }} waiting</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}">
                        <i class="ti ti-heart"></i>
                        <strong>Wishlist</strong>
                        <span>Saved products</span>
                    </a>
                    <a href="{{ route('reviews.index') }}">
                        <i class="ti ti-message-star"></i>
                        <strong>Reviews</strong>
                        <span>Read and manage feedback</span>
                    </a>
                    <a href="{{ route('home') }}">
                        <i class="ti ti-shopping-bag-search"></i>
                        <strong>Continue Shopping</strong>
                        <span>Explore latest products</span>
                    </a>
                </div>
            </section>

            <section class="account-lists-grid">
                <div class="account-panel">
                    <div class="account-panel-head">
                        <div>
                            <span>Wishlist</span>
                            <h2>Saved products</h2>
                        </div>
                        <a href="{{ route('wishlist.index') }}">View all</a>
                    </div>

                    <div class="account-mini-list">
                        @forelse($wishlistItems as $wishlist)
                            @php($product = $wishlist->product)
                            @if($product)
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <img src="{{ asset($product->image ?: 'images/no-image.png') }}" alt="{{ $product->name }}">
                                    <span>{{ $product->name }}</span>
                                    <strong>₹{{ number_format((float) $product->final_price, 2) }}</strong>
                                </a>
                            @endif
                        @empty
                            <p class="account-empty-text">No wishlist products yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="account-panel">
                    <div class="account-panel-head">
                        <div>
                            <span>Reviews</span>
                            <h2>Recent activity</h2>
                        </div>
                        <a href="{{ route('reviews.index') }}">View all</a>
                    </div>

                    <div class="account-review-list">
                        @forelse($recentReviews as $review)
                            <div>
                                <strong>{{ $review->product->name ?? 'Product' }}</strong>
                                <span>{{ $review->rating ?? 0 }}/5 rating</span>
                                <p>{{ \Illuminate\Support\Str::limit($review->comment ?? $review->review ?? 'Review submitted.', 80) }}</p>
                            </div>
                        @empty
                            <p class="account-empty-text">No reviews submitted yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="account-panel account-address-panel">
                <div class="account-panel-head">
                    <div>
                        <span>Saved Addresses</span>
                        <h2>Delivery addresses</h2>
                    </div>
                    <a href="{{ route('dashboard.profile') }}">Manage</a>
                </div>

                <div class="account-address-grid">
                    @forelse($user->addresses as $address)
                        <div class="account-address-card">
                            <strong>{{ $address->label }} {{ $address->is_default ? '(Default)' : '' }}</strong>
                            <span>{{ $address->name }} / {{ $address->phone }}</span>
                            <p>{{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }}, {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}, {{ $address->country }}</p>
                        </div>
                    @empty
                        <p class="account-empty-text">No saved addresses yet. Save an address during checkout and it will appear here.</p>
                    @endforelse
                </div>
            </section>
                </main>
            </div>
        </div>
    </div>
</section>
@endsection
