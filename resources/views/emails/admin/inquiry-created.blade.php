@extends('emails.layouts.store')

@section('title', 'New Contact Query')
@section('preheader', 'A new inquiry was submitted from the contact form.')

@section('content')
    <h1 style="margin:0;color:#172033;font-size:24px;">New Contact Query</h1>
    <p style="margin:10px 0 18px;color:#64748b;line-height:1.6;">A customer submitted a new inquiry. Please follow up as soon as possible.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
        <tr><td style="padding:9px 0;color:#64748b;width:140px;">Name</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $inquiry->name }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">Phone</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $inquiry->phone }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">Email</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ $inquiry->email ?: '-' }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;">Status</td><td style="padding:9px 0;font-weight:700;color:#172033;">{{ ucfirst(str_replace('_', ' ', $inquiry->status ?? 'pending')) }}</td></tr>
        <tr><td style="padding:9px 0;color:#64748b;vertical-align:top;">Query</td><td style="padding:9px 0;color:#172033;line-height:1.7;">{{ $inquiry->message }}</td></tr>
    </table>
@endsection
