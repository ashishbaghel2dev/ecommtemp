@extends('admin.layouts.app')

@section('title', 'Charge Settings')

@section('content')
<div class="main-content admin-settings-page">
    <div class="settings-hero">
        <div>
            <span>Checkout</span>
            <h2 class="page-title">Shipping, Tax & Charges</h2>
            <p class="page-subtitle">Manage shipping charge, tax rate, handling, packaging, and other checkout costs.</p>
        </div>
    </div>

    @include('admin.pages.settings._nav')

    @if(session('success'))
        <div class="settings-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('settings.costs.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-truck-delivery"></i>
                <h3>Shipping Charge</h3>
            </div>

            <div class="settings-grid">
                <label>
                    <span>Fixed Shipping Charge</span>
                    <input type="number" name="shipping_amount" min="0" step="0.01" value="{{ old('shipping_amount', $settings['shipping_amount']) }}" required>
                </label>

                <label>
                    <span>Free Shipping Above Order Total</span>
                    <input type="number" name="shipping_free_above" min="0" step="0.01" value="{{ old('shipping_free_above', $settings['shipping_free_above']) }}" required>
                </label>

                <label class="settings-span-2">
                    <span>Apply Only When Cart Has Product IDs</span>
                    <input type="text" name="shipping_product_ids" value="{{ old('shipping_product_ids', $settings['shipping_product_ids']) }}" placeholder="Example: 12, 18, 24. Leave blank for all products.">
                </label>
            </div>

            <div class="settings-toggle-grid">
                <label class="settings-toggle">
                    <input type="checkbox" name="shipping_enabled" value="1" {{ old('shipping_enabled', $settings['shipping_enabled']) ? 'checked' : '' }}>
                    <span>Enable Shipping Charge</span>
                </label>

                <label class="settings-toggle">
                    <input type="checkbox" name="shipping_apply_cod" value="1" {{ old('shipping_apply_cod', $settings['shipping_apply_cod']) ? 'checked' : '' }}>
                    <span>Apply on COD</span>
                </label>

                <label class="settings-toggle">
                    <input type="checkbox" name="shipping_apply_online" value="1" {{ old('shipping_apply_online', $settings['shipping_apply_online']) ? 'checked' : '' }}>
                    <span>Apply on Online Payment</span>
                </label>
            </div>
        </section>

        <section class="settings-panel">
            <div class="settings-section-head">
                <i class="ti ti-receipt-tax"></i>
                <h3>Tax & Extra Costs</h3>
            </div>

            <div class="settings-grid">
                <label>
                    <span>Tax Rate (%)</span>
                    <input type="number" name="tax_rate" min="0" max="100" step="0.01" value="{{ old('tax_rate', $settings['tax_rate']) }}" required>
                </label>

                <label>
                    <span>Handling Charge</span>
                    <input type="number" name="handling_charge" min="0" step="0.01" value="{{ old('handling_charge', $settings['handling_charge']) }}" required>
                </label>

                <label>
                    <span>Packaging Charge</span>
                    <input type="number" name="packaging_charge" min="0" step="0.01" value="{{ old('packaging_charge', $settings['packaging_charge']) }}" required>
                </label>

                <label>
                    <span>{{ old('other_charge_label', $settings['other_charge_label']) ?: 'Other Charge' }} Amount</span>
                    <input type="number" name="other_charge_amount" min="0" step="0.01" value="{{ old('other_charge_amount', $settings['other_charge_amount']) }}" required>
                </label>

                <label class="settings-span-2">
                    <span>Other Charge Label</span>
                    <input type="text" name="other_charge_label" value="{{ old('other_charge_label', $settings['other_charge_label']) }}" placeholder="Example: Convenience Charge">
                </label>
            </div>

            <div class="settings-toggle-grid">
                <label class="settings-toggle">
                    <input type="checkbox" name="tax_enabled" value="1" {{ old('tax_enabled', $settings['tax_enabled']) ? 'checked' : '' }}>
                    <span>Enable Tax</span>
                </label>
            </div>
        </section>

        <div class="settings-actions">
            <button type="submit"><i class="ti ti-device-floppy"></i> Save Charges</button>
            <a href="{{ route('settings.general') }}">Back to General</a>
        </div>
    </form>
</div>
@endsection
