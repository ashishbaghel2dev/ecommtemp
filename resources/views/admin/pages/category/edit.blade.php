@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Edit Category</h2>
                <p class="page-subtitle">Update category details, media, and SEO content</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('categories.index') }}">Categories</a>
            <i class="ti ti-chevron-right"></i>
            <span>Edit Category</span>
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
        <h3>Edit Category</h3>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">
                            Name <span class="required-mark">*</span>
                            <span class="tooltip-hint" tabindex="0" data-tooltip="Customer-facing category name.">?</span>
                        </label>
                        <input type="text" name="name" class="input-control" value="{{ old('name', $category->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Parent Category</label>
                        <select name="parent_id" class="input-control">
                            <option value="">None</option>
                            @foreach($parents as $cat)
                                <option value="{{ $cat->id }}" {{ (string) old('parent_id', $category->parent_id) === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Slug</label>
                            <input type="text" name="slug" class="input-control" value="{{ old('slug', $category->slug) }}">
                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-field">
                            <label class="input-label">Sort Order</label>
                            <input type="number" name="sort_order" class="input-control" value="{{ old('sort_order', $category->sort_order) }}">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Description</label>
                        <textarea name="description" class="input-control" rows="5">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="check-grid">
                        <input type="hidden" name="show_on_home" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="show_on_home" value="1" {{ old('show_on_home', $category->show_on_home) ? 'checked' : '' }}>
                            Show On Home
                        </label>
                        <input type="hidden" name="is_active" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
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
                        @if($category->image)
                            <div class="existing-images">
                                <div class="image-chip">
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                                </div>
                            </div>
                        @endif
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
                        @if($category->banner)
                            <div class="existing-images">
                                <div class="image-chip">
                                    <img src="{{ asset($category->banner) }}" alt="{{ $category->name }} banner">
                                </div>
                            </div>
                        @endif
                        @error('banner') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Meta Title</label>
                        <input type="text" name="meta_title" class="input-control" value="{{ old('meta_title', $category->meta_title) }}">
                    </div>

                    <div class="form-field">
                        <label class="input-label">Meta Description</label>
                        <textarea name="meta_description" class="input-control" rows="4">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy"></i> Update Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
