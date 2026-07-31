@extends('client.layouts.app')

@section('title', 'Forgot Password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/auth.css') }}?v=20260730-auth4">
@endpush

@section('content')
@php($redirectTo = request('redirect') === 'checkout' ? 'checkout' : old('redirect_to'))

<section class="auth-page tanvi-auth tanvi-otp-auth auth-otp-page">
    <div class="auth-shell">
        <div class="auth-panel auth-copy tanvi-copy">
            <h1>Reset your password with an email OTP</h1>
            <p>Enter your registered email address. We will send a secure 6-digit code to verify it is you.</p>
        </div>

        <div class="auth-panel auth-form-panel tanvi-card">
            <div class="auth-form-head">
                <h2>Forgot Password</h2>
                <p>Use your Gmail or registered email to continue.</p>
            </div>

            @if(session('success'))
                <div class="auth-alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-alert error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.otp.send') }}" class="auth-form">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <label class="auth-field">
                    <span>Email Address</span>
                    <input type="email" name="email" placeholder="you@gmail.com" value="{{ old('email') }}" required>
                </label>

                <button class="auth-primary-btn" type="submit">
                    <i class="ti ti-mail-check"></i>
                    Send OTP
                </button>
            </form>

            <p class="auth-footer-text">
                Remember password?
                <a href="{{ route('login', array_filter(['redirect' => $redirectTo])) }}">Login</a>
            </p>
        </div>
    </div>
</section>
@endsection
