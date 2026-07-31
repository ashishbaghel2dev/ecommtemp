<script>
    (() => {
        const form = document.getElementById('categoryFilterForm');
        const filterPanel = document.querySelector('.category-filter-panel');
        const mobileFilterButton = document.querySelector('[data-mobile-filter]');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        document.querySelectorAll('[data-filter-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                button.closest('.category-filter-group')?.classList.toggle('is-collapsed');
            });
        });

        mobileFilterButton?.addEventListener('click', () => {
            filterPanel?.classList.toggle('is-open');
        });

        document.addEventListener('click', (event) => {
            if (!filterPanel || !mobileFilterButton) return;
            if (window.innerWidth > 900) return;
            if (filterPanel.contains(event.target) || mobileFilterButton.contains(event.target)) return;
            filterPanel.classList.remove('is-open');
        });

        const money = (value) => '₹ ' + Number(value || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const normalizeMap = (map) => {
            const normalized = {};
            Object.keys(map || {})
                .map((key) => parseInt(key, 10))
                .filter(Number.isFinite)
                .sort((a, b) => a - b)
                .forEach((key) => {
                    const value = parseInt(map[key], 10);
                    if (Number.isFinite(value)) normalized[key] = value;
                });
            return JSON.stringify(normalized);
        };

        const readPricing = (card) => {
            try {
                return JSON.parse(card.dataset.pricing || '{}');
            } catch (error) {
                return {};
            }
        };

        const selectedAttributeMap = (card) => {
            const map = {};
            card.querySelectorAll('[data-size-option].is-selected').forEach((button) => {
                const attributeId = parseInt(button.dataset.attributeId, 10);
                const attributeValueId = parseInt(button.dataset.attributeValueId, 10);
                if (Number.isFinite(attributeId) && Number.isFinite(attributeValueId)) {
                    map[attributeId] = attributeValueId;
                }
            });
            return map;
        };

        const matchingVariant = (pricing, attributeMap, expectedGroupCount) => {
            if (!pricing.variants?.length || Object.keys(attributeMap).length !== expectedGroupCount) return null;
            const selected = normalizeMap(attributeMap);
            return pricing.variants.find((variant) => normalizeMap(variant.attributes || {}) === selected) || null;
        };

        const selectedPavIds = (card) => Array.from(card.querySelectorAll('[data-size-option].is-selected'))
            .map((button) => parseInt(button.dataset.pavId, 10))
            .filter(Number.isFinite);

        const updateCategoryCardPrice = (card) => {
            const pricing = readPricing(card);
            const groups = card.querySelectorAll('[data-category-attribute-group]');
            const variant = matchingVariant(pricing, selectedAttributeMap(card), groups.length);
            const finalEl = card.querySelector('[data-category-final-price]');
            const listEl = card.querySelector('[data-category-list-price]');

            let finalPrice = Number(pricing.base_final_price || 0);
            let listPrice = Number(pricing.base_list_price || finalPrice);

            if (variant) {
                finalPrice = Number(variant.final_price || finalPrice);
                listPrice = Number(variant.list_price || listPrice || finalPrice);
            }

            if (finalEl) finalEl.textContent = money(finalPrice);
            if (listEl) {
                listEl.textContent = money(listPrice);
                listEl.hidden = !(listPrice > finalPrice);
            }
        };

        document.querySelectorAll('[data-category-product-card]').forEach((card) => {
            updateCategoryCardPrice(card);
            card.querySelectorAll('[data-size-option]').forEach((button) => {
                button.addEventListener('click', () => {
                    button.closest('[data-category-attribute-group]')?.querySelectorAll('[data-size-option]').forEach((item) => {
                        item.classList.toggle('is-selected', item === button);
                    });
                    updateCategoryCardPrice(card);
                });
            });
        });

        document.querySelectorAll('.option-filter-item input, .stock-filter input').forEach((input) => {
            input.addEventListener('change', () => form?.submit());
        });

        document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
            button.addEventListener('click', async () => {
                const productId = button.dataset.addToCart;
                const card = button.closest('[data-category-product-card]');
                const pricing = card ? readPricing(card) : {};
                const groups = card ? card.querySelectorAll('[data-category-attribute-group]') : [];
                const variant = card ? matchingVariant(pricing, selectedAttributeMap(card), groups.length) : null;
                const body = {
                    product_id: productId,
                    quantity: 1,
                    selected_product_attribute_value_ids: card ? selectedPavIds(card) : [],
                };

                if (variant?.id) {
                    body.product_variant_id = variant.id;
                }

                button.disabled = true;
                try {
                    const response = await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(body),
                    });
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.status !== false) {
                        button.classList.add('is-added');
                        button.innerHTML = '<i class="ti ti-check"></i> Added';
                        window.updateNavbarCartSummary?.(data.cart);
                    } else {
                        if (!window.AppToast) alert(data.message || 'Could not add product to cart');
                    }
                } catch (error) {
                    if (!window.AppToast) alert('Network error. Please try again.');
                } finally {
                    window.setTimeout(() => {
                        button.disabled = false;
                        button.classList.remove('is-added');
                        button.innerHTML = 'ADD TO CART';
                    }, 1400);
                }
            });
        });

        document.querySelectorAll('[data-wishlist]').forEach((button) => {
            button.addEventListener('click', async () => {
                try {
                    const response = await fetch(`/wishlist/${button.dataset.wishlist}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.success !== false) {
                        button.classList.toggle('is-active', Boolean(data.added));
                        window.updateNavbarWishlistCount?.(data.count);
                    }
                } catch (error) {
                    button.classList.remove('is-active');
                }
            });
        });
    })();
</script>
