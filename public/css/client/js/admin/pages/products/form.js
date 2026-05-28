(() => {
    const dataNode = document.getElementById('product-form-data');

    if (!dataNode) {
        return;
    }

    const formData = JSON.parse(dataNode.textContent || '{}');
    const attributesByCategory = formData.attributesByCategory || {};
    const selectedAttributes = formData.selectedAttributes || {};
    const selectedVariants = formData.selectedVariants || [];
    const productTypeSelect = document.getElementById('product_type');
    const categorySelect = document.getElementById('category_id');
    const fieldsWrapper = document.getElementById('attribute_fields');
    const emptyState = document.getElementById('attribute_empty');
    const variantSection = document.getElementById('variant_section');
    const addVariantButton = document.getElementById('add_variant_btn');
    const variantHeader = document.getElementById('variant_header');
    const variantRows = document.getElementById('variant_rows');
    const generateVariantsButton = document.getElementById('generate_variants_btn');
    const productNameInput = document.getElementById('product_name_input');
    const imagesInput = document.getElementById('product_images');
    const selectedImages = document.getElementById('selected_images');
    let variantIndex = 0;
    const htmlEditors = [];

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
            column.className = 'form-field';

            const required = attribute.is_required ? 'required' : '';
            const requiredMark = attribute.is_required ? ' <span class="required-mark">*</span>' : '';

            if (attribute.values.length && attribute.type === 'select') {
                const rawSelectedValueIds = selected.attribute_value_ids || selected.attribute_value_id || [];
                const selectedValueIds = Array.isArray(rawSelectedValueIds)
                    ? rawSelectedValueIds.map(String)
                    : [String(rawSelectedValueIds)];
                const options = attribute.values.map((value) => {
                    const isChecked = selectedValueIds.includes(String(value.id)) ? 'checked' : '';

                    return `
                        <label class="check-pill">
                            <input type="checkbox" name="attributes[${attribute.id}][attribute_value_ids][]" value="${value.id}" ${isChecked}>
                            ${value.value}
                        </label>
                    `;
                }).join('');

                column.innerHTML = `
                    <label class="input-label">
                        ${attribute.name}${requiredMark}
                        <span class="tooltip-hint" tabindex="0" data-tooltip="Choose one or more values for ${attribute.name}.">?</span>
                    </label>
                    <div class="check-grid">${options}</div>
                `;
            } else if (attribute.type === 'boolean') {
                const value = String(selected.value || '');
                column.innerHTML = `
                    <label class="input-label">
                        ${attribute.name}${requiredMark}
                        <span class="tooltip-hint" tabindex="0" data-tooltip="Select yes or no for ${attribute.name}.">?</span>
                    </label>
                    <select name="attributes[${attribute.id}][value]" class="input-control" ${required}>
                        <option value="">Select ${attribute.name}</option>
                        <option value="1" ${value === '1' ? 'selected' : ''}>Yes</option>
                        <option value="0" ${value === '0' ? 'selected' : ''}>No</option>
                    </select>
                `;
            } else {
                const inputType = attribute.type === 'number' ? 'number' : 'text';
                column.innerHTML = `
                    <label class="input-label">
                        ${attribute.name}${requiredMark}
                        <span class="tooltip-hint" tabindex="0" data-tooltip="Enter the ${attribute.name} value for this product.">?</span>
                    </label>
                    <input type="${inputType}" name="attributes[${attribute.id}][value]" class="input-control" value="${selected.value || ''}" ${required}>
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
            <th width="90">Action</th>
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
                    <select name="variants[${rowIndex}][attributes][${attribute.id}]" class="input-control">
                        <option value="">Select ${attribute.name}</option>
                        ${options}
                    </select>
                </td>
            `;
        }).join('');

        row.innerHTML = `
            ${attributeCells}
            <td><input type="text" name="variants[${rowIndex}][sku]" class="input-control" value="${data.sku || ''}" placeholder="Variant SKU"></td>
            <td><input type="number" step="0.01" name="variants[${rowIndex}][price]" class="input-control" value="${data.price || ''}" placeholder="25900"></td>
            <td><input type="number" step="0.01" name="variants[${rowIndex}][sale_price]" class="input-control" value="${data.sale_price || ''}"></td>
            <td><input type="number" name="variants[${rowIndex}][stock]" class="input-control" value="${data.stock || 0}"></td>
            <td>
                <label class="check-pill">
                    <input type="checkbox" name="variants[${rowIndex}][in_stock]" value="1" ${(data.in_stock ?? true) ? 'checked' : ''}>
                    In Stock
                </label>
                <input type="hidden" name="variants[${rowIndex}][is_active]" value="1">
            </td>
            <td><button type="button" class="btn-danger-soft remove-variant">Remove</button></td>
        `;

        row.querySelector('.remove-variant').addEventListener('click', () => row.remove());
        variantRows.appendChild(row);
    }

    function resetVariantsForCategory() {
        variantRows.innerHTML = '';
        variantIndex = 0;
    }

    function slugPart(text) {
        return String(text || '')
            .toUpperCase()
            .replace(/[^A-Z0-9]+/g, '-')
            .replace(/^-|-$/g, '')
            .slice(0, 16);
    }

    function productSkuBase() {
        const raw = productNameInput && productNameInput.value ? productNameInput.value.trim() : '';

        const slug = slugPart(raw).slice(0, 24);

        return slug || 'VARIANT';
    }

    function valueLabelFor(attributeId, valueId) {
        const attrs = variantAttributes();
        const attr = attrs.find((a) => String(a.id) === String(attributeId));

        if (!attr) {
            return String(valueId);
        }

        const val = attr.values.find((v) => String(v.id) === String(valueId));

        return val ? val.value : String(valueId);
    }

    function getCheckedValuesByAttribute() {
        const attrs = variantAttributes();
        const result = {};

        attrs.forEach((attr) => {
            const checked = fieldsWrapper.querySelectorAll(
                `input[name="attributes[${attr.id}][attribute_value_ids][]"]:checked`,
            );

            result[attr.id] = Array.from(checked).map((el) => el.value);
        });

        return result;
    }

    function cartesianSelections(attrs, map) {
        let rows = [{}];

        attrs.forEach((attr) => {
            const vals = map[attr.id];

            if (!vals || !vals.length) {
                return;
            }

            const next = [];

            rows.forEach((row) => {
                vals.forEach((v) => {
                    next.push({ ...row, [attr.id]: v });
                });
            });

            rows = next;
        });

        return rows;
    }

    function generateVariantCombinations() {
        if (productTypeSelect.value !== 'configurable') {
            window.alert('Set product type to Configurable before generating variants.');

            return;
        }

        const attrs = variantAttributes();

        if (!attrs.length) {
            window.alert('Choose a category that has attributes with option lists (e.g. Capacity, Star rating).');

            return;
        }

        const map = getCheckedValuesByAttribute();
        const missing = attrs.filter((a) => !map[a.id] || map[a.id].length === 0);

        if (missing.length) {
            window.alert(
                `Tick at least one value under each option on the store:\n${missing.map((m) => m.name).join(', ')}`,
            );

            return;
        }

        const combos = cartesianSelections(attrs, map);

        if (!combos.length) {
            window.alert('No combinations to generate.');

            return;
        }

        if (combos.length > 120) {
            if (!window.confirm(`This will create ${combos.length} variant rows. Continue?`)) {
                return;
            }
        }

        resetVariantsForCategory();

        const base = productSkuBase();
        const used = new Set();

        combos.forEach((combo) => {
            const parts = attrs.map((attr) => slugPart(valueLabelFor(attr.id, combo[attr.id]))).filter(Boolean);
            let sku = [base, ...parts].join('-').slice(0, 80);
            let n = 1;

            while (used.has(sku)) {
                sku = `${base}-${n++}`.slice(0, 80);
            }

            used.add(sku);

            addVariantRow({
                attributes: combo,
                sku,
                price: '',
                stock: 0,
                in_stock: true,
            });
        });
    }

    function toggleVariantSection(clearRows = false) {
        const isConfigurable = productTypeSelect.value === 'configurable';

        variantSection.style.display = isConfigurable ? 'block' : 'none';

        if (generateVariantsButton) {
            generateVariantsButton.disabled = !isConfigurable;
            generateVariantsButton.style.opacity = isConfigurable ? '1' : '0.55';
        }

        if (!isConfigurable && clearRows) {
            variantRows.innerHTML = '';
            variantIndex = 0;
        }
    }

    function initImagePreview() {
        if (!imagesInput || !selectedImages) {
            return;
        }

        imagesInput.addEventListener('change', () => {
            selectedImages.innerHTML = '';

            Array.from(imagesInput.files || []).forEach((file) => {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    const chip = document.createElement('div');
                    chip.className = 'image-chip';
                    chip.innerHTML = `<img src="${event.target.result}" alt="${file.name}">`;
                    selectedImages.appendChild(chip);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    function initHtmlEditors() {
        if (window.ClassicEditor) {
            document.querySelectorAll('.js-html-editor').forEach((textarea) => {
                window.ClassicEditor
                    .create(textarea, {
                        toolbar: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'link',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'undo',
                            'redo',
                        ],
                    })
                    .then((editor) => {
                        htmlEditors.push({ textarea, editor });
                    })
                    .catch(() => {
                        initFallbackEditor(textarea);
                    });
            });

            syncEditorsOnSubmit();
            return;
        }

        document.querySelectorAll('.js-html-editor').forEach((textarea) => {
            initFallbackEditor(textarea);
        });

        syncEditorsOnSubmit();
    }

    function initFallbackEditor(textarea) {
        if (textarea.dataset.editorReady === 'true') {
            return;
        }

        textarea.dataset.editorReady = 'true';
        const wrapper = document.createElement('div');
        const toolbar = document.createElement('div');
        const editor = document.createElement('div');

        textarea.classList.add('html-editor-source');
        toolbar.className = 'editor-toolbar';
        editor.className = 'html-editor-surface';
        editor.contentEditable = 'true';
        editor.innerHTML = textarea.value || '';

        [
            ['bold', 'B'],
            ['italic', 'I'],
            ['insertUnorderedList', 'UL'],
            ['insertOrderedList', 'OL'],
            ['formatBlock', 'H3', 'h3'],
            ['formatBlock', 'P', 'p'],
        ].forEach(([command, label, value]) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'editor-btn';
            button.textContent = label;
            button.addEventListener('click', () => {
                editor.focus();
                document.execCommand(command, false, value || null);
                textarea.value = editor.innerHTML;
            });
            toolbar.appendChild(button);
        });

        editor.addEventListener('input', () => {
            textarea.value = editor.innerHTML;
        });

        textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);
        wrapper.appendChild(toolbar);
        wrapper.appendChild(editor);
    }

    function syncEditorsOnSubmit() {
        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', () => {
                htmlEditors.forEach(({ textarea, editor }) => {
                    textarea.value = editor.getData();
                });

                form.querySelectorAll('.js-html-editor').forEach((textarea) => {
                    const editor = textarea.nextElementSibling?.querySelector('.html-editor-surface');

                    if (editor) {
                        textarea.value = editor.innerHTML;
                    }
                });
            });
        });
    }

    categorySelect.addEventListener('change', () => {
        renderAttributes();
        resetVariantsForCategory();
    });
    addVariantButton.addEventListener('click', () => addVariantRow());

    if (generateVariantsButton) {
        generateVariantsButton.addEventListener('click', () => generateVariantCombinations());
    }

    productTypeSelect.addEventListener('change', () => toggleVariantSection(true));

    initHtmlEditors();
    initImagePreview();
    renderAttributes();

    if (productTypeSelect.value === 'configurable') {
        selectedVariants.forEach((variant) => addVariantRow(variant));
    }

    toggleVariantSection();
})();
