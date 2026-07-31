@extends('admin.layouts.app')

@section('title', 'Create Gallery')

@section('content')
    <section class="banner-page">
        <div class="banner-form-hero">
            <div class="banner-form-title">
                <span>
                     <i class="ti ti-chevron-right"></i>
                </span>
                <div>
                    <h2>Create Gallery</h2>
                    <p>Add clinic gallery image and display order</p>
                </div>
            </div>

            <nav class="banner-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="ti ti-chevron-right"></i>
                <a href="{{ route('gallery.index') }}">Gallery</a>
                <i class="ti ti-chevron-right"></i>
                <span>Create Gallery</span>
            </nav>
        </div>

        <div class="banner-form-card">
            <h3>Create Gallery</h3>

            @if(isset($errors) && $errors->any())
                <div class="banner-error">
                    Please fix the highlighted fields and try again.
                </div>
            @endif

            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="gallery-form-grid">
                    <label class="banner-field">
                        <span>Title <em>*</em></span>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Clinic Reception" required>
                        @if(isset($errors) && $errors->has('title')) <small>{{ $errors->first('title') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Alt Text</span>
                        <input type="text" name="alt_text" value="{{ old('alt_text') }}" placeholder="Clinic reception area">
                        @if(isset($errors) && $errors->has('alt_text')) <small>{{ $errors->first('alt_text') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Sort Order</span>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                        @if(isset($errors) && $errors->has('sort_order')) <small>{{ $errors->first('sort_order') }}</small> @endif
                    </label>

                    <label class="banner-field gallery-image-field">
                        <span>Image <em>*</em></span>
                        <span class="banner-upload">
                            <input type="file" name="image" accept="image/*" required>
                            <i class="ti ti-cloud-upload"></i>
                            <strong>Click to upload gallery image</strong>
                        </span>
                        @if(isset($errors) && $errors->has('image')) <small>{{ $errors->first('image') }}</small> @endif
                    </label>

                    <label class="banner-check">
                        <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>

                <div class="banner-form-actions">
                    <button type="submit" class="banner-primary-btn">
                        <i class="ti ti-device-floppy"></i>
                        <span>Save Gallery</span>
                    </button>
                    <a href="{{ route('gallery.index') }}" class="banner-secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
