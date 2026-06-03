@php
    $reviewItems = ($reviews ?? collect())->take(8);
@endphp

<section class="review-carousel-section container-fluid" aria-label="Customer product reviews">
    <div class="review-carousel-shell">
        <div class="review-carousel-copy">
            <span>Customer reviews</span>
            <h2>Real feedback from shoppers upgrading their setup</h2>
            <p>See what customers say after buying computer accessories, cables, adapters, keyboards and everyday PC essentials from our store.</p>
            <a href="{{ route('reviews.index') }}" class="review-carousel-cta">
                <span>View all reviews</span>
                <i class="ti ti-arrow-right"></i>
            </a>
        </div>

        <div class="review-carousel-panel" data-review-carousel>
            @if($reviewItems->isNotEmpty())
                <div class="review-carousel-actions" aria-label="Review carousel controls">
                    <button type="button" class="review-carousel-control" data-review-prev aria-label="Previous reviews">
                        <i class="ti ti-chevron-left"></i>
                    </button>
                    <button type="button" class="review-carousel-control" data-review-next aria-label="Next reviews">
                        <i class="ti ti-chevron-right"></i>
                    </button>
                </div>

                <div class="review-carousel-track" data-review-track>
                    @foreach($reviewItems as $review)
                        <article class="home-review-card">
                            <div class="home-review-stars" aria-label="{{ $review->rating }} out of 5 stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="ti {{ $i <= (int) $review->rating ? 'ti-star-filled' : 'ti-star' }}"></i>
                                @endfor
                            </div>

                            <h3>{{ $review->title ?: 'Helpful product review' }}</h3>

                            @if($review->comment)
                                <p>{{ \Illuminate\Support\Str::limit($review->comment, 150) }}</p>
                            @endif

                            <div class="home-review-foot">
                                <div>
                                    <strong>{{ $review->user->name ?? 'Customer' }}</strong>
                                    <span>{{ $review->product->name ?? 'Product' }}</span>
                                </div>

                                @if($review->product?->slug)
                                    <a href="{{ route('products.show', ['product' => $review->product->slug]) }}" aria-label="View {{ $review->product->name }} details">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="home-review-empty">
                    <i class="ti ti-message-circle"></i>
                    <strong>No reviews yet</strong>
                    <p>Approved customer reviews will appear here soon.</p>
                </div>
            @endif
        </div>
    </div>
</section>

@if($reviewItems->isNotEmpty())
    <script>
        (() => {
            const section = document.querySelector('[data-review-carousel]');
            const track = section?.querySelector('[data-review-track]');
            const previousButton = section?.querySelector('[data-review-prev]');
            const nextButton = section?.querySelector('[data-review-next]');

            if (!track || !previousButton || !nextButton) {
                return;
            }

            const scrollByCard = (direction) => {
                const card = track.querySelector('.home-review-card');
                const cardWidth = card ? card.getBoundingClientRect().width : 320;

                track.scrollBy({
                    left: direction * (cardWidth + 16),
                    behavior: 'smooth',
                });
            };

            previousButton.addEventListener('click', () => scrollByCard(-1));
            nextButton.addEventListener('click', () => scrollByCard(1));
        })();
    </script>
@endif
