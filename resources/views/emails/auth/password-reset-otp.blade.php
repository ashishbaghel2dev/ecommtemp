@extends('emails.layouts.store')

@section('title', 'Reset your '.$brandName.' password')
@section('preheader', 'Use this OTP to set a new password')

@section('content')
    <p style="margin:0 0 12px;color:#172033;font-size:18px;font-weight:800;">Hi {{ $name }},</p>
    <p style="margin:0;color:#475569;font-size:15px;line-height:1.7;">
        We received a request to reset your {{ $brandName }} password. Use this OTP to continue.
    </p>

    <div style="margin:24px 0;padding:22px;border:1px solid #d8e1f5;border-radius:12px;background:#f5f8ff;text-align:center;">
        <div style="color:#1d4ed8;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Password Reset OTP</div>
        <div style="margin-top:10px;color:#172033;font-size:34px;font-weight:900;letter-spacing:.22em;">{{ $otp }}</div>
        <div style="margin-top:12px;color:#64748b;font-size:13px;">This code expires in {{ $expiresIn }} minutes.</div>
    </div>

    <p style="margin:0;color:#64748b;font-size:14px;line-height:1.7;">
        If you did not request a password reset, keep your current password and ignore this email.
    </p>
@endsection
