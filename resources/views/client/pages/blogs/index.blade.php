@extends('client.layouts.app')

@section('title', 'Blogs')
@section('meta_description', 'Read Adzone blogs on retail branding, BTL activations, event campaigns, display solutions, visual merchandising, and brand marketing ideas.')
@section('meta_keywords', 'Adzone blogs, retail branding blog, BTL activation blog, event marketing articles, visual merchandising')
@section('breadcrumb_title', 'Blogs')

@section('content')
    @php
        $featuredBlog = $blogs->first();
        $remainingBlogs = $blogs->skip(1);
        $fallbackImage = asset('uploads/banners/1782293484-banner-6a3ba3ec01f3a5.35774213.jpg');
    @endphp

    <section class="blogs-page">
        <div class="container">
            <div class="blog-page-head">
          <div class="blog-page-copy">
    <span class="section-label">Go Sowa Blogs</span>
    <h1>Natural Wellness Tips & Herbal Tea Insights</h1>
   
</div>

         
            </div>

            @if($featuredBlog)
                <article class="blog-feature-card" aria-label="Featured blog">
                    <a href="{{ route('client.blogs.show', $featuredBlog->slug) }}" class="blog-feature-image">
                        <img src="{{ $featuredBlog->image ? asset($featuredBlog->image) : $fallbackImage }}" alt="{{ $featuredBlog->image_alt ?: $featuredBlog->title }}">
                        <span>Featured</span>
                    </a>
                    <div class="blog-feature-content">
                        <span class="blog-pill">{{ $featuredBlog->category ?: 'Brand Blog' }}</span>
                        <h2>
                            <a href="{{ route('client.blogs.show', $featuredBlog->slug) }}">{{ $featuredBlog->title }}</a>
                        </h2>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($featuredBlog->description), 210) }}</p>
                        <div class="blog-list-meta">
                            <span><i class="ti ti-calendar"></i>{{ optional($featuredBlog->created_at)->format('M d, Y') }}</span>
                            <span><i class="ti ti-clock"></i>{{ max(3, ceil(str_word_count(strip_tags($featuredBlog->description)) / 180)) }} Min Read</span>
                        </div>
                        <a href="{{ route('client.blogs.show', $featuredBlog->slug) }}" class="blog-read-btn">
                            <span>Read Blog</span>
                            <i class="ti ti-arrow-up-right"></i>
                        </a>
                    </div>
                </article>
            @endif

            <div class="blog-page-body">
                <div class="blog-list-grid">
                    @forelse($remainingBlogs as $blog)
                        <article class="blog-list-card">
                            <a href="{{ route('client.blogs.show', $blog->slug) }}" class="blog-list-image">
                                <img src="{{ $blog->image ? asset($blog->image) : $fallbackImage }}" alt="{{ $blog->image_alt ?: $blog->title }}">
                                <span>{{ $blog->category ?: 'Brand Blog' }}</span>
                            </a>

                            <div class="blog-list-content">
                                <div class="blog-list-meta">
                                    <span><i class="ti ti-calendar"></i>{{ optional($blog->created_at)->format('M d, Y') }}</span>
                                    <span><i class="ti ti-clock"></i>{{ max(3, ceil(str_word_count(strip_tags($blog->description)) / 180)) }} Min Read</span>
                                </div>
                                <h2>
                                    <a href="{{ route('client.blogs.show', $blog->slug) }}">{{ $blog->title }}</a>
                                </h2>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($blog->description), 135) }}</p>
                                <a href="{{ route('client.blogs.show', $blog->slug) }}" class="blog-read-btn">
                                    <span>Read Blog</span>
                                    <i class="ti ti-arrow-up-right"></i>
                                </a>
                            </div>
                        </article>
                    @empty
                        @unless($featuredBlog)
                            <div class="blog-empty">
                                <i class="ti ti-news"></i>
                                <strong>No blogs available</strong>
                                <span>Please add posted blogs from the admin dashboard.</span>
                            </div>
                        @endunless
                    @endforelse
                </div>

                <aside class="blog-sidebar">
                    <div class="blog-sidebar-panel">
                        <span class="section-label">Categories</span>
                        <div class="blog-category-list">
                            @forelse($categories as $category)
                                <span>{{ $category }}</span>
                            @empty
                                <span>Brand Blog</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="blog-sidebar-panel is-dark">
                        <span>Need a campaign idea?</span>
                        <strong>Let us turn your brief into a rollout plan.</strong>
                        <a href="{{ route('contact') }}">
                            Contact Us
                            <i class="ti ti-arrow-up-right"></i>
                        </a>
                    </div>
                </aside>
            </div>

            @if($blogs->hasPages())
                <div class="blogs-pagination">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
