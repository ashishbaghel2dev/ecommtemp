@extends('client.layouts.app')

@section('title', 'Reset Password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/auth.css') }}?v=20260730-auth4">
@endpush

@section('content')
@php($resetData = session('password_reset_otp', []))

<section class="auth-page tanvi-auth tanvi-otp-auth auth-otp-page">
    <div class="auth-shell">
        <div class="auth-panel auth-copy tanvi-copy">
            <h1>Create a new password after OTP verification</h1>
            <p>Enter the code sent to {{ $resetData['email'] ?? 'your email' }}, then choose a strong new password.</p>
        </div>

        <div class="auth-panel auth-form-panel tanvi-card">
            <div class="auth-form-head">
                <h2>Verify & Reset</h2>
                <p>Your OTP is valid for 10 minutes.</p>
            </div>

            @if(session('success'))
                <div class="auth-alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-alert error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.otp.reset') }}" class="auth-form otp-code-form">
                @csrf

                <label class="auth-field">
                    <span>6-digit OTP</span>
                    <input type="text" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" placeholder="123456" autocomplete="one-time-code" required>
                </label>

                <label class="auth-field">
                    <span>New Password</span>
                    <input type="password" name="password" placeholder="Minimum 8 characters" required>
                </label>

                <label class="auth-field">
                    <span>Confirm New Password</span>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </label>

                <button class="auth-primary-btn" type="submit">
                    <i class="ti ti-lock-check"></i>
                    Update Password
                </button>
            </form>

            <form method="POST" action="{{ route('password.otp.resend') }}" class="auth-inline-form">
                @csrf
                <button type="submit" class="auth-text-btn">Resend OTP</button>
            </form>

            <p class="auth-footer-text">
                Use another email?
                <a href="{{ route('password.request') }}">Start again</a>
            </p>
        </div>
    </div>
</section>
@endsection
