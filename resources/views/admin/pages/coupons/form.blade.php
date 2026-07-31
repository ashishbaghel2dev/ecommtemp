@extends('admin.layouts.app')

@section('title', $coupon->exists ? 'Edit Coupon' : 'Create Coupon')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">%</span>
            <div>
                <h2 class="page-title">{{ $coupon->exists ? 'Edit Coupon' : 'Create Coupon' }}</h2>
                <p class="page-subtitle">Configure discount rules, limits, and product eligibility</p>
            </div>
        </div>
        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('coupons.index') }}">Coupons</a>
            <i class="ti ti-chevron-right"></i>
            <span>{{ $coupon->exists ? 'Edit' : 'Create' }}</span>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <form action="{{ $coupon->exists ? route('coupons.update', $coupon) : route('coupons.store') }}" method="POST">
            @csrf
            @if($coupon->exists)
                @method('PUT')
            @endif

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Code <span class="required-mark">*</span></label>
                            <input class="input-control" name="code" value="{{ old('code', $coupon->code) }}" required>
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-field">
                            <label class="input-label">Name <span class="required-mark">*</span></label>
                            <input class="input-control" name="name" value="{{ old('name', $coupon->name) }}" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Description</label>
                        <textarea class="input-control" name="description" rows="3">{{ old('description', $coupon->description) }}</textarea>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Discount Type</label>
                            <select class="input-control" name="type" required>
                                @foreach(['flat' => 'Flat Discount', 'percentage' => 'Percentage Discount', 'free_shipping' => 'Free Shipping'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $coupon->type ?: 'flat') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="input-label">Value</label>
                            <input class="input-control" type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value ?? 0) }}">
                        </div>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Minimum Order Amount</label>
                            <input class="input-control" type="number" step="0.01" min="0" name="minimum_order_amount" value="{{ old('minimum_order_amount', $coupon->minimum_order_amount ?? 0) }}">
                        </div>
                        <div class="form-field">
                            <label class="input-label">Maximum Discount</label>
                            <input class="input-control" type="number" step="0.01" min="0" name="maximum_discount_amount" value="{{ old('maximum_discount_amount', $coupon->maximum_discount_amount) }}">
                        </div>
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Start Date</label>
                            <input class="input-control" type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\\TH:i')) }}">
                        </div>
                        <div class="form-field">
                            <label class="input-label">Expiry Date</label>
                            <input class="input-control" type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\\TH:i')) }}">
                        </div>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Usage Limit</label>
                            <input class="input-control" type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                        </div>
                        <div class="form-field">
                            <label class="input-label">Per User Limit</label>
                            <input class="input-control" type="number" min="1" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Applicable Categories</label>
                        <select class="input-control" name="applicable_category_ids[]" multiple size="5">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, old('applicable_category_ids', $coupon->applicable_category_ids ?? [])) ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Applicable Products</label>
                        <select class="input-control" name="applicable_product_ids[]" multiple size="5">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ in_array($product->id, old('applicable_product_ids', $coupon->applicable_product_ids ?? [])) ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Excluded Products</label>
                        <select class="input-control" name="excluded_product_ids[]" multiple size="5">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ in_array($product->id, old('excluded_product_ids', $coupon->excluded_product_ids ?? [])) ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="check-grid">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->exists ? $coupon->is_active : true) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="ti ti-device-floppy"></i> Save Coupon</button>
                <a href="{{ route('coupons.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
