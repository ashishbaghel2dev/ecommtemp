@extends('admin.layouts.app')

@section('title', 'Create Blog')

@section('content')
    @php
        $faqQuestions = old('faq_questions', ['']);
        $faqAnswers = old('faq_answers', ['']);
    @endphp

    <section class="banner-page">
        <div class="banner-form-hero">
            <div class="banner-form-title">
                <span>             <i class="ti ti-chevron-right"></i></span>
                <div>
                    <h2>Create Blog</h2>
                    <p>Add blog question, SEO fields, image, and schema setup</p>
                </div>
            </div>

            <nav class="banner-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="ti ti-chevron-right"></i>
                <a href="{{ route('blogs.index') }}">Blogs</a>
                <i class="ti ti-chevron-right"></i>
                <span>Create Blog</span>
            </nav>
        </div>

        <div class="banner-form-card">
            <h3>Create Blog</h3>

            @if(isset($errors) && $errors->any())
                <div class="banner-error">Please fix the highlighted fields and try again.</div>
            @endif

            <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="blog-form-grid">
                    <label class="banner-field blog-full-field">
                        <span>Title (Question) <em>*</em></span>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Which treatment is best for tooth pain?" required>
                        @if(isset($errors) && $errors->has('title')) <small>{{ $errors->first('title') }}</small> @endif
                    </label>

                    <label class="banner-field blog-full-field">
                        <span>Slug</span>
                        <input type="text" name="slug" value="{{ old('slug') }}" placeholder="best-treatment-for-tooth-pain">
                        @if(isset($errors) && $errors->has('slug')) <small>{{ $errors->first('slug') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Category</span>
                        <input type="text" name="category" value="{{ old('category') }}" placeholder="Dental Care">
                        @if(isset($errors) && $errors->has('category')) <small>{{ $errors->first('category') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Publish</span>
                        <select name="publish_status">
                            <option value="draft" {{ old('publish_status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="posted" {{ old('publish_status') === 'posted' ? 'selected' : '' }}>Post</option>
                        </select>
                        @if(isset($errors) && $errors->has('publish_status')) <small>{{ $errors->first('publish_status') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Sort Order</span>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                        @if(isset($errors) && $errors->has('sort_order')) <small>{{ $errors->first('sort_order') }}</small> @endif
                    </label>

                    <label class="banner-field blog-description-field">
                        <span>Description</span>
                        <textarea class="rich-editor-source" name="description" placeholder="Write blog answer and details">{{ old('description') }}</textarea>
                        @if(isset($errors) && $errors->has('description')) <small>{{ $errors->first('description') }}</small> @endif
                    </label>

                    <label class="banner-field blog-full-field">
                        <span>Meta Keyword</span>
                        <textarea name="meta_keyword" placeholder="dental care, tooth pain, clinic">{{ old('meta_keyword') }}</textarea>
                        @if(isset($errors) && $errors->has('meta_keyword')) <small>{{ $errors->first('meta_keyword') }}</small> @endif
                    </label>

                    <label class="banner-field blog-full-field">
                        <span>Meta Description</span>
                        <textarea name="meta_description" placeholder="Short SEO description">{{ old('meta_description') }}</textarea>
                        @if(isset($errors) && $errors->has('meta_description')) <small>{{ $errors->first('meta_description') }}</small> @endif
                    </label>

                    <label class="banner-field blog-full-field">
                        <span>Meta Tags</span>
                        <textarea name="meta_tags" placeholder="tooth pain, dental clinic, root canal">{{ old('meta_tags') }}</textarea>
                        @if(isset($errors) && $errors->has('meta_tags')) <small>{{ $errors->first('meta_tags') }}</small> @endif
                    </label>

                    <div class="blog-faq-builder" data-blog-faq-builder>
                        <div class="blog-faq-head">
                            <div>
                                <h4>FAQ Schema</h4>
                                <p>Add question and answer rows for FAQPage JSON-LD.</p>
                            </div>
                            <button type="button" class="banner-primary-btn blog-add-faq-btn" data-add-faq>
                                <i class="ti ti-plus"></i>
                                <span>Add More</span>
                            </button>
                        </div>

                        <div class="blog-faq-list" data-faq-list>
                            @foreach($faqQuestions as $index => $question)
                                <div class="blog-faq-item" data-faq-item>
                                    <div class="blog-faq-item-head">
                                        <strong>Question #{{ $loop->iteration }}</strong>
                                        <button type="button" class="banner-icon-btn blog-remove-faq-btn" data-remove-faq aria-label="Remove FAQ">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <label class="banner-field">
                                        <span>Question</span>
                                        <input type="text" name="faq_questions[]" value="{{ $question }}" placeholder="What is Mediclaim insurance?">
                                    </label>
                                    <label class="banner-field">
                                        <span>Answer</span>
                                        <textarea name="faq_answers[]" placeholder="Write answer">{{ $faqAnswers[$index] ?? '' }}</textarea>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <label class="banner-field blog-image-field">
                        <span>Image</span>
                        <span class="banner-upload">
                            <input type="file" name="image" accept="image/*">
                            <i class="ti ti-cloud-upload"></i>
                            <strong>Click to upload blog image</strong>
                        </span>
                        @if(isset($errors) && $errors->has('image')) <small>{{ $errors->first('image') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Image Alt</span>
                        <input type="text" name="image_alt" value="{{ old('image_alt') }}" placeholder="Doctor consultation">
                        @if(isset($errors) && $errors->has('image_alt')) <small>{{ $errors->first('image_alt') }}</small> @endif
                    </label>

                    <label class="banner-check">
                        <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>

                    <label class="banner-field blog-schema-field">
                        <span>Schema JSON-LD Preview</span>
                        <textarea readonly data-schema-preview></textarea>
                    </label>
                </div>

                <div class="banner-form-actions">
                    <button type="submit" class="banner-primary-btn">
                        <i class="ti ti-device-floppy"></i>
                        <span>Save Blog</span>
                    </button>
                    <a href="{{ route('blogs.index') }}" class="banner-secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
