@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')

<div class="main-content">


    <div class="top-bar">
        <h2 class="page-title">Categories</h2>
        <p class="page-subtitle">Create and manage categories</p>
        <a href="{{ route('categories.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add Category
        </a>
    </div>


    <div class="filter-card">
        <form method="GET" class="filter-form">


            <input type="text" name="search" class="input-field" placeholder="Search review..."
                value="{{ request('search') }}">



            <select name="status" class="input-field">

                <option value="">All Status</option>

                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>
                    Pending
                </option>

                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>
                    Approved
                </option>

                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>
                    Rejected
                </option>

            </select>

            <select name="rating" class="input-field">

                <option value="">All Ratings</option>

                @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ request('rating')==$i ? 'selected' : '' }}>
                    {{ $i }} Star
                </option>
                @endfor

            </select>

            <button class="btn-filter">
                Filter
            </button>


        </form>
    </div>




<div class="table-card">
    <table class="custom-table">

        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Product</th>
                <th>Rating</th>
                <th>Title</th>
                  <th>Comments</th>
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
                    
                    <i class="ti ti-star " style="color: rgb(232, 197, 1); font-size: 19px;"></i> {{ $review->rating }}
                </td>

                <td>
                    {{ $review->title }}
                </td>

                <td>
                    {{ $review->comment }}
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

                    <div class="product-image-box">

                        <a href="#" class="btn-icon edit" title="View">
    <i class="ti ti-eye"></i>
</a>

<button class="btn-icon grn" title="Approve">
    <i class="ti ti-check"></i>
</button>

<button class="btn-icon delete" title="Reject">
    <i class="ti ti-x"></i>
</button>

<button class="btn-icon delete" title="Delete">
    <i class="ti ti-trash"></i>
</button>
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