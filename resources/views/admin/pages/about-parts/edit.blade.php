@extends('admin.layouts.app')

@section('title', 'Edit About Part')

@section('content')
    <section class="banner-page">
        <div class="banner-form-hero">
            <div class="banner-form-title">
                <span>             <i class="ti ti-chevron-right"></i></span>
                <div>
                    <h2>Edit About Part</h2>
                    <p>Update About section title, descriptions, and images</p>
                </div>
            </div>

            <nav class="banner-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="ti ti-chevron-right"></i>
                <a href="{{ route('about-parts.index') }}">About Parts</a>
                <i class="ti ti-chevron-right"></i>
                <span>Edit About Part</span>
            </nav>
        </div>

        <div class="banner-form-card">
            <h3>Edit About Part</h3>

            @if(isset($errors) && $errors->any())
                <div class="banner-error">
                    Please fix the highlighted fields and try again.
                </div>
            @endif

            <form action="{{ route('about-parts.update', $aboutPart) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="about-part-form-grid">
                    <label class="banner-field">
                        <span>Title <em>*</em></span>
                        <input type="text" name="title" value="{{ old('title', $aboutPart->title) }}" placeholder="About Divyakriti Clinic" required>
                        @if(isset($errors) && $errors->has('title')) <small>{{ $errors->first('title') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Slug</span>
                        <input type="text" name="slug" value="{{ old('slug', $aboutPart->slug) }}" placeholder="about-divyakriti-clinic">
                        @if(isset($errors) && $errors->has('slug')) <small>{{ $errors->first('slug') }}</small> @endif
                    </label>

                    <label class="banner-field about-part-short-field">
                        <span>Short Description</span>
                        <textarea name="short_description" placeholder="Short intro for the about section">{{ old('short_description', $aboutPart->short_description) }}</textarea>
                        @if(isset($errors) && $errors->has('short_description')) <small>{{ $errors->first('short_description') }}</small> @endif
                    </label>

                    <label class="banner-field about-part-description-field">
                        <span>Description <em>*</em></span>
                        <textarea class="rich-editor-source" name="description" placeholder="Write full about section details" required>{{ old('description', $aboutPart->description) }}</textarea>
                        @if(isset($errors) && $errors->has('description')) <small>{{ $errors->first('description') }}</small> @endif
                    </label>

                    @foreach(['image_1' => 'Image 1', 'image_2' => 'Image 2', 'image_3' => 'Image 3'] as $field => $label)
                        <label class="banner-field about-part-image-field">
                            <span>{{ $label }}</span>
                            <span class="banner-upload">
                                <input type="file" name="{{ $field }}" accept="image/*">
                                <i class="ti ti-cloud-upload"></i>
                                <strong>Click to upload {{ strtolower($label) }}</strong>
                            </span>
                            @if(isset($errors) && $errors->has($field)) <small>{{ $errors->first($field) }}</small> @endif
                        </label>
                    @endforeach

                    @foreach(['image_1' => 'Current image 1', 'image_2' => 'Current image 2', 'image_3' => 'Current image 3'] as $field => $label)
                        @if($aboutPart->{$field})
                            <div class="banner-current-image about-part-current-image">
                                <span>{{ $label }}</span>
                                <img src="{{ asset($aboutPart->{$field}) }}" alt="{{ $aboutPart->title }}">
                            </div>
                        @endif
                    @endforeach

                    <label class="banner-check">
                        <input type="checkbox" name="status" value="1" {{ old('status', $aboutPart->status) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>

                <div class="banner-form-actions">
                    <button type="submit" class="banner-primary-btn">
                        <i class="ti ti-device-floppy"></i>
                        <span>Update About Part</span>
                    </button>
                    <a href="{{ route('about-parts.index') }}" class="banner-secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
