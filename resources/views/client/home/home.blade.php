@extends('client.layouts.app')

@section('title', 'Go Sowa Herbal Tea')



@section('content')


@include('client.home.components.hero')
@include('client.home.components.category')

@foreach($labelProductCarousels as $labelCarousel)
    @include('client.home.components.product-carousel', [
        'carouselProducts' => $labelCarousel['products'],
        'carouselLimit' => 8,
        'carouselEyebrow' => 'Tanvi Picks',
        'carouselTitle' => $labelCarousel['title'] === 'New Arrived' ? 'Our New Arrived Product' : 'Our Top Product',
        'carouselViewAllUrl' => route('labels.show', str_replace('label-', '', $labelCarousel['key'])),
    ])
@endforeach
@include('client.home.components.offer-banner')
@include('client.home.components.trust')
@include('client.home.components.term')

@include('client.home.components.popup')


@endsection

@push('scripts')
<script>
    (() => {
        const popup = document.querySelector('[data-home-scroll-popup]');

        if (!popup) {
            return;
        }

        const storageKey = 'gosowa_home_scroll_popup_seen';
        const showOnce = popup.dataset.showOnce === '1';

        if (showOnce && sessionStorage.getItem(storageKey) === '1') {
            return;
        }

        let timer = null;
        let opened = false;
        const delay = Math.max(1, parseInt(popup.dataset.delay || '10', 10)) * 1000;

        const openPopup = () => {
            if (opened) {
                return;
            }

            opened = true;
            popup.hidden = false;
            requestAnimationFrame(() => popup.classList.add('is-open'));

            if (showOnce) {
                sessionStorage.setItem(storageKey, '1');
            }
        };

        const closePopup = () => {
            popup.classList.remove('is-open');
            setTimeout(() => {
                popup.hidden = true;
            }, 280);
        };

        const startScrollTimer = () => {
            if (timer || opened || window.scrollY < 80) {
                return;
            }

            timer = window.setTimeout(openPopup, delay);
        };

        window.addEventListener('scroll', startScrollTimer, { passive: true });
        popup.querySelectorAll('[data-home-popup-close]').forEach((item) => {
            item.addEventListener('click', closePopup);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !popup.hidden) {
                closePopup();
            }
        });
    })();

    (() => {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        const money = (value) => '₹ ' + Number(value || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const navbarMoney = (value) => Number(value || 0).toLocaleString('en-IN', {
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
                    if (Number.isFinite(value)) {
                        normalized[key] = value;
                    }
                });

            return JSON.stringify(normalized);
        };

        const selectedState = (card) => {
            const pavIds = [];
            const attributeMap = {};

            card.querySelectorAll('[data-attribute-option].active').forEach((button) => {
                const pavId = parseInt(button.dataset.pavId, 10);
                const attributeId = parseInt(button.dataset.attributeId, 10);
                const attributeValueId = parseInt(button.dataset.attributeValueId, 10);

                if (Number.isFinite(pavId)) {
                    pavIds.push(pavId);
                }
                if (Number.isFinite(attributeId) && Number.isFinite(attributeValueId)) {
                    attributeMap[attributeId] = attributeValueId;
                }
            });

            return { pavIds, attributeMap };
        };

        const pricingFor = (card) => {
            try {
                return JSON.parse(card.dataset.pricing || '{}');
            } catch (error) {
                return {};
            }
        };

        const matchingVariant = (pricing, attributeMap, expectedGroupCount) => {
            if (!pricing.variants?.length || Object.keys(attributeMap).length !== expectedGroupCount) {
                return null;
            }

            const selected = normalizeMap(attributeMap);

            return pricing.variants.find((variant) => normalizeMap(variant.attributes || {}) === selected) || null;
        };

        const minOrderQty = (card) => {
            const min = parseInt(card.dataset.minOrderQty || '1', 10);
            return Number.isFinite(min) && min > 0 ? min : 1;
        };

        const normalizeCardQuantity = (card) => {
            const input = card.querySelector('[data-product-card-quantity]');
            const min = minOrderQty(card);

            if (!input) {
                return min;
            }

            const quantity = Math.max(min, parseInt(input.value || String(min), 10) || min);
            input.min = String(min);
            input.value = String(quantity);

            return quantity;
        };

        const updateCardPrice = (card) => {
            const pricing = pricingFor(card);
            const state = selectedState(card);
            const groupCount = card.querySelectorAll('[data-attribute-group]').length;
            const variant = matchingVariant(pricing, state.attributeMap, groupCount);
            const addButton = card.querySelector('[data-add-home-cart]');

            let finalPrice = Number(pricing.base_final_price || 0);
            let listPrice = Number(pricing.base_list_price || finalPrice);
            let variantId = '';
            let available = true;

            if (variant) {
                finalPrice = Number(variant.final_price || finalPrice);
                listPrice = Number(variant.list_price || listPrice || finalPrice);
                variantId = variant.id || '';
                available = variant.in_stock !== false;
            } else if (pricing.type === 'configurable' && pricing.variants?.length && groupCount > 0) {
                available = false;
            }

            const finalEl = card.querySelector('[data-product-final-price]');
            const listEl = card.querySelector('[data-product-list-price]');

            if (finalEl) {
                finalEl.textContent = money(finalPrice);
            }
            if (listEl) {
                listEl.textContent = money(listPrice);
                listEl.hidden = !(listPrice > finalPrice);
            }

            card.dataset.selectedVariantId = variantId;
            if (addButton) {
                addButton.disabled = !available;
                addButton.innerHTML = available
                    ? '<i class="ti ti-shopping-bag-plus"></i>'
                    : '<span>Not Available</span>';
            }
        };

        const updateCartNavbar = (cart) => {
            document.querySelectorAll('[data-navbar-cart-count]').forEach((item) => {
                item.textContent = cart?.total_quantity ?? item.textContent;
            });
            document.querySelectorAll('[data-navbar-cart-total]').forEach((item) => {
                item.textContent = navbarMoney(cart?.grand_total ?? cart?.subtotal ?? 0);
            });
        };

        const updateWishlistNavbar = (count) => {
            document.querySelectorAll('[data-navbar-wishlist-count]').forEach((item) => {
                item.textContent = count ?? item.textContent;
            });
        };

        document.querySelectorAll('[data-home-product-card]').forEach((card) => {
            updateCardPrice(card);
            normalizeCardQuantity(card);

            card.querySelector('[data-product-qty-decrease]')?.addEventListener('click', () => {
                const input = card.querySelector('[data-product-card-quantity]');
                if (!input) {
                    return;
                }

                input.value = String(normalizeCardQuantity(card) - 1);
                normalizeCardQuantity(card);
            });

            card.querySelector('[data-product-qty-increase]')?.addEventListener('click', () => {
                const input = card.querySelector('[data-product-card-quantity]');
                if (!input) {
                    return;
                }

                input.value = String(normalizeCardQuantity(card) + 1);
                normalizeCardQuantity(card);
            });

            card.querySelector('[data-product-card-quantity]')?.addEventListener('change', () => {
                normalizeCardQuantity(card);
            });

            card.querySelector('[data-product-card-quantity]')?.addEventListener('blur', () => {
                normalizeCardQuantity(card);
            });

            card.querySelectorAll('[data-attribute-option]').forEach((button) => {
                button.addEventListener('click', () => {
                    const group = button.closest('[data-attribute-group]');
                    group?.querySelectorAll('[data-attribute-option]').forEach((option) => {
                        option.classList.toggle('active', option === button);
                    });
                    updateCardPrice(card);
                });
            });

            card.querySelector('[data-add-home-cart]')?.addEventListener('click', async (event) => {
                const button = event.currentTarget;
                const original = button.innerHTML;
                const productId = parseInt(card.dataset.productId, 10);
                const state = selectedState(card);
                const quantity = normalizeCardQuantity(card);
                const minimumOrderQty = minOrderQty(card);

                if (quantity < minimumOrderQty) {
                    window.alert(`Minimum order quantity for this product is ${minimumOrderQty}.`);
                    return;
                }

                button.innerHTML = '<i class="ti ti-loader-2"></i>';
                button.disabled = true;

                try {
                    const response = await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity,
                            product_variant_id: card.dataset.selectedVariantId || null,
                            selected_product_attribute_value_ids: state.pavIds,
                        }),
                    });
                    const data = await response.json();

                    if (!response.ok || !data.status) {
                        throw new Error(data.message || 'Could not add item.');
                    }

                    updateCartNavbar(data.cart);
                    button.innerHTML = '<i class="ti ti-check"></i>';
                    setTimeout(() => { button.innerHTML = original; }, 1000);
                } catch (error) {
                    window.alert(error.message || 'Could not add item.');
                    button.innerHTML = original;
                } finally {
                    setTimeout(() => {
                        button.disabled = false;
                        updateCardPrice(card);
                    }, 800);
                }
            });

            card.querySelector('[data-home-wishlist]')?.addEventListener('click', async (event) => {
                const button = event.currentTarget;
                const productId = parseInt(card.dataset.productId, 10);
                const state = selectedState(card);

                button.disabled = true;

                try {
                    const response = await fetch(`/wishlist/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({
                            product_variant_id: card.dataset.selectedVariantId || null,
                            selected_product_attribute_value_ids: state.pavIds,
                        }),
                    });
                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Could not update wishlist.');
                    }

                    button.classList.toggle('is-active', data.added);
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.className = 'ti ti-heart';
                    }
                    updateWishlistNavbar(data.count);
                } catch (error) {
                    window.alert(error.message || 'Could not update wishlist.');
                } finally {
                    button.disabled = false;
                }
            });
        });
    })();
</script>
@endpush
