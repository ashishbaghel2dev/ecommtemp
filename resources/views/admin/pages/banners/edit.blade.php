@extends('admin.layouts.app')

@section('title', 'Edit Banner')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Edit Banner</h2>
                <p class="page-subtitle">Update promotional banner media and placement</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('banners.index') }}">Banners</a>
            <i class="ti ti-chevron-right"></i>
            <span>Edit Banner</span>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Edit Banner</h3>

        <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Image</label>
                        <label class="upload-box">
                            <input type="file" name="image" accept="image/*">
                            <span>
                                <i class="ti ti-cloud-upload"></i>
                                <span>Click to upload banner</span>
                            </span>
                        </label>
                        @if($banner->image)
                            <div class="existing-images">
                                <div class="image-chip">
                                    <img src="{{ asset($banner->image) }}" alt="Banner">
                                </div>
                            </div>
                        @endif
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Link</label>
                        <input type="text" name="link" class="input-control" value="{{ old('link', $banner->link) }}" placeholder="/products or https://example.com">
                        @error('link') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Button Text</label>
                        <input type="text" name="button_text" class="input-control" value="{{ old('button_text', $banner->button_text) }}" placeholder="Shop Now">
                        @error('button_text') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Small Label</label>
                        <input type="text" name="eyebrow" class="input-control" value="{{ old('eyebrow', $banner->eyebrow) }}" placeholder="Go Sowa Herbal Tea">
                        @error('eyebrow') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Banner Title</label>
                        <input type="text" name="title" class="input-control" value="{{ old('title', $banner->title) }}" placeholder="Premium Herbal Tea Collection">
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Banner Description</label>
                        <textarea name="subtitle" class="input-control" rows="4" placeholder="Short banner message">{{ old('subtitle', $banner->subtitle) }}</textarea>
                        @error('subtitle') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Priority</label>
                            <input type="number" name="priority" class="input-control" value="{{ old('priority', $banner->priority) }}">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Position</label>
                            <input type="text" name="position" class="input-control" value="{{ old('position', $banner->position) }}">
                            <small class="field-help">Use <code>home_slider</code> for hero carousel or <code>offer_banner</code> for the 4-card offer grid.</small>
                        </div>
                    </div>

                    <div class="check-grid">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="ti ti-device-floppy"></i> Update Banner</button>
                <a href="{{ route('banners.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
