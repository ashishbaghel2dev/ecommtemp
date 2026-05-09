@extends('admin.layouts.app')

@section('title', 'Edit Social Link')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Edit Social Link</h2>
                <p class="page-subtitle">Update social media links displayed across the storefront</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('social-links.index') }}">Social Links</a>
            <i class="ti ti-chevron-right"></i>
            <span>Edit Social Link</span>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Edit Social Link</h3>

        <form action="{{ route('social-links.update', $social_link->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Name <span class="required-mark">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $social_link->name) }}" class="input-control" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">URL <span class="required-mark">*</span></label>
                        <input type="url" name="url" value="{{ old('url', $social_link->url) }}" class="input-control" required>
                        @error('url') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Icon</label>
                            <input type="text" name="icon" value="{{ old('icon', $social_link->icon) }}" class="input-control">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Priority</label>
                            <input type="number" name="priority" value="{{ old('priority', $social_link->priority) }}" class="input-control">
                        </div>
                    </div>

                    <div class="check-grid">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $social_link->is_active) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="ti ti-device-floppy"></i> Update Link</button>
                <a href="{{ route('social-links.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
