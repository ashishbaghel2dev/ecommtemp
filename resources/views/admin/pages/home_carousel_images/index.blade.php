@extends('admin.layouts.app')

@section('title', 'Home Carousel Images')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Home Carousel Images</h2>
                <p class="page-subtitle">Update side promo images shown beside product carousels</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <span>Carousel Images</span>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Carousel Images</h3>

        <div class="product-form-layout">
            @foreach($carouselImages as $carouselImage)
                <form class="product-panel" action="{{ route('home-carousel-images.update', $carouselImage->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-field">
                        <label class="input-label">Carousel Key</label>
                        <input type="text" class="input-control" value="{{ $carouselImage->carousel_key }}" disabled>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Title <span class="required-mark">*</span></label>
                        <input type="text" name="title" class="input-control" value="{{ old('title', $carouselImage->title) }}" required>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Side Image</label>
                        <label class="upload-box">
                            <input type="file" name="image" accept="image/*">
                            <span>
                                <i class="ti ti-photo-up"></i>
                                <span>Click to upload side image</span>
                            </span>
                        </label>

                        @if($carouselImage->image)
                            <div class="existing-images">
                                <div class="image-chip">
                                    <img src="{{ asset($carouselImage->image) }}" alt="{{ $carouselImage->title }}">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="ti ti-device-floppy"></i> Update
                        </button>
                    </div>
                </form>
            @endforeach
        </div>
    </section>
</div>
@endsection
