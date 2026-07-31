@extends('emails.layouts.store')

@section('title', 'User Login')
@section('preheader', 'A user logged in to the store.')

@section('content')
    <h1 style="margin:0;color:#172033;font-size:24px;">New User Login</h1>
    <p style="margin:10px 0 18px;color:#64748b;line-height:1.6;">A customer logged in using {{ $loginType }}.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
        <tr><td style="padding:9px 0;color:#64748b;width:150px;">Name</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $user->name }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">Email</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $user->email ?: '-' }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">Phone</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $user->phone ?: '-' }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">Login Time</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $loggedInAt->format('d-m-Y h:i A') }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">IP Address</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $ip ?: '-' }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;vertical-align:top;">Device</td><td style="padding:9px 0;color:#172033;line-height:1.6;">{{ $userAgent ?: '-' }}</td></tr>
    </table>
@endsection
