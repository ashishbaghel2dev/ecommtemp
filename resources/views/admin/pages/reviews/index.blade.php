@extends('admin.layouts.app')

@section('title', 'Review Requests')

@section('content')
<div class="main-content">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Review Requests</h2>
            <p class="page-subtitle">Approve, reject, reply, and update customer product reviews.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" class="filter-form">
            <input type="text"
                   name="search"
                   class="input-field"
                   placeholder="Search review title or comment..."
                   value="{{ request('search') }}">

            <select name="status" class="input-field">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <select name="rating" class="input-field">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ (int) request('rating') === $i ? 'selected' : '' }}>
                        {{ $i }} Star
                    </option>
                @endfor
            </select>

            <button class="btn-filter" type="submit">Filter</button>
        </form>
    </div>

    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th width="270">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->id }}</td>
                        <td>{{ $review->user->name ?? 'N/A' }}</td>
                        <td>{{ $review->product->name ?? 'N/A' }}</td>
                        <td>
                            <i class="ti ti-star-filled" style="color: rgb(232, 197, 1); font-size: 18px;"></i>
                            {{ $review->rating }}
                        </td>
                        <td>
                            <strong>{{ $review->title ?: 'Untitled review' }}</strong>
                            <p class="admin-review-comment">{{ \Illuminate\Support\Str::limit($review->comment ?? '', 90) }}</p>
                            @if($review->admin_reply)
                                <small class="admin-review-reply">Reply added</small>
                            @endif
                        </td>
                        <td>
                            @if($review->status === 'approved')
                                <span class="status-badge active">Approved</span>
                            @elseif($review->status === 'rejected')
                                <span class="status-badge inactive">Rejected</span>
                            @else
                                <span class="admin-status-pending">Pending</span>
                            @endif
                        </td>
                        <td>{{ $review->created_at->format('d M Y') }}</td>
                        <td class="action-cell">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="btn-icon edit" title="View">
                                <i class="ti ti-eye"></i>
                            </a>

                            <a href="{{ route('admin.reviews.edit', $review) }}" class="btn-icon edit" title="Edit">
                                <i class="ti ti-pencil-minus"></i>
                            </a>

                            @if($review->status !== 'approved')
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-icon grn" title="Approve">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </form>
                            @endif

                            @if($review->status !== 'rejected')
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-icon delete" title="Reject">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn-icon delete"
                                        title="Delete"
                                        onclick="return confirm('Delete this review?')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">No reviews found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reviews->withQueryString()->links() }}
    </div>
</div>

<style>
    .admin-review-comment {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.45;
    }

    .admin-review-reply,
    .admin-status-pending {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 12px;
        font-weight: 700;
    }

    .admin-review-reply {
        margin-top: 7px;
        background: #eef2ff;
        color: #3730a3;
    }

    .admin-status-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .action-cell {
        display: flex;
        gap: 7px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-icon.grn {
        background: #ecfdf3;
        color: #027a48;
    }

    .btn-icon.grn:hover {
        background: #027a48;
        color: #fff;
    }
</style>
@endsection
