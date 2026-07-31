@extends('admin.layouts.app')

@section('title', 'Edit FAQ')

@section('content')
    <section class="banner-page">
        <div class="banner-form-hero">
            <div class="banner-form-title">
                <span>             <i class="ti ti-chevron-right"></i></span>
                <div>
                    <h2>Edit FAQ</h2>
                    <p>Update question and answer content for the clinic FAQ</p>
                </div>
            </div>

            <nav class="banner-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="ti ti-chevron-right"></i>
                <a href="{{ route('faqs.index') }}">FAQ</a>
                <i class="ti ti-chevron-right"></i>
                <span>Edit FAQ</span>
            </nav>
        </div>

        <div class="banner-form-card">
            <h3>Edit FAQ</h3>

            @if(isset($errors) && $errors->any())
                <div class="banner-error">
                    Please fix the highlighted fields and try again.
                </div>
            @endif

            <form action="{{ route('faqs.update', $faq) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="faq-form-grid">
                    <label class="banner-field">
                        <span>Question <em>*</em></span>
                        <input type="text" name="question" value="{{ old('question', $faq->question) }}" placeholder="Enter question" required>
                        @if(isset($errors) && $errors->has('question')) <small>{{ $errors->first('question') }}</small> @endif
                    </label>

                    <label class="banner-field">
                        <span>Written By</span>
                        <input type="text" name="written_by" value="{{ old('written_by', $faq->written_by) }}" placeholder="Author name">
                        @if(isset($errors) && $errors->has('written_by')) <small>{{ $errors->first('written_by') }}</small> @endif
                    </label>

                    <label class="banner-field faq-answer-field">
                        <span>Answer <em>*</em></span>
                        <textarea class="rich-editor-source" name="answer" placeholder="Write answer" required>{{ old('answer', $faq->answer) }}</textarea>
                        @if(isset($errors) && $errors->has('answer')) <small>{{ $errors->first('answer') }}</small> @endif
                    </label>
                </div>

                <div class="banner-form-actions">
                    <button type="submit" class="banner-primary-btn">
                        <i class="ti ti-device-floppy"></i>
                        <span>Update FAQ</span>
                    </button>
                    <a href="{{ route('faqs.index') }}" class="banner-secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
