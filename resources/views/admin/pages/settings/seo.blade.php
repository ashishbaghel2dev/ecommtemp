@extends('admin.layouts.app')

@section('title', 'SEO Pages')

@section('content')
<div class="main-content admin-settings-page">
    <div class="settings-hero">
        <div>
            <span>SEO</span>
            <h2 class="page-title">Page SEO</h2>
            <p class="page-subtitle">Manage meta titles, descriptions, and keywords for static storefront pages.</p>
        </div>
    </div>

    @include('admin.pages.settings._nav')

    @if(session('success'))
        <div class="settings-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('settings.seo.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-world-search"></i>
                <h3>Storefront Pages</h3>
            </div>

            <div class="seo-page-grid">
                @foreach($definitions as $key => $definition)
                    @php($page = $seoPages[$key] ?? [])
                    <article class="seo-page-card">
                        <div class="seo-page-card-head">
                            <div>
                                <strong>{{ $definition['label'] }}</strong>
                                <span>{{ $definition['path'] }}</span>
                            </div>
                            <em>{{ $definition['route'] }}</em>
                        </div>

                        <label>
                            <span>Meta Title</span>
                            <input
                                type="text"
                                name="seo[{{ $key }}][title]"
                                value="{{ old('seo.'.$key.'.title', $page['title'] ?? '') }}"
                                maxlength="180"
                            >
                        </label>

                        <label>
                            <span>Meta Description</span>
                            <textarea
                                name="seo[{{ $key }}][description]"
                                rows="3"
                                maxlength="320"
                            >{{ old('seo.'.$key.'.description', $page['description'] ?? '') }}</textarea>
                        </label>

                        <label>
                            <span>Meta Keywords</span>
                            <textarea
                                name="seo[{{ $key }}][keywords]"
                                rows="2"
                                maxlength="500"
                            >{{ old('seo.'.$key.'.keywords', $page['keywords'] ?? '') }}</textarea>
                        </label>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="settings-actions">
            <button type="submit"><i class="ti ti-device-floppy"></i> Save SEO Pages</button>
        </div>
    </form>
</div>
@endsection
