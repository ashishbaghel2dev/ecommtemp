@extends('admin.layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Add Category</h2>
                <p class="page-subtitle">Create category details, media, and SEO content</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('categories.index') }}">Categories</a>
            <i class="ti ti-chevron-right"></i>
            <span>Add Category</span>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Add Category</h3>

        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">
                            Name <span class="required-mark">*</span>
                            <span class="tooltip-hint" tabindex="0" data-tooltip="Customer-facing category name.">?</span>
                        </label>
                        <input type="text" name="name" class="input-control" value="{{ old('name') }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Parent Category</label>
                        <select name="parent_id" class="input-control">
                            <option value="">None</option>
                            @foreach($parents as $cat)
                                <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Slug</label>
                            <input type="text" name="slug" class="input-control" value="{{ old('slug') }}" placeholder="auto-generated if empty">
                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-field">
                            <label class="input-label">Sort Order</label>
                            <input type="number" name="sort_order" class="input-control" value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Description</label>
                        <textarea name="description" class="input-control" rows="5">{{ old('description') }}</textarea>
                    </div>

                    <div class="check-grid">
                        <input type="hidden" name="show_on_home" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="show_on_home" value="1" {{ old('show_on_home') ? 'checked' : '' }}>
                            Show On Home
                        </label>
                        <input type="hidden" name="is_active" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Image</label>
                        <label class="upload-box">
                            <input type="file" name="image" accept="image/*">
                            <span>
                                <i class="ti ti-cloud-upload"></i>
                                <span>Click to upload image</span>
                            </span>
                        </label>
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Banner</label>
                        <label class="upload-box">
                            <input type="file" name="banner" accept="image/*">
                            <span>
                                <i class="ti ti-photo-up"></i>
                                <span>Click to upload banner</span>
                            </span>
                        </label>
                        @error('banner') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Meta Title</label>
                        <input type="text" name="meta_title" class="input-control" value="{{ old('meta_title') }}">
                    </div>

                    <div class="form-field">
                        <label class="input-label">Meta Description</label>
                        <textarea name="meta_description" class="input-control" rows="4">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy"></i> Save Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
