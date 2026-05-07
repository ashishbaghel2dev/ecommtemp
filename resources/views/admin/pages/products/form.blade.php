@php
    $selectedCategory = old('category_id', $product->category_id ?? '');
    $selectedLabels = old(
        'labels',
        $product ? $product->labels->pluck('id')->map(fn ($id) => (string) $id)->all() : []
    );
@endphp

<div class="container">
    <h2 class="mb-4">{{ $product ? 'Edit Product' : 'Create Product' }}</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            Please fix the highlighted fields and try again.
        </div>
    @endif

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if($method !== 'POST')
            @method($method)
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required>
                @error('sku') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
                @error('price') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Discount Price</label>
                <input type="number" step="0.01" name="discount_price" class="form-control" value="{{ old('discount_price', $product->discount_price ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Sale Price</label>
                <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price ?? '') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Sale Start</label>
                <input type="datetime-local" name="sale_start" class="form-control" value="{{ old('sale_start', optional($product?->sale_start ?? null)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Sale End</label>
                <input type="datetime-local" name="sale_end" class="form-control" value="{{ old('sale_end', optional($product?->sale_end ?? null)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select Category</option>

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) $selectedCategory === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->parent ? $category->parent->name . ' - ' : '' }}{{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Labels</label>
                <select name="labels[]" class="form-control" multiple>
                    @foreach($labels as $label)
                        <option value="{{ $label->id }}" {{ in_array((string) $label->id, $selectedLabels, true) ? 'selected' : '' }}>
                            {{ $label->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" class="form-control">

                @if($product?->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="mt-2" width="90" height="90" style="object-fit: cover;">
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Product Type</label>
                <select name="type" id="product_type" class="form-control">
                    <option value="simple" {{ old('type', $product->type ?? 'simple') === 'simple' ? 'selected' : '' }}>Simple</option>
                    <option value="configurable" {{ old('type', $product->type ?? '') === 'configurable' ? 'selected' : '' }}>Configurable</option>
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <h5>Category Attributes</h5>
                <div id="attribute_fields" class="row"></div>
                <p id="attribute_empty" class="text-muted mb-0">Select a category to load attributes.</p>
            </div>

            <div class="col-md-12 mb-3" id="variant_section">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Variant Pricing</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add_variant_btn">Add Variant</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr id="variant_header">
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody id="variant_rows"></tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Short Description</label>
                <textarea name="short_description" class="form-control" rows="3">{{ old('short_description', $product->short_description ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="6">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product->meta_title ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Meta Description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $product->meta_description ?? '') }}">
            </div>

            <div class="col-md-12 mb-3">
                <label class="me-3">
                    <input type="checkbox" name="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock ?? true) ? 'checked' : '' }}>
                    Manage Stock
                </label>

                <label class="me-3">
                    <input type="checkbox" name="in_stock" value="1" {{ old('in_stock', $product->in_stock ?? true) ? 'checked' : '' }}>
                    In Stock
                </label>

                <label class="me-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    Active
                </label>

                <label>
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                    Featured
                </label>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">{{ $buttonText }}</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const attributesByCategory = @json($attributesByCategory);
    const selectedAttributes = @json($selectedAttributes);
    const selectedVariants = @json($selectedVariants ?? []);
    const productTypeSelect = document.getElementById('product_type');
    const categorySelect = document.getElementById('category_id');
    const fieldsWrapper = document.getElementById('attribute_fields');
    const emptyState = document.getElementById('attribute_empty');
    const variantSection = document.getElementById('variant_section');
    const addVariantButton = document.getElementById('add_variant_btn');
    const variantHeader = document.getElementById('variant_header');
    const variantRows = document.getElementById('variant_rows');
    let variantIndex = 0;

    function selectedData(attributeId) {
        return selectedAttributes[attributeId] || selectedAttributes[String(attributeId)] || {};
    }

    function renderAttributes() {
        const categoryId = categorySelect.value;
        const attributes = attributesByCategory[categoryId] || [];

        fieldsWrapper.innerHTML = '';
        emptyState.style.display = attributes.length ? 'none' : 'block';
        emptyState.textContent = categoryId ? 'No attributes found for this category.' : 'Select a category to load attributes.';

        attributes.forEach((attribute) => {
            const selected = selectedData(attribute.id);
            const column = document.createElement('div');
            column.className = 'col-md-6 mb-3';

            const required = attribute.is_required ? 'required' : '';
            const requiredMark = attribute.is_required ? ' *' : '';

            if (attribute.values.length && attribute.type === 'select') {
                const rawSelectedValueIds = selected.attribute_value_ids || selected.attribute_value_id || [];
                const selectedValueIds = Array.isArray(rawSelectedValueIds)
                    ? rawSelectedValueIds.map(String)
                    : [String(rawSelectedValueIds)];
                const options = attribute.values.map((value) => {
                    const isChecked = selectedValueIds.includes(String(value.id)) ? 'checked' : '';
                    return `
                        <label class="me-3 mb-2">
                            <input type="checkbox" name="attributes[${attribute.id}][attribute_value_ids][]" value="${value.id}" ${isChecked}>
                            ${value.value}
                        </label>
                    `;
                }).join('');

                column.innerHTML = `
                    <label class="form-label">${attribute.name}${requiredMark}</label>
                    <div class="border rounded p-2">${options}</div>
                `;
            } else if (attribute.type === 'boolean') {
                const value = String(selected.value || '');
                column.innerHTML = `
                    <label class="form-label">${attribute.name}${requiredMark}</label>
                    <select name="attributes[${attribute.id}][value]" class="form-control" ${required}>
                        <option value="">Select ${attribute.name}</option>
                        <option value="1" ${value === '1' ? 'selected' : ''}>Yes</option>
                        <option value="0" ${value === '0' ? 'selected' : ''}>No</option>
                    </select>
                `;
            } else {
                const inputType = attribute.type === 'number' ? 'number' : 'text';
                column.innerHTML = `
                    <label class="form-label">${attribute.name}${requiredMark}</label>
                    <input type="${inputType}" name="attributes[${attribute.id}][value]" class="form-control" value="${selected.value || ''}" ${required}>
                `;
            }

            fieldsWrapper.appendChild(column);
        });

        renderVariantHeader(attributes);
    }

    function variantAttributes() {
        const categoryId = categorySelect.value;
        return (attributesByCategory[categoryId] || []).filter((attribute) => attribute.values.length);
    }

    function renderVariantHeader(attributes) {
        const variantAttrs = attributes.filter((attribute) => attribute.values.length);
        variantHeader.innerHTML = `
            ${variantAttrs.map((attribute) => `<th>${attribute.name}</th>`).join('')}
            <th>SKU</th>
            <th>Price</th>
            <th>Sale Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th width="80">Action</th>
        `;
    }

    function addVariantRow(data = {}) {
        const attrs = variantAttributes();
        const rowIndex = variantIndex++;
        const row = document.createElement('tr');

        const attributeCells = attrs.map((attribute) => {
            const selectedValue = data.attributes ? data.attributes[attribute.id] : '';
            const options = attribute.values.map((value) => {
                const selected = String(selectedValue || '') === String(value.id) ? 'selected' : '';
                return `<option value="${value.id}" ${selected}>${value.value}</option>`;
            }).join('');

            return `
                <td>
                    <select name="variants[${rowIndex}][attributes][${attribute.id}]" class="form-control">
                        <option value="">Select ${attribute.name}</option>
                        ${options}
                    </select>
                </td>
            `;
        }).join('');

        row.innerHTML = `
            ${attributeCells}
            <td><input type="text" name="variants[${rowIndex}][sku]" class="form-control" value="${data.sku || ''}" placeholder="Variant SKU"></td>
            <td><input type="number" step="0.01" name="variants[${rowIndex}][price]" class="form-control" value="${data.price || ''}" placeholder="25900"></td>
            <td><input type="number" step="0.01" name="variants[${rowIndex}][sale_price]" class="form-control" value="${data.sale_price || ''}"></td>
            <td><input type="number" name="variants[${rowIndex}][stock]" class="form-control" value="${data.stock || 0}"></td>
            <td>
                <label>
                    <input type="checkbox" name="variants[${rowIndex}][in_stock]" value="1" ${(data.in_stock ?? true) ? 'checked' : ''}>
                    In Stock
                </label>
                <input type="hidden" name="variants[${rowIndex}][is_active]" value="1">
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button></td>
        `;

        row.querySelector('.remove-variant').addEventListener('click', () => row.remove());
        variantRows.appendChild(row);
    }

    function resetVariantsForCategory() {
        variantRows.innerHTML = '';
        variantIndex = 0;
    }

    function toggleVariantSection(clearRows = false) {
        const isConfigurable = productTypeSelect.value === 'configurable';
        variantSection.style.display = isConfigurable ? 'block' : 'none';

        if (!isConfigurable && clearRows) {
            variantRows.innerHTML = '';
        }
    }

    categorySelect.addEventListener('change', () => {
        renderAttributes();
        resetVariantsForCategory();
    });
    addVariantButton.addEventListener('click', () => addVariantRow());
    productTypeSelect.addEventListener('change', () => toggleVariantSection(true));
    renderAttributes();
    if (productTypeSelect.value === 'configurable') {
        selectedVariants.forEach((variant) => addVariantRow(variant));
    }
    toggleVariantSection();
</script>
