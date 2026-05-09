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
                        <input type="url" name="link" class="input-control" value="{{ old('link', $banner->link) }}" placeholder="https://example.com">
                        @error('link') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Priority</label>
                            <input type="number" name="priority" class="input-control" value="{{ old('priority', $banner->priority) }}">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Position</label>
                            <input type="text" name="position" class="input-control" value="{{ old('position', $banner->position) }}">
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
