@extends('admin.layouts.app')

@section('title', 'Home Popup')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step"><i class="ti ti-message-circle-star"></i></span>
            <div>
                <h2 class="page-title">Home Popup</h2>
                <p class="page-subtitle">Manage the smooth popup shown on the home page after visitor scrolling.</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <span>Home Popup</span>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
    @endif

    <section class="product-form-shell">
        <h3>Popup Content</h3>

        <form action="{{ route('home-popup.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Small Label</label>
                            <input type="text" name="home_popup_eyebrow" class="input-control" value="{{ old('home_popup_eyebrow', $settings['home_popup_eyebrow']) }}" placeholder="Special Offer">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Delay Seconds</label>
                            <input type="number" name="home_popup_delay_seconds" class="input-control" min="1" max="120" value="{{ old('home_popup_delay_seconds', $settings['home_popup_delay_seconds']) }}" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Title <span class="required-mark">*</span></label>
                        <input type="text" name="home_popup_title" class="input-control" value="{{ old('home_popup_title', $settings['home_popup_title']) }}" required>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Message <span class="required-mark">*</span></label>
                        <textarea name="home_popup_body" class="input-control" rows="5" required>{{ old('home_popup_body', $settings['home_popup_body']) }}</textarea>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Button Text</label>
                            <input type="text" name="home_popup_button_text" class="input-control" value="{{ old('home_popup_button_text', $settings['home_popup_button_text']) }}" placeholder="Shop Now">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Button Link</label>
                            <input type="text" name="home_popup_button_url" class="input-control" value="{{ old('home_popup_button_url', $settings['home_popup_button_url']) }}" placeholder="/products">
                        </div>
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Popup Image</label>
                        @if(!empty($settings['home_popup_image_path']))
                            <div class="home-popup-preview">
                                <img src="{{ asset($settings['home_popup_image_path']) }}" alt="Home popup preview">
                            </div>
                        @endif
                        <label class="upload-box">
                            <input type="file" name="home_popup_image" accept="image/png,image/jpeg,image/webp">
                            <span>
                                <i class="ti ti-cloud-upload"></i>
                                <span>Upload popup image</span>
                            </span>
                        </label>
                        <small class="form-hint">PNG, JPG, or WEBP up to 2 MB.</small>
                    </div>

                    <div class="check-grid">
                        <label class="check-pill">
                            <input type="checkbox" name="home_popup_enabled" value="1" {{ old('home_popup_enabled', $settings['home_popup_enabled']) ? 'checked' : '' }}>
                            Show popup on home page
                        </label>

                        <label class="check-pill">
                            <input type="checkbox" name="home_popup_show_once" value="1" {{ old('home_popup_show_once', $settings['home_popup_show_once']) ? 'checked' : '' }}>
                            Show once per browser session
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="ti ti-device-floppy"></i> Save Popup</button>
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
