@extends('client.layouts.app')

@section('title', 'Customer Reviews')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/reviews.css') }}">
@endpush

@section('content')
    <div class="reviews-page">
        <section class="reviews-shell">
            <div class="reviews-head">
                <div>
                    <span>Customer feedback</span>
                    <h1>Customer Reviews</h1>
                </div>

                <a href="{{ route('home') }}" class="reviews-back">
                    <i class="ti ti-arrow-left"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>

            <div class="reviews-list">
                @forelse($reviews as $review)
                    <article class="review-card">
                        <div class="review-card-top">
                            <div>
                                <span>{{ $review->product->name ?? 'Product' }}</span>
                                <h2>{{ $review->title ?: 'Customer review' }}</h2>
                                <small>{{ $review->user->name ?? 'Customer' }} / {{ $review->created_at?->format('M d, Y') }}</small>
                            </div>

                            <div class="review-stars" aria-label="{{ $review->rating }} out of 5 stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="ti {{ $i <= $review->rating ? 'ti-star-filled' : 'ti-star' }}"></i>
                                @endfor
                            </div>
                        </div>

                        @if($review->comment)
                            <p>{{ $review->comment }}</p>
                        @endif

                        @if($review->images->isNotEmpty())
                            <div class="review-images">
                                @foreach($review->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?: 'Review image' }}">
                                @endforeach
                            </div>
                        @endif

                        <div class="review-card-foot">
                            <span>{{ (int) $review->helpful_votes }} found helpful</span>

                            @if($review->product?->slug)
                                <a href="{{ route('products.show', ['product' => $review->product->slug]) }}">View Product</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="reviews-empty">
                        <i class="ti ti-message-off"></i>
                        <strong>No approved reviews yet</strong>
                        <p>Approved customer reviews will appear here.</p>
                    </div>
                @endforelse
            </div>

            <div class="reviews-pagination">
                {{ $reviews->links() }}
            </div>
        </section>
    </div>
@endsection
