@extends('admin.layouts.app')

@section('title', 'Sales Customers')

@section('content')
<div class="main-content sales-admin-page">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Customers</h2>
            <p class="page-subtitle">View registered users, order totals, and customer history.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn-primary"><i class="ti ti-shopping-cart"></i> Orders</a>
    </div>

    <div class="filter-card">
        <form method="GET" class="filter-form sales-filter-form">
            <input class="input-field" type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone">
            <button class="btn-filter" type="submit"><i class="ti ti-search"></i> Search</button>
            <a href="{{ route('sales.customers') }}" class="sales-link-btn">Reset</a>
        </form>
    </div>

    <div class="table-card sales-table-card">
        <div class="admin-table-scroll">
            <table class="custom-table sales-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td><strong>{{ $customer->name }}</strong><small>{{ $customer->email }}</small></td>
                            <td>{{ $customer->phone ?: '-' }}</td>
                            <td>{{ $customer->orders_count }}</td>
                            <td>₹{{ number_format((float) ($customer->total_order_value ?? 0), 2) }}</td>
                            <td><span class="status-badge {{ $customer->status ? 'active' : 'inactive' }}">{{ $customer->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('sales.customers.show', $customer) }}" class="btn-icon edit" title="View history"><i class="ti ti-user-search"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $customers->links() }}
    </div>
</div>
@endsection
