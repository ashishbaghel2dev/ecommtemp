@extends('admin.layouts.app')

@section('title', 'Update Review Request')

@section('content')
<div class="main-content">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Update Review Request</h2>
            <p class="page-subtitle">Edit the customer review, status, and admin response.</p>
        </div>

        <a href="{{ route('admin.reviews.index') }}" class="btn-primary">
            <i class="ti ti-arrow-left"></i> Back to Reviews
        </a>
    </div>

    @if($errors->any())
        <div class="alert error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="filter-card admin-review-context">
        <div>
            <strong>Customer</strong>
            <span>{{ $review->user->name ?? 'N/A' }}</span>
        </div>
        <div>
            <strong>Product</strong>
            <span>{{ $review->product->name ?? 'N/A' }}</span>
        </div>
        <div>
            <strong>Submitted</strong>
            <span>{{ $review->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    <div class="table-card admin-review-edit-card">
        <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="admin-review-form">
            @csrf
            @method('PUT')

            <div class="admin-review-form-grid">
                <label>
                    <span>Review Title</span>
                    <input type="text"
                           name="title"
                           class="input-field"
                           value="{{ old('title', $review->title) }}"
                           placeholder="Review title">
                </label>

                <label>
                    <span>Rating</span>
                    <select name="rating" class="input-field" required>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ (int) old('rating', $review->rating) === $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </label>

                <label>
                    <span>Status</span>
                    <select name="status" class="input-field" required>
                        <option value="pending" {{ old('status', $review->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status', $review->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $review->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </label>

                <label class="admin-review-checkbox">
                    <input type="checkbox" name="is_verified_purchase" value="1" {{ old('is_verified_purchase', $review->is_verified_purchase) ? 'checked' : '' }}>
                    <span>Verified purchase</span>
                </label>
            </div>

            <label>
                <span>Customer Comment</span>
                <textarea name="comment"
                          class="input-field"
                          rows="6"
                          placeholder="Customer review comment">{{ old('comment', $review->comment) }}</textarea>
            </label>

            <label>
                <span>Admin Reply</span>
                <textarea name="admin_reply"
                          class="input-field"
                          rows="5"
                          placeholder="Write a public seller/admin reply">{{ old('admin_reply', $review->admin_reply) }}</textarea>
            </label>

            @if($review->images->isNotEmpty())
                <div>
                    <span class="admin-review-label">Review Images</span>
                    <div class="admin-review-images">
                        @foreach($review->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?: 'Review image' }}">
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="admin-review-actions">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy"></i> Update Review
                </button>

                <a href="{{ route('admin.reviews.show', $review) }}" class="admin-review-secondary">View Details</a>
            </div>
        </form>
    </div>
</div>

<style>
    .admin-review-context {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .admin-review-context strong,
    .admin-review-form label > span,
    .admin-review-label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 700;
    }

    .admin-review-context span {
        color: #111827;
    }

    .admin-review-edit-card {
        padding: 22px;
    }

    .admin-review-form {
        display: grid;
        gap: 18px;
    }

    .admin-review-form-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        align-items: end;
    }

    .admin-review-form textarea.input-field {
        min-height: auto;
        padding-top: 12px;
        resize: vertical;
    }

    .admin-review-checkbox {
        display: flex;
        align-items: center;
        gap: 9px;
        min-height: 44px;
    }

    .admin-review-checkbox span {
        margin: 0;
    }

    .admin-review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .admin-review-images img {
        width: 86px;
        height: 86px;
        border-radius: 8px;
        object-fit: cover;
    }

    .admin-review-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .admin-review-secondary {
        color: #374151;
        font-weight: 700;
        text-decoration: none;
    }

    @media (max-width: 900px) {
        .admin-review-context,
        .admin-review-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
