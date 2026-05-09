@extends('admin.layouts.app')

@section('title', 'Edit Attribute Value')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Edit Attribute Value</h2>
                <p class="page-subtitle">Update selectable values for product attributes</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('attribute-values.index') }}">Attribute Values</a>
            <i class="ti ti-chevron-right"></i>
            <span>Edit Attribute Value</span>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Edit Attribute Value</h3>

        <form action="{{ route('attribute-values.update', $value->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Attribute <span class="required-mark">*</span></label>
                        <select name="attribute_id" class="input-control" required>
                            @foreach($attributes as $attr)
                                <option value="{{ $attr->id }}" {{ old('attribute_id', $value->attribute_id) == $attr->id ? 'selected' : '' }}>
                                    {{ $attr->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('attribute_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Value <span class="required-mark">*</span></label>
                        <input type="text" name="value" value="{{ old('value', $value->value) }}" class="input-control" required>
                        @error('value') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Color Code</label>
                            <input type="text" name="color_code" value="{{ old('color_code', $value->color_code) }}" class="input-control">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $value->sort_order) }}" class="input-control">
                        </div>
                    </div>

                    <div class="check-grid">
                        <input type="hidden" name="is_active" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $value->is_active) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn-primary" type="submit"><i class="ti ti-device-floppy"></i> Update Value</button>
                <a href="{{ route('attribute-values.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
