@extends('client.layouts.app')

@section('title', 'Register')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/auth.css') }}?v=20260730-auth4">
@endpush

@section('content')
@php($redirectTo = request('redirect') === 'checkout' ? 'checkout' : old('redirect_to'))

<section class="auth-page tanvi-auth tanvi-register-auth">
    <div class="auth-shell">
        <div class="auth-panel auth-copy tanvi-copy">
            <h1>Create your Tanvi Fashion Jewellery account</h1>
            <p>Register with email, 10-digit mobile number, and password. You can also continue with Google.</p>
        </div>

        <div class="auth-panel auth-form-panel tanvi-card">
            <div class="auth-form-head auth-form-head-row">
                <h2>Create Account</h2>
                <a href="{{ route('login', array_filter(['redirect' => $redirectTo])) }}">Login</a>
            </div>

            @if(session('success'))
                <div class="auth-alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-alert error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <label class="auth-field">
                    <span>Full Name</span>
                    <input type="text" name="name" placeholder="Your name" value="{{ old('name') }}" required>
                </label>

                <label class="auth-field">
                    <span>10-digit Mobile Number</span>
                    <input type="tel" name="phone" inputmode="numeric" maxlength="10" placeholder="1234567890" value="{{ old('phone', request('phone')) }}" required>
                </label>

                <label class="auth-field">
                    <span>Email Address</span>
                    <input type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
                </label>

                <label class="auth-field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Minimum 8 characters" required>
                </label>

                <label class="auth-field">
                    <span>Confirm Password</span>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </label>

                <button type="submit" class="auth-primary-btn">
                    <i class="ti ti-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div class="auth-separator"><span>or</span></div>

            <a href="{{ route('auth.google', array_filter(['redirect' => $redirectTo])) }}" class="auth-google-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="">
                Continue with Google
            </a>

            <p class="auth-footer-text mt-3">
                Already have an account?
                <a href="{{ route('login', array_filter(['redirect' => $redirectTo])) }}">Login</a>
            </p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelector('input[name="phone"]')?.addEventListener('input', (event) => {
        event.currentTarget.value = event.currentTarget.value.replace(/\D/g, '').slice(0, 10);
    });
</script>
@endpush
