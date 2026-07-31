@extends('client.layouts.app')

@section('title', $blog->title)
@section('meta_description', $blog->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($blog->description), 155, ''))
@section('meta_keywords', $blog->meta_keyword ?: $blog->meta_tags)
@section('seo_image', $blog->image ? asset($blog->image) : asset('uploads/hero.webp'))
@section('breadcrumb_title', $blog->title)
@section('breadcrumb_image', $blog->image ? asset($blog->image) : asset('uploads/hero.webp'))

@if($blog->schema_markup)
    @push('schema')
        <script type="application/ld+json">{!! $blog->schema_markup !!}</script>
    @endpush
@endif

@section('content')
    @php
        $fallbackImage = 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=1200&auto=format&fit=crop';
        $readTime = max(3, ceil(str_word_count(strip_tags($blog->description)) / 180));
    @endphp

    <section class="blog-detail-page">
        <div class="container">
            <div class="blog-detail-head">
                <span class="blog-pill">{{ $blog->category ?: 'Technical Note' }}</span>
                <h1>{{ $blog->title }}</h1>
                <div class="blog-detail-meta">
                    <span><i class="ti ti-calendar"></i>{{ optional($blog->created_at)->format('M d, Y') }}</span>
                    <span><i class="ti ti-clock"></i>{{ $readTime }} Min Read</span>
                </div>
            </div>

            <div class="blog-detail-layout">
                <article class="blog-detail-main">
                    <div class="blog-detail-image">
                        <img src="{{ $blog->image ? asset($blog->image) : $fallbackImage }}" alt="{{ $blog->image_alt ?: $blog->title }}">
                    </div>

                    <div class="blog-detail-copy">
                        {!! $blog->description !!}
                    </div>
                </article>

                <aside class="blog-side-panel">
                    <div class="blog-side-box blog-contact-box">
                        <span class="section-label">Need Guidance?</span>
                        <strong>Discuss your plant condition with MaxDew.</strong>
                        <p>Share your water quality, operating issue, or product requirement and our team will suggest the next step.</p>
                        <a href="{{ route('contact') }}">Contact Technical Team</a>
                    </div>

                    <div class="blog-side-box">
                        <h3>Related Insights</h3>
                        <div class="blog-related-list">
                            @forelse($relatedBlogs as $relatedBlog)
                                <a href="{{ route('client.blogs.show', $relatedBlog->slug) }}">
                                    <img src="{{ $relatedBlog->image ? asset($relatedBlog->image) : $fallbackImage }}" alt="{{ $relatedBlog->image_alt ?: $relatedBlog->title }}">
                                    <span>{{ $relatedBlog->title }}</span>
                                </a>
                            @empty
                                <p>No related insights available.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="blog-side-box">
                        <h3>Topics</h3>
                        <div class="blog-tag-list">
                            @forelse($tags as $tag)
                                <a href="{{ route('client.tags.show', $tag->slug) }}">{{ $tag->title }}</a>
                            @empty
                                <span>No tags available</span>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
