@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')
<div class="main-content sales-admin-page users-admin-page">

    <div class="top-bar">
        <div>
            <h2 class="page-title">Users</h2>
            <p class="page-subtitle">Manage customer accounts, verification, and order history.</p>
        </div>
        <a href="{{ route('sales.customers') }}" class="btn-primary">
            <i class="ti ti-users"></i> Customer History
        </a>
    </div>

    <div class="table-card sales-table-card">
        <div class="admin-table-scroll">
            <table class="custom-table users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <div class="admin-user-cell">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                                    @else
                                        <span><i class="ti ti-user"></i></span>
                                    @endif
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span>{{ $user->phone ?: '-' }}</span>
                                @if($user->phone)
                                    <small class="tag {{ $user->phone_verified_at ? 'verified' : 'unverified' }}">
                                        {{ $user->phone_verified_at ? 'Verified' : 'Unverified' }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span>{{ $user->email ?: '-' }}</span>
                                @if($user->email)
                                    <small class="tag {{ $user->email_verified_at ? 'verified' : 'unverified' }}">
                                        {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $user->status ? 'active' : 'inactive' }}">
                                    {{ $user->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="product-image-box">
                                    <a href="{{ route('users.show', $user) }}" class="btn-icon edit" title="View customer history">
                                        <i class="ti ti-user-search"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</div>
@endsection
