@extends('admin.layouts.app')

@section('title', $product ? 'Edit Product' : 'Add Product')

@section('content')
@php
    $selectedCategory = old('category_id', $product->category_id ?? '');
    $selectedLabels = old(
        'labels',
        $product ? $product->labels->pluck('id')->map(fn ($id) => (string) $id)->all() : []
    );
@endphp

<div class="main-content product-form-page">
    <div class="product-form-hero">
        <div class="product-form-heading">
            <span class="product-form-step">5</span>
            <div>
                <h2 class="page-title">{{ $product ? 'Edit Product' : 'Add Product' }}</h2>
                <p class="page-subtitle">Add new product using attributes and values</p>
            </div>
        </div>

        <nav class="product-form-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="ti ti-chevron-right"></i>
            <a href="{{ route('products.index') }}">Products</a>
            <i class="ti ti-chevron-right"></i>
            <span>{{ $product ? 'Edit Product' : 'Add Product' }}</span>
        </nav>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            Please fix the highlighted fields and try again.
        </div>
    @endif

    <section class="product-form-shell">
        <h3>{{ $product ? 'Edit Product' : 'Add Product' }}</h3>

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="product-form-layout">
                <div class="product-panel">
                    <div class="form-field">
                        <label class="input-label">
                            Product Name <span class="required-mark">*</span>
                            <span class="tooltip-hint" tabindex="0" data-tooltip="Use the customer-facing product name.">?</span>
                        </label>
                        <input type="text" name="name" class="input-control" value="{{ old('name', $product->name ?? '') }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-field">
                        <label class="input-label">
                            Category <span class="required-mark">*</span>
                            <span class="tooltip-hint" tabindex="0" data-tooltip="Select a category to load its product attributes.">?</span>
                        </label>
                        <select name="category_id" id="category_id" class="input-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) $selectedCategory === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->parent ? $category->parent->name . ' - ' : '' }}{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">
                                SKU <span class="required-mark">*</span>
                                <span class="tooltip-hint" tabindex="0" data-tooltip="SKU must be unique for this product.">?</span>
                            </label>
                            <input type="text" name="sku" class="input-control" value="{{ old('sku', $product->sku ?? '') }}" required>
                            @error('sku') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-field">
                            <label class="input-label">Product Type</label>
                            <select name="type" id="product_type" class="input-control">
                                <option value="simple" {{ old('type', $product->type ?? 'simple') === 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="configurable" {{ old('type', $product->type ?? '') === 'configurable' ? 'selected' : '' }}>Configurable</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">
                            Product Price <span class="required-mark">*</span>
                            <span class="tooltip-hint" tabindex="0" data-tooltip="Base product price before sale pricing.">?</span>
                        </label>
                        <input type="number" step="0.01" name="price" class="input-control" value="{{ old('price', $product->price ?? '') }}" required>
                        @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" class="input-control" value="{{ old('discount_price', $product->discount_price ?? '') }}">
                        </div>

                        <div class="form-field">
                            <label class="input-label">
                                Sale Price
                                <span class="tooltip-hint" tabindex="0" data-tooltip="Temporary promotional price. Use Sale Start and Sale End to control when this price should be active. Leave dates empty if the sale price is not date limited.">?</span>
                            </label>
                            <input type="number" step="0.01" name="sale_price" class="input-control" value="{{ old('sale_price', $product->sale_price ?? '') }}">
                        </div>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">
                                Sale Start
                                <span class="tooltip-hint" tabindex="0" data-tooltip="Date and time when sale price starts applying.">?</span>
                            </label>
                            <input type="datetime-local" name="sale_start" class="input-control" value="{{ old('sale_start', optional($product?->sale_start ?? null)->format('Y-m-d\TH:i')) }}">
                        </div>

                        <div class="form-field">
                            <label class="input-label">
                                Sale End
                                <span class="tooltip-hint" tabindex="0" data-tooltip="Date and time when sale price should stop applying.">?</span>
                            </label>
                            <input type="datetime-local" name="sale_end" class="input-control" value="{{ old('sale_end', optional($product?->sale_end ?? null)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Product Image</label>
                        <label class="upload-box">
                            <input type="file" name="images[]" id="product_images" accept="image/*" multiple>
                            <span>
                                <i class="ti ti-cloud-upload"></i>
                                <span>Click to upload images</span>
                            </span>
                        </label>
                        <div class="selected-images" id="selected_images"></div>
                        @if($product?->images?->isNotEmpty())
                            <div class="existing-images">
                                @foreach($product->images as $image)
                                    <div class="image-chip">
                                        <img src="{{ asset($image->image) }}" alt="{{ $product->name }}">
                                    </div>
                                @endforeach
                            </div>
                        @elseif($product?->image)
                            <div class="existing-images">
                                <div class="image-chip">
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                                </div>
                            </div>
                        @endif
                        @error('images') <small class="text-danger">{{ $message }}</small> @enderror
                        @error('images.*') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="product-panel">
                    <div class="section-box">
                        <h4>Product Attributes</h4>
                        <div id="attribute_fields" class="attribute-grid"></div>
                        <p id="attribute_empty" class="attribute-empty">Select a category to load attributes.</p>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Stock</label>
                            <input type="number" name="stock" class="input-control" value="{{ old('stock', $product->stock ?? 0) }}">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Inventory</label>
                            <div class="check-grid">
                                <label class="check-pill">
                                    <input type="checkbox" name="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock ?? true) ? 'checked' : '' }}>
                                    Manage Stock
                                </label>
                                <label class="check-pill">
                                    <input type="checkbox" name="in_stock" value="1" {{ old('in_stock', $product->in_stock ?? true) ? 'checked' : '' }}>
                                    In Stock
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Labels</label>
                        <div class="label-grid">
                            @forelse($labels as $label)
                                <label class="label-pill">
                                    <input type="checkbox"
                                        name="labels[]"
                                        value="{{ $label->id }}"
                                        {{ in_array((string) $label->id, $selectedLabels, true) ? 'checked' : '' }}>
                                    {{ $label->name }}
                                </label>
                            @empty
                                <span class="attribute-empty">No labels found.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Short Description</label>
                        <textarea name="short_description" class="input-control js-html-editor" rows="3">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                    </div>

                    <div class="form-field">
                        <label class="input-label">Description</label>
                        <textarea name="description" class="input-control js-html-editor" rows="5">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div class="form-two-col">
                        <div class="form-field">
                            <label class="input-label">Meta Title</label>
                            <input type="text" name="meta_title" class="input-control" value="{{ old('meta_title', $product->meta_title ?? '') }}">
                        </div>

                        <div class="form-field">
                            <label class="input-label">Meta Description</label>
                            <textarea name="meta_description" class="input-control" rows="3">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="check-grid">
                        <label class="check-pill">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            Active
                        </label>
                        <label class="check-pill">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                            Featured
                        </label>
                    </div>
                </div>

            </div>

            <div class="section-box" id="variant_section" style="margin-top: 22px;">
                <div class="variant-head">
                    <h4>Variant Pricing</h4>
                    <button type="button" class="btn-outline" id="add_variant_btn">
                        <i class="ti ti-plus"></i> Add Variant
                    </button>
                </div>

                <div class="variant-table-wrap">
                    <table class="variant-table">
                        <thead>
                            <tr id="variant_header">
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th width="90">Action</th>
                            </tr>
                        </thead>
                        <tbody id="variant_rows"></tbody>
                    </table>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy"></i> {{ $buttonText }}
                </button>
                <a href="{{ route('products.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>

<script type="application/json" id="product-form-data">
    {!! json_encode([
        'attributesByCategory' => $attributesByCategory,
        'selectedAttributes' => $selectedAttributes,
        'selectedVariants' => $selectedVariants ?? [],
    ]) !!}
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="{{ asset('js/admin/pages/products/form.js') }}"></script>
@endsection
