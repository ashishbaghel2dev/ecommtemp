

<section class="section section-light blog-section" id="blogs">

    <div class="container">

        <div class="section-heading center-heading">
            <span class="ab-badge"> Our Blogs</span>
            <h2 class="blog-title">Explore Our Blogs
</h2>
           
        </div>

        <div class="cards-grid cards-grid-3">
            @forelse($blogs->take(3) as $blog)
                <article class="blog-card">
                    {{-- {{ route('client.blogs.show', $blog->slug) }} --}}
                    <a href="" class="blog-image">
                        <img src="{{ $blog->image ? asset($blog->image) : 'https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?q=80&w=1200&auto=format&fit=crop' }}"
                             alt="{{ $blog->image_alt ?: $blog->title }}">

                        <span class="blog-category">{{ $blog->category ?: 'Technical Note' }}</span>
                    </a>

                    <div class="blog-content">
                        <div class="blog-meta">
                            <span>{{ optional($blog->created_at)->format('M d, Y') }}</span>
                            <span>5 Min Read</span>
                        </div>

                        <h3>
                            {{-- {{ route('client.blogs.show', $blog->slug) }}  --}}
                            <a href="#">{{ $blog->title }}</a>
                        </h3>

                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($blog->description), 110) }}</p>

                        {{-- {{ route('client.blogs.show', $blog->slug) }}  --}}
                        <a href="#" class="text-link">
                            Read More
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                </article>
            @empty
                <article class="empty-card">
                    <h3>No blogs available yet</h3>
                    <p>Publish blog posts from the admin panel and they will appear here automatically.</p>
                </article>
            @endforelse
        </div>

        <div class="section-action">
            {{-- {{ route('client.blogs.index') }} --}}
            <a href="#" class="btn btn-dark">Explore All Blogs</a>
        </div>
    </div>
</section>
