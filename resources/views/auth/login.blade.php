@extends('client.layouts.app')

@section('title', 'Login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/auth.css') }}">
@endpush

@section('content')
<section class="auth-page">
    <div class="auth-shell">
        <div class="auth-panel auth-copy">
            <span class="auth-kicker">Computer Shop Account</span>
            <h1>Welcome back</h1>
            <p>Sign in to access your dashboard, wishlist, cart, reviews, and saved account details without leaving the store experience.</p>

            <div class="auth-benefits">
                <div>
                    <i class="ti ti-shopping-cart"></i>
                    <span>Cart sync</span>
                </div>
                <div>
                    <i class="ti ti-heart"></i>
                    <span>Wishlist</span>
                </div>
                <div>
                    <i class="ti ti-user-check"></i>
                    <span>Profile</span>
                </div>
            </div>
        </div>

        <div class="auth-panel auth-form-panel">
            <div class="auth-form-head">
                <span>Login</span>
                <h2>Continue to your account</h2>
            </div>

            @if(session('success'))
                <div class="auth-alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-alert error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                @method('post')

                <label class="auth-field">
                    <span>Email Address</span>
                    <input type="email" name="email" placeholder="ashish@example.com" required value="{{ old('email') }}">
                </label>

                <label class="auth-field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </label>

                <button type="submit" class="auth-primary-btn">
                    <i class="ti ti-login-2"></i>
                    Login
                </button>
            </form>

            <div class="auth-separator">
                <span>or</span>
            </div>

            <a href="{{ route('auth.google') }}" class="auth-google-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="">
                Continue with Google
            </a>

            <p class="auth-footer-text">
                Don't have an account? <a href="{{ route('register') }}">Create one</a>
            </p>
        </div>
    </div>
</section>
@endsection
