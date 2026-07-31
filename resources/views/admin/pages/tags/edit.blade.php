@extends('admin.layouts.app')

@section('title', 'Edit Tag')

@section('content')
    <section class="banner-page">
        <div class="banner-form-hero">
            <div class="banner-form-title">
                <span>             <i class="ti ti-chevron-right"></i></span>
                <div>
                    <h2>Edit Tag</h2>
                    <p>Update SEO tag details and display status</p>
                </div>
            </div>

            <nav class="banner-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="ti ti-chevron-right"></i>
                <a href="{{ route('tags.index') }}">Tags</a>
                <i class="ti ti-chevron-right"></i>
                <span>Edit Tag</span>
            </nav>
        </div>

        <div class="banner-form-card">
            <h3>Edit Tag</h3>

            @if(isset($errors) && $errors->any())
                <div class="banner-error">Please fix the highlighted fields and try again.</div>
            @endif

            <form action="{{ route('tags.update', $tag) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="tag-form-grid">
                    <label class="banner-field">
                        <span>Title <em>*</em></span>
                        <input type="text" name="title" value="{{ old('title', $tag->title) }}" placeholder="Dental Care" required>
                        @if(isset($errors) && $errors->has('title')) <small>{{ $errors->first('title') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Slug</span>
                        <input type="text" name="slug" value="{{ old('slug', $tag->slug) }}" placeholder="dental-care">
                        @if(isset($errors) && $errors->has('slug')) <small>{{ $errors->first('slug') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Sort Order</span>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $tag->sort_order) }}" min="0">
                        @if(isset($errors) && $errors->has('sort_order')) <small>{{ $errors->first('sort_order') }}</small> @endif
                    </label>

                    <label class="banner-field tag-description-field">
                        <span>Description</span>
                        <textarea class="rich-editor-source" name="description" placeholder="Write tag description">{{ old('description', $tag->description) }}</textarea>
                        @if(isset($errors) && $errors->has('description')) <small>{{ $errors->first('description') }}</small> @endif
                    </label>

                    <label class="banner-field tag-description-field">
                        <span>Meta Description</span>
                        <textarea name="meta_description" placeholder="Short SEO meta description">{{ old('meta_description', $tag->meta_description) }}</textarea>
                        @if(isset($errors) && $errors->has('meta_description')) <small>{{ $errors->first('meta_description') }}</small> @endif
                    </label>

                    <label class="banner-check">
                        <input type="checkbox" name="status" value="1" {{ old('status', $tag->status) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>

                <div class="banner-form-actions">
                    <button type="submit" class="banner-primary-btn">
                        <i class="ti ti-device-floppy"></i>
                        <span>Update Tag</span>
                    </button>
                    <a href="{{ route('tags.index') }}" class="banner-secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
