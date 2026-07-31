@extends('admin.layouts.app')

@section('title', 'Search Console Settings')

@section('content')
<div class="main-content admin-settings-page">
    <div class="settings-hero">
        <div>
            <span>SEO</span>
            <h2 class="page-title">Search Console & Sitemap</h2>
            <p class="page-subtitle">Add Google Search Console verification and refresh the generated sitemap.xml file.</p>
        </div>
    </div>

    @include('admin.pages.settings._nav')

    @if(session('success'))
        <div class="settings-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('settings.search.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-brand-google"></i>
                <h3>Google Search Console</h3>
            </div>

            <div class="settings-grid">
                <label class="settings-span-2">
                    <span>Google Search Console Meta Tag</span>
                    <textarea name="google_search_console_verification" rows="4" placeholder='<meta name="google-site-verification" content="verification-code-here">'>{{ old('google_search_console_verification', $settings['google_search_console_verification'] ?? '') }}</textarea>
                    <small>Paste the full Google meta tag or only the content value.</small>
                </label>
            </div>
        </section>

        <div class="settings-actions">
            <button type="submit"><i class="ti ti-device-floppy"></i> Save Search Console</button>
        </div>
    </form>

    <section class="settings-panel">
        <div class="settings-section-head">
            <i class="ti ti-sitemap"></i>
            <h3>Sitemap XML</h3>
        </div>

        <div class="settings-sitemap-row">
            <div>
                <strong>{{ url('/sitemap.xml') }}</strong>
                <small>
                    @if(!empty($settings['sitemap_last_updated_at']))
                        Last updated {{ $settings['sitemap_last_updated_at'] }}
                    @elseif(file_exists($sitemapPath))
                        Existing sitemap file found.
                    @else
                        No static sitemap file has been generated yet.
                    @endif
                </small>
            </div>

            <form action="{{ route('settings.sitemap.update') }}" method="POST">
                @csrf
                <button type="submit" class="settings-secondary-button">
                    <i class="ti ti-refresh"></i>
                    Update sitemap.xml
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
