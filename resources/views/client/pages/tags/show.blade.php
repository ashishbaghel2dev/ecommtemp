@extends('client.layouts.app')

@section('title', $tag->title)
@section('meta_description', $tag->meta_description ?: 'Read MaxDew Chemicals articles and technical notes related to ' . $tag->title . '.')
@section('meta_keywords', $tag->title . ', MaxDew Chemicals, industrial chemical articles')
@section('breadcrumb_title', $tag->title)

@section('content')
    <section class="tags-page">
        <div class="container">
            <div class="blog-page-head">
            
                <h1>{{ $tag->title }}</h1>
                <p>{{ $tag->meta_description ?: 'Read blogs related to this industrial chemical topic.' }}</p>
            </div>

            <div class="blog-list-grid">
                @forelse($blogs as $blog)
                    <article class="blog-list-card">
                        <a href="{{ route('client.blogs.show', $blog->slug) }}" class="blog-list-image">
                            <img src="{{ $blog->image ? asset($blog->image) : 'https://images.unsplash.com/photo-1584515933487-779824d29309?q=80&w=1200&auto=format&fit=crop' }}" alt="{{ $blog->image_alt ?: $blog->title }}">
                            <span>{{ $blog->category ?: 'Orthopedic Care' }}</span>
                        </a>

                        <div class="blog-list-content">
                            <div class="blog-list-meta">
                                <span><i class="ti ti-calendar"></i>{{ optional($blog->created_at)->format('M d, Y') }}</span>
                                <span><i class="ti ti-clock"></i>5 Min Read</span>
                            </div>
                            <h2>
                                <a href="{{ route('client.blogs.show', $blog->slug) }}">{{ $blog->title }}</a>
                            </h2>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($blog->description), 130) }}</p>
                            <a href="{{ route('client.blogs.show', $blog->slug) }}" class="blog-read-btn">
                                Read More
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="blog-empty">
                        <i class="ti ti-news"></i>
                        <strong>No blogs found</strong>
                        <span>No posted blogs are linked with this tag yet.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
