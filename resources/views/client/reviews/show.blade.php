@extends('client.layouts.app')

@section('title', $review->title ?: 'Review Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/pages/reviews.css') }}">
@endpush

@section('content')
    <div class="reviews-page">
        <section class="reviews-shell">
            <div class="reviews-head">
                <div>
                    <span>{{ $review->product->name ?? 'Product review' }}</span>
                    <h1>{{ $review->title ?: 'Customer review' }}</h1>
                </div>

                <a href="{{ url()->previous() }}" class="reviews-back">
                    <i class="ti ti-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>

            <article class="review-card review-card-large">
                <div class="review-card-top">
                    <div>
                        <small>{{ $review->user->name ?? 'Customer' }} / {{ $review->created_at?->format('M d, Y') }}</small>
                    </div>

                    <div class="review-stars" aria-label="{{ $review->rating }} out of 5 stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="ti {{ $i <= $review->rating ? 'ti-star-filled' : 'ti-star' }}"></i>
                        @endfor
                    </div>
                </div>

                <p>{{ $review->comment ?: 'No comment added.' }}</p>

                @if($review->admin_reply)
                    <div class="review-reply">
                        <strong>Seller reply</strong>
                        <p>{{ $review->admin_reply }}</p>
                    </div>
                @endif

                @if($review->images->isNotEmpty())
                    <div class="review-images">
                        @foreach($review->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?: 'Review image' }}">
                        @endforeach
                    </div>
                @endif
            </article>
        </section>
    </div>
@endsection
