@extends('admin.layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="main-content admin-settings-page">
    <div class="settings-hero">
        <div>
            <span>Configuration</span>
            <h2 class="page-title">General Settings</h2>
            <p class="page-subtitle">Manage website name, admin labels, and the website logo.</p>
        </div>
    </div>

    @include('admin.pages.settings._nav')

    @if(session('success'))
        <div class="settings-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('settings.general.update') }}" method="POST" enctype="multipart/form-data" class="settings-form">
        @csrf
        @method('PUT')

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-cube"></i>
                <h3>Website Details</h3>
            </div>

            <div class="settings-grid">
                <label>
                    <span>Admin App Name <b>*</b></span>
                    <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                </label>

                <label>
                    <span>Website Name <b>*</b></span>
                    <input type="text" name="user_app_name" value="{{ old('user_app_name', $settings['user_app_name']) }}" required>
                </label>

                <label>
                    <span>Admin Subtitle <b>*</b></span>
                    <input type="text" name="admin_subtitle" value="{{ old('admin_subtitle', $settings['admin_subtitle']) }}" required>
                </label>

                <label>
                    <span>Dashboard Label <b>*</b></span>
                    <input type="text" name="dashboard_label" value="{{ old('dashboard_label', $settings['dashboard_label']) }}" required>
                </label>
            </div>
        </section>

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-photo-up"></i>
                <h3>Website Logo</h3>
            </div>

            <div class="settings-logo-row">
                <div class="settings-logo-preview">
                    <img src="{{ asset($settings['site_logo_path'] ?: 'asset/logo.svg') }}" alt="{{ $settings['user_app_name'] }} logo">
                </div>

                <label class="settings-file">
                    <span>Upload Logo</span>
                    <input type="file" name="site_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    <small>Use PNG, JPG, WEBP, or SVG up to 2 MB.</small>
                </label>
            </div>
        </section>

        <div class="settings-actions">
            <button type="submit"><i class="ti ti-device-floppy"></i> Save General</button>
            <a href="{{ route('admin.dashboard') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
