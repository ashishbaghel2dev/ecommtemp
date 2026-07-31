@extends('client.layouts.app')

@section('title', 'Edit Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/account-dashboard.css') }}">
@endpush

@section('content')
<section class="account-dashboard-page">
    <header class="account-hero">
        <div>
            <h1>Edit Profile</h1>
            <p>Update your personal details. After saving, you will return to your profile page.</p>
        </div>

        <a href="{{ route('dashboard.profile') }}" class="account-logout-btn">
            <i class="ti ti-arrow-left"></i>
            Back to Profile
        </a>
    </header>

    @if($errors->any())
        <div class="account-alert error">Please fix the highlighted fields and try again.</div>
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
            <section class="account-panel account-edit-panel">
                <div class="account-panel-head">
                    <div>
                        <span>Edit Details</span>
                        <h2>Personal information</h2>
                    </div>
                </div>

                <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data" class="account-edit-form">
                    @csrf
                    @method('PUT')

                    <label>
                        <span>Full Name</span>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Email Address</span>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Phone Number</span>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+91 98765 43210">
                        @error('phone') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Profile Photo</span>
                        <input type="file" name="avatar" accept="image/*">
                        @error('avatar') <small>{{ $message }}</small> @enderror
                    </label>

                    <button type="submit">
                        <i class="ti ti-device-floppy"></i>
                        Save Changes
                    </button>
                </form>
            </section>
        </div>
    </div>
</section>
@endsection
