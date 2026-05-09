@extends('admin.layouts.app')

@section('title', 'Create Attribute')

@section('content')
<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">Create Attribute</h2>
                <p class="page-subtitle">Create product attributes for category-based product details</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('attributes.index') }}">Attributes</a>
            <i class="ti ti-chevron-right"></i>
            <span>Create Attribute</span>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert error">Please fix the highlighted fields and try again.</div>
    @endif

    <section class="product-form-shell">
        <h3>Create Attribute</h3>

        <form action="{{ route('attributes.store') }}" method="POST">
            @csrf

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">Category <span class="required-mark">*</span></label>

                        <select name="category_id" class="input-control" required>

                          
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">Name <span class="required-mark">*</span></label>
                        <input type="text" name="name" class="input-control" value="{{ old('name') }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Code <span class="required-mark">*</span></label>
                            <input type="text" name="code" class="input-control" value="{{ old('code') }}" required>
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-field">
                            <label class="input-label">Type <span class="required-mark">*</span></label>
                            <select name="type" class="input-control" required>
                                <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text</option>
                                <option value="select" {{ old('type') === 'select' ? 'selected' : '' }}>Select</option>
                                <option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>Number</option>
                                <option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                            </select>
                            @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="check-grid">
                        <input type="hidden" name="is_required" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                            Required
                        </label>

                        <input type="hidden" name="is_filterable" value="0">
                        <label class="check-pill">
                            <input type="checkbox" name="is_filterable" value="1" {{ old('is_filterable') ? 'checked' : '' }}>
                            Filterable
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn-primary" type="submit"><i class="ti ti-device-floppy"></i> Save Attribute</button>
                <a href="{{ route('attributes.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
