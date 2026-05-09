@extends('admin.layouts.app')

@section('title', 'Edit Product Label')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Edit Product Label</h2>
                <p class="page-subtitle">Update labels used to tag products</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('productlabels.index') }}">Product Labels</a>
            <i class="ti ti-chevron-right"></i>
            <span>Edit Product Label</span>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Edit Product Label</h3>

        <form action="{{ route('productlabels.update', $product_label->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Name <span class="required-mark">*</span></label>
                        <input type="text" name="name" class="input-control" value="{{ old('name', $product_label->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Color</label>
                        <input type="color" name="color" value="{{ old('color', $product_label->color ?: '#2563eb') }}" class="input-control">
                    </div>

                    <div class="check-grid">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product_label->is_active) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="ti ti-device-floppy"></i> Update Label</button>
                <a href="{{ route('productlabels.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
