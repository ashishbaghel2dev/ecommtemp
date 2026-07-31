@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
<div class="main-content">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Coupons</h2>
            <p class="page-subtitle">Create and manage checkout promotions</p>
        </div>
        <a href="{{ route('coupons.create') }}" class="btn-primary"><i class="ti ti-plus"></i> Add Coupon</a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Usage</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong><br><small>{{ $coupon->name }}</small></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $coupon->type)) }}</td>
                        <td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '₹'.number_format((float) $coupon->value, 2) }}</td>
                        <td>₹{{ number_format((float) $coupon->minimum_order_amount, 2) }}</td>
                        <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}</td>
                        <td>{{ $coupon->expires_at?->format('d M Y') ?: 'No expiry' }}</td>
                        <td><span class="status-badge {{ $coupon->is_active ? 'active' : 'inactive' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="product-image-box">
                                <a href="{{ route('coupons.edit', $coupon) }}" class="btn-icon edit"><i class="ti ti-pencil-minus"></i></a>
                                <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" onclick="return confirm('Delete this coupon?')"><i class="ti ti-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No coupons created yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $coupons->links() }}
    </div>
</div>
@endsection
