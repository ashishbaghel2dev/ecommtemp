@extends('admin.layouts.app')

@section('title', 'Theme Settings')

@section('content')
<div class="main-content admin-settings-page">
    <div class="settings-hero">
        <div>
            <span>Appearance</span>
            <h2 class="page-title">Theme Settings</h2>
            <p class="page-subtitle">Choose the dashboard color theme used across admin pages.</p>
        </div>
    </div>

    @include('admin.pages.settings._nav')

    @if(session('success'))
        <div class="settings-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('settings.theme.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-palette"></i>
                <h3>Dashboard Theme</h3>
            </div>

            <div class="theme-grid">
                @foreach($themes as $key => $theme)
                    <label class="theme-option {{ old('dashboard_theme', $settings['dashboard_theme']) === $key ? 'is-selected' : '' }}">
                        <input type="radio" name="dashboard_theme" value="{{ $key }}" {{ old('dashboard_theme', $settings['dashboard_theme']) === $key ? 'checked' : '' }}>
                        <span class="theme-swatch" style="--swatch: {{ $theme['color'] }}">
                            <i class="ti ti-check"></i>
                        </span>
                        <strong>{{ $theme['label'] }}</strong>
                    </label>
                @endforeach
            </div>
        </section>

        <div class="settings-actions">
            <button type="submit"><i class="ti ti-device-floppy"></i> Save Theme</button>
            <a href="{{ route('settings.general') }}">Back to General</a>
        </div>
    </form>
</div>
@endsection
