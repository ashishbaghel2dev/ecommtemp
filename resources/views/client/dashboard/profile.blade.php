@extends('client.layouts.app')

@section('title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/account-dashboard.css') }}">
@endpush

@section('content')
@php
    $avatar = $user->avatar ?: null;
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
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
                <h1>My Profile</h1>
                <p>View your personal information, verification status and account activity.</p>
            </div>
        </div>

        <a href="{{ route('dashboard.profile.edit') }}" class="account-logout-btn">
            <i class="ti ti-pencil"></i>
            Edit Profile
        </a>
    </header>

    @if(session('success'))
        <div class="account-alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="account-alert error">Please fix the address details and try again.</div>
    @endif

    <div class="account-page-shell">
        <nav class="account-page-nav" aria-label="Account navigation">
            <a href="{{ route('dashboard') }}"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
            <a href="{{ route('dashboard.profile') }}" class="active"><i class="ti ti-user-circle"></i> Profile</a>
            <a href="{{ route('cart.index') }}"><i class="ti ti-shopping-cart"></i> Cart</a>
            <a href="{{ route('wishlist.index') }}"><i class="ti ti-heart"></i> Wishlist</a>
            <a href="{{ route('reviews.index') }}"><i class="ti ti-message-star"></i> Reviews</a>
        </nav>

        <div class="account-page-content">
            <div class="account-side-panel">
                <div class="account-panel">
                    <h2>Personal Information</h2>
                    <dl class="account-info-list">
                        <div>
                            <dt>Name</dt>
                            <dd>{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt>Phone</dt>
                            <dd>{{ $user->phone ?: 'Not added' }}</dd>
                        </div>
                        <div>
                            <dt>Member Since</dt>
                            <dd>{{ optional($user->created_at)->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="account-panel">
                    <h2>Security</h2>
                    <div class="account-security-list">
                        <div>
                            <i class="ti ti-mail-check"></i>
                            <span>Email</span>
                            <strong>{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}</strong>
                        </div>
                        <div>
                            <i class="ti ti-phone-check"></i>
                            <span>Phone</span>
                            <strong>{{ $user->phone_verified_at ? 'Verified' : 'Not verified' }}</strong>
                        </div>
                        <div>
                            <i class="ti ti-clock-check"></i>
                            <span>Last login</span>
                            <strong>{{ $user->last_login_at ? $user->last_login_at->format('d M, h:i A') : 'First visit' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="account-panel account-address-panel">
                <div class="account-panel-head">
                    <div>
                        <span>Saved Addresses</span>
                        <h2>Delivery addresses</h2>
                    </div>
                </div>

                <form action="{{ route('dashboard.addresses.store') }}" method="POST" class="account-address-form">
                    @csrf
                    <div class="account-form-title">
                        <i class="ti ti-map-plus"></i>
                        <strong>Add new address</strong>
                    </div>

                    <label>
                        <span>Label</span>
                        <input type="text" name="label" value="{{ old('label', 'Home') }}" placeholder="Home, Office" required>
                        @error('label') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Name</span>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Phone</span>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Address line 1</span>
                        <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" placeholder="House no, building, street" required>
                        @error('address_line_1') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Address line 2</span>
                        <input type="text" name="address_line_2" value="{{ old('address_line_2') }}" placeholder="Area, landmark">
                        @error('address_line_2') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>City</span>
                        <input type="text" name="city" value="{{ old('city') }}" required>
                        @error('city') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>State</span>
                        <input type="text" name="state" value="{{ old('state') }}" required>
                        @error('state') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Postal code</span>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required>
                        @error('postal_code') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Country</span>
                        <input type="text" name="country" value="{{ old('country', 'India') }}" required>
                        @error('country') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="account-check-row">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <span>Make default address</span>
                    </label>
                    <button type="submit">
                        <i class="ti ti-plus"></i>
                        Add Address
                    </button>
                </form>

                <div class="account-address-grid">
                    @forelse($user->addresses as $address)
                        <div class="account-address-card">
                            <strong>{{ $address->label }} {{ $address->is_default ? '(Default)' : '' }}</strong>
                            <span>{{ $address->name }} / {{ $address->phone }}</span>
                            <p>{{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }}, {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}, {{ $address->country }}</p>

                            <details class="account-address-edit">
                                <summary>
                                    <i class="ti ti-pencil"></i>
                                    Edit address
                                </summary>

                                <form action="{{ route('dashboard.addresses.update', $address) }}" method="POST" class="account-address-form compact">
                                    @csrf
                                    @method('PUT')
                                    <label>
                                        <span>Label</span>
                                        <input type="text" name="label" value="{{ old('label', $address->label) }}" required>
                                    </label>
                                    <label>
                                        <span>Name</span>
                                        <input type="text" name="name" value="{{ old('name', $address->name) }}" required>
                                    </label>
                                    <label>
                                        <span>Phone</span>
                                        <input type="text" name="phone" value="{{ old('phone', $address->phone) }}" required>
                                    </label>
                                    <label>
                                        <span>Address line 1</span>
                                        <input type="text" name="address_line_1" value="{{ old('address_line_1', $address->address_line_1) }}" required>
                                    </label>
                                    <label>
                                        <span>Address line 2</span>
                                        <input type="text" name="address_line_2" value="{{ old('address_line_2', $address->address_line_2) }}">
                                    </label>
                                    <label>
                                        <span>City</span>
                                        <input type="text" name="city" value="{{ old('city', $address->city) }}" required>
                                    </label>
                                    <label>
                                        <span>State</span>
                                        <input type="text" name="state" value="{{ old('state', $address->state) }}" required>
                                    </label>
                                    <label>
                                        <span>Postal code</span>
                                        <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" required>
                                    </label>
                                    <label>
                                        <span>Country</span>
                                        <input type="text" name="country" value="{{ old('country', $address->country) }}" required>
                                    </label>
                                    <label class="account-check-row">
                                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                                        <span>Make default address</span>
                                    </label>
                                    <button type="submit">
                                        <i class="ti ti-device-floppy"></i>
                                        Update Address
                                    </button>
                                </form>
                            </details>
                        </div>
                    @empty
                        <p class="account-empty-text">No saved addresses yet. Add your first delivery address above.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
