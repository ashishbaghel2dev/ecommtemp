@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')


<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Reviews</h2>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
{{-- {{ route('admin.reviews.index') }} --}}
            <form method="GET" action="#">

                <div class="row">

                    {{-- Search --}}
                    <div class="col-md-4 mb-3">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search review..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    {{-- Status --}}
                    <div class="col-md-3 mb-3">
                        <select name="status" class="form-control">

                            <option value="">All Status</option>

                            <option value="pending"
                                {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="approved"
                                {{ request('status') == 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="rejected"
                                {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                        </select>
                    </div>

                    {{-- Rating --}}
                    <div class="col-md-3 mb-3">
                        <select name="rating" class="form-control">

                            <option value="">All Ratings</option>

                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}"
                                    {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} Star
                                </option>
                            @endfor

                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-dark w-100">
                            Filter
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="card">

        <div class="card-body table-responsive">

            <table class="table table-bordered align-middle">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="250">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($reviews as $review)

                        <tr>

                            <td>{{ $review->id }}</td>

                            <td>
                                {{ $review->user->name ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $review->product->name ?? 'N/A' }}
                            </td>

                            <td>
                                ⭐ {{ $review->rating }}
                            </td>

                            <td>
                                {{ $review->title }}
                            </td>

                            <td>

                                @if($review->status == 'approved')

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                @elseif($review->status == 'rejected')

                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $review->created_at->format('d M Y') }}
                            </td>

                            <td>

                                <div class="d-flex flex-wrap gap-2">

                                    {{-- View --}}
                                    {{-- {{ route('admin.reviews.show', $review->id) }} --}}
                                    <a href="#"
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>

                                    {{-- Approve --}}
                                    {{-- {{ route('admin.reviews.approve', $review->id) }} --}}
                                    <form method="POST"
                                          action="#">

                                        @csrf

                                        <button class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                    </form>

                                    {{-- Reject --}}
                                    <form method="POST"
                                    {{-- {{ route('admin.reviews.reject', $review->id) }} --}}
                                          action="#">

                                        @csrf

                                        <button class="btn btn-sm btn-warning">
                                            Reject
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form method="POST"
                                    {{-- {{ route('admin.reviews.destroy', $review->id) }} --}}
                                          action="#"
                                          onsubmit="return confirm('Delete this review?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center">
                                No reviews found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $reviews->withQueryString()->links() }}
    </div>

</div>


@endsection