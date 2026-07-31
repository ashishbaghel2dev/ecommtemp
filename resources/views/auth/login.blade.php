@extends('client.layouts.app')

@section('title', 'Login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/auth.css') }}?v=20260730-auth4">
@endpush

@section('content')
@php($redirectTo = request('redirect') === 'checkout' ? 'checkout' : old('redirect_to'))

<section class="auth-page tanvi-auth">
    <div class="auth-shell">
        <div class="auth-panel auth-copy tanvi-copy">
            <h1>Login to Tanvi Fashion Jewellery</h1>
            <p>Use your email address, 10-digit mobile number, or continue securely with Google.</p>
        </div>

        <div class="auth-panel auth-form-panel tanvi-card">
            <div class="auth-form-head">
                <h2>Login</h2>
                <p>Email password, mobile password, or Google account only.</p>
            </div>

            @if(session('success'))
                <div class="auth-alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-alert error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <label class="auth-field">
                    <span>Email or 10-digit Mobile Number</span>
                    <input type="text"
                           name="login"
                           inputmode="email"
                           placeholder="you@example.com or 9876543210"
                           value="{{ old('login') }}"
                           required>
                </label>

                <label class="auth-field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </label>

                <div class="auth-form-link-row">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" class="auth-primary-btn">
                    <i class="ti ti-login-2"></i>
                    Login
                </button>
            </form>

            <div class="auth-separator"><span>or</span></div>

            <a href="{{ route('auth.google', array_filter(['redirect' => $redirectTo])) }}" class="auth-google-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="">
                Continue with Google
            </a>

            <p class="auth-footer-text mt-3">
                First time here? Create an account with email/mobile password or continue with Google.
                <a href="{{ route('register', array_filter(['redirect' => $redirectTo])) }}">Create Account</a>
            </p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelector('input[name="login"]')?.addEventListener('input', (event) => {
        const value = event.currentTarget.value;
        if (!value.includes('@') && /^[\d\s()+-]*$/.test(value)) {
            event.currentTarget.value = value.replace(/\D/g, '').slice(0, 10);
        }
    });
</script>
@endpush
