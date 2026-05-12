@extends('admin.layouts.app')

@section('title', 'Wishlist Users')

@section('content')

<div class="main-content">

    <!-- Top -->
    <div class="top-bar">

        <h2 class="page-title">
            Wishlist Users
        </h2>

        <p class="page-subtitle">
            Users who added this product to wishlist
        </p>

    </div>





    <!-- Product Info -->
    <div class="card mb-4">

        <div class="card-body d-flex align-items-center gap-3">

            @php
                $image = $product->image
                    ?? optional($product->images->first())->image
                    ?? 'images/no-image.png';
            @endphp

            <img
                src="{{ asset($image) }}"
                alt="{{ $product->name }}"
                style="
                    width:90px;
                    height:90px;
                    object-fit:cover;
                    border-radius:10px;
                "
            >

            <div>

                <h4 class="mb-1">
                    {{ $product->name }}
                </h4>

                <p class="mb-1">
                    <strong>SKU:</strong>
                    {{ $product->sku }}
                </p>

                <span class="badge bg-primary">
                    {{ $users->count() }} Wishlist Users
                </span>

            </div>

        </div>

    </div>





    <!-- Users Table -->
    <div class="table-card">

        <table class="custom-table">

            <thead>

                <tr>
                    <th>#</th>
                    <th>Name</th>
                     <th>Phone</th>
                    <th>Email</th>
                    <th>Wishlist Date</th>
                </tr>

            </thead>

            <tbody>

                @forelse($users as $wishlist)

                    <tr>

                        <td>

                            <div
                                style="
                                    width:40px;
                                    height:40px;
                                    border-radius:50%;
                                    background:#cfe2ff;
                                    color:#002d7b;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-weight:bold;
                                "
                            >
                                {{ strtoupper(substr($wishlist->user->name ?? 'U', 0, 1)) }}
                            </div>

                        </td>

                        <td>
                            {{ $wishlist->user->name ?? 'User Deleted' }}
                        </td>
                       <td>
    @if($wishlist->user->phone)

        <span>
            {{ $wishlist->user->phone }}
        </span>

        <span class="tag {{ $wishlist->user->phone_verified_at ? 'verified' : 'unverified' }}">
            {{ $wishlist->user->phone_verified_at ? 'Verified' : 'Unverified' }}
        </span>

    @else

        <span>-</span>

    @endif
</td>

                      
<td>
    @if($wishlist->user->email)

        <span>
            {{ $wishlist->user->email }}
        </span>

        <span class="tag {{ $wishlist->user->email_verified_at ? 'verified' : 'unverified' }}">
            {{ $wishlist->user->email_verified_at ? 'Verified' : 'Unverified' }}
        </span>

    @else

        <span>-</span>

    @endif
</td>

                        <td>
                            {{ $wishlist->created_at->format('d M, Y h:i A') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" class="text-center">
                            No users found
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection