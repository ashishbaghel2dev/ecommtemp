@extends('admin.layouts.app')

@section('title', 'Review Details')

@section('content')
<div class="main-content">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Review Details</h2>
            <p class="page-subtitle">Inspect the review request before updating its status.</p>
        </div>

        <a href="{{ route('admin.reviews.edit', $review) }}" class="btn-primary">
            <i class="ti ti-pencil-minus"></i> Update Review
        </a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    <div class="admin-review-detail-grid">
        <article class="table-card admin-review-detail-card">
            <div class="admin-review-detail-head">
                <div>
                    <span>{{ $review->product->name ?? 'N/A' }}</span>
                    <h3>{{ $review->title ?: 'Untitled review' }}</h3>
                </div>

                @if($review->status === 'approved')
                    <span class="status-badge active">Approved</span>
                @elseif($review->status === 'rejected')
                    <span class="status-badge inactive">Rejected</span>
                @else
                    <span class="admin-status-pending">Pending</span>
                @endif
            </div>

            <div class="admin-review-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="ti {{ $i <= $review->rating ? 'ti-star-filled' : 'ti-star' }}"></i>
                @endfor
                <strong>{{ $review->rating }}/5</strong>
            </div>

            <p class="admin-review-detail-comment">{{ $review->comment ?: 'No comment added.' }}</p>

            @if($review->admin_reply)
                <div class="admin-review-reply-box">
                    <strong>Admin Reply</strong>
                    <p>{{ $review->admin_reply }}</p>
                </div>
            @endif

            @if($review->images->isNotEmpty())
                <div class="admin-review-images">
                    @foreach($review->images as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?: 'Review image' }}">
                    @endforeach
                </div>
            @endif
        </article>

        <aside class="table-card admin-review-side-card">
            <h3>Request Info</h3>
            <dl>
                <div>
                    <dt>Customer</dt>
                    <dd>{{ $review->user->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt>Product</dt>
                    <dd>{{ $review->product->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt>Submitted</dt>
                    <dd>{{ $review->created_at->format('d M Y, h:i A') }}</dd>
                </div>
                <div>
                    <dt>Helpful</dt>
                    <dd>{{ (int) $review->helpful_votes }}</dd>
                </div>
                <div>
                    <dt>Verified Purchase</dt>
                    <dd>{{ $review->is_verified_purchase ? 'Yes' : 'No' }}</dd>
                </div>
            </dl>

            <div class="admin-review-quick-actions">
                @if($review->status !== 'approved')
                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary">Approve</button>
                    </form>
                @endif

                @if($review->status !== 'rejected')
                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-review-danger">Reject</button>
                    </form>
                @endif

                <a href="{{ route('admin.reviews.index') }}" class="admin-review-secondary">Back to Reviews</a>
            </div>
        </aside>
    </div>
</div>

<style>
    .admin-review-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 22px;
    }

    .admin-review-detail-card,
    .admin-review-side-card {
        padding: 24px;
    }

    .admin-review-detail-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
    }

    .admin-review-detail-head span:first-child {
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
    }

    .admin-review-detail-head h3,
    .admin-review-side-card h3 {
        margin: 4px 0 0;
        color: #111827;
        font-size: 22px;
        font-weight: 800;
    }

    .admin-review-stars {
        display: flex;
        align-items: center;
        gap: 3px;
        margin-top: 16px;
        color: #e8c501;
    }

    .admin-review-stars strong {
        margin-left: 8px;
        color: #111827;
    }

    .admin-review-detail-comment {
        margin: 18px 0 0;
        color: #374151;
        line-height: 1.7;
    }

    .admin-review-reply-box {
        margin-top: 18px;
        border-left: 3px solid #111827;
        padding: 14px;
        background: #f9fafb;
    }

    .admin-review-reply-box p {
        margin: 7px 0 0;
        color: #4b5563;
    }

    .admin-review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .admin-review-images img {
        width: 92px;
        height: 92px;
        border-radius: 8px;
        object-fit: cover;
    }

    .admin-status-pending {
        border-radius: 999px;
        padding: 6px 10px;
        background: #fff7ed;
        color: #c2410c;
        font-size: 12px;
        font-weight: 800;
    }

    .admin-review-side-card dl {
        display: grid;
        gap: 12px;
        margin: 18px 0;
    }

    .admin-review-side-card dl div {
        display: flex;
        justify-content: space-between;
        gap: 16px;
    }

    .admin-review-side-card dt {
        color: #6b7280;
        font-weight: 700;
    }

    .admin-review-side-card dd {
        margin: 0;
        color: #111827;
        font-weight: 700;
        text-align: right;
    }

    .admin-review-quick-actions {
        display: grid;
        gap: 10px;
    }

    .admin-review-quick-actions form,
    .admin-review-quick-actions button,
    .admin-review-quick-actions a {
        width: 100%;
    }

    .admin-review-danger,
    .admin-review-secondary {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 42px;
        border-radius: 8px;
        font-weight: 800;
        text-decoration: none;
    }

    .admin-review-danger {
        border: 0;
        background: #b42318;
        color: #fff;
        cursor: pointer;
    }

    .admin-review-secondary {
        border: 1px solid #d1d5db;
        color: #374151;
    }

    @media (max-width: 900px) {
        .admin-review-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
