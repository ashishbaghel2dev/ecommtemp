<section class="recently-viewed-section">



    <div class="product-grid">

        @foreach($products as $product)

            @php
                $pricingPayload = [
                    'baseFinal' => (float) $product->final_price,
                    'baseList' => (float) ($product->price ?? 0),
                    'variants' => [],
                ];
                if ($product->type === 'configurable' && $product->variants->isNotEmpty()) {
                    $pricingPayload['variants'] = $product->variants->map(function ($v) {
                        return [
                            'id' => $v->id,
                            'price' => (float) $v->getFinalPriceAttribute(),
                            'attributes' => is_array($v->attributes) ? $v->attributes : [],
                        ];
                    })->values()->all();
                }
                $strikeList = max(
                    (float) ($product->price ?? 0),
                    (float) $product->final_price * 1.08
                );
            @endphp

            <div class="product-card" data-id="{{ $product->id }}" data-pricing='@json($pricingPayload)'>

                <!-- CATEGORY BADGE -->
                <div class="product-badge">
                    {{ $product->category->name ?? 'Category' }}
                </div>

                <!-- PRODUCT IMAGE -->
                <div class="product-image-wrapper">

                    <img 
                        src="{{ asset($product->image ?? 'images/no-image.png') }}"
                        alt="{{ $product->name }}"
                        class="product-image"
                    >

                </div>

                <!-- CONTENT -->
                <div class="product-content">

                    <!-- TITLE -->
                    <h3 class="product-title">
                        {{ $product->name }}
                    </h3>

                    <!-- SHORT DESCRIPTION -->
                    <p class="product-description">
                        {{ Str::limit($product->short_description ?? 'Premium quality product with modern design and excellent performance.', 80) }}
                    </p>

                    <!-- ATTRIBUTES -->
                    <div class="attributes">
                        @php
                            $groupedAttributes = $product->attributeValues->groupBy('attribute_id');
                        @endphp

                        @if($product->type === 'configurable' && $product->variants->count() > 0 && $groupedAttributes->isEmpty())
                            <div class="attribute-item">
                                <span class="label">Variant</span>
                                <select class="value product-variant-select" id="variant-select-{{ $product->id }}" onchange="updateProductPrice({{ $product->id }})">
                                    @foreach($product->variants as $variant)
                                        @php
                                            $variantName = [];
                                            if (isset($variantAttributes) && isset($variantValues) && is_array($variant->attributes)) {
                                                foreach($variant->attributes as $attrId => $valId) {
                                                    $attrName = $variantAttributes[$attrId] ?? 'Attr';
                                                    $valValue = $variantValues[$valId] ?? 'Val';
                                                    $variantName[] = "$attrName: $valValue";
                                                }
                                            }
                                            $displayName = implode(', ', $variantName) ?: ($variant->sku ?: 'Default Variant');
                                        @endphp
                                        <option value="{{ $variant->id }}"
                                                data-price="{{ $variant->getFinalPriceAttribute() }}"
                                                data-sku="{{ $variant->sku }}">
                                            {{ $displayName }} (₹{{ number_format($variant->getFinalPriceAttribute(), 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @foreach($groupedAttributes as $attrId => $pavs)
                            <div class="attribute-selection" data-attribute-id="{{ $attrId }}">
                                <p class="label" style="margin-bottom: 5px; font-weight: 600;">{{ $pavs->first()->attribute->name ?? 'Option' }}:</p>
                                <div class="options-group" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;">
                                    @foreach($pavs as $pav)
                                        <label class="option-item">
                                            <input type="checkbox"
                                                   name="pav-{{ $product->id }}-{{ $attrId }}"
                                                   value="{{ $pav->id }}"
                                                   class="attr-pav-checkbox"
                                                   data-attribute-id="{{ $pav->attribute_id }}"
                                                   data-attribute-value-id="{{ $pav->attribute_value_id }}"
                                                   style="display: none;">
                                            <span class="option-label">{{ $pav->attributeValue ? $pav->attributeValue->value : ($pav->value ?? '-') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="attribute-item">
                            <span class="label">Stock</span>
                            <span class="value stock">
                                {{ $product->stock ?? 0 }} Left
                            </span>
                        </div>

                    </div>

                    <!-- PRICE AREA -->
                    <div class="price-area">

                        <div>
                            <span class="old-price js-old-price">
                                ₹{{ number_format($strikeList, 2) }}
                            </span>

                            <div class="product-price js-product-price">
                                ₹{{ number_format($product->final_price, 2) }}
                            </div>
                        </div>

                        <div class="discount-badge">
                            20% OFF
                        </div>

                    </div>

                    <!-- BUTTONS -->
                    <div class="button-group">
<button onclick="handleAddToCart({{ $product->id }})">
    Add to Cart
</button>

   <button
    class="wishlist-btn"
    data-product="{{ $product->id }}"
>
    ❤️ Wishlist
</button>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</section>

<style>

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.recently-viewed-section{
    width: 92%;
    margin: 60px auto;
}

/* HEADER */

.section-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 15px;
}

.section-header h2{
    font-size: 34px;
    font-weight: 700;
    color: #111;
    margin-bottom: 8px;
}

.section-header p{
    color: #777;
    font-size: 15px;
}

.view-all-btn{
    padding: 12px 22px;
    background: #111;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.view-all-btn:hover{
    background: #e63946;
}

/* GRID */

.product-grid{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 28px;
}

/* CARD */

.product-card{
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    border: 1px solid #eee;
    box-shadow: 0 5px 25px rgba(0,0,0,0.06);
    transition: 0.35s ease;
}

.product-card:hover{
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}

/* BADGE */

.product-badge{
    position: absolute;
    top: 15px;
    left: 15px;
    background: #e63946;
    color: #fff;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    z-index: 2;
}

/* IMAGE */

.product-image-wrapper{
    width: 100%;
    height: 260px;
    background: #f8f8f8;
    overflow: hidden;
}

.product-image{
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.5s ease;
}

.product-card:hover .product-image{
    transform: scale(1.08);
}

/* CONTENT */

.product-content{
    padding: 22px;
}

.product-title{
    font-size: 20px;
    font-weight: 700;
    color: #111;
    margin-bottom: 10px;
    line-height: 1.4;
}

.product-description{
    font-size: 14px;
    color: #666;
    line-height: 1.7;
    margin-bottom: 18px;
}

/* ATTRIBUTES */

.attributes{
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.attribute-item{
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7f7f7;
    padding: 10px 14px;
    border-radius: 10px;
}

.label{
    color: #666;
    font-size: 14px;
}

.value{
    font-weight: 600;
    color: #111;
}

.stock{
    color: green;
}

/* PRICE */

.price-area{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.old-price{
    color: #999;
    text-decoration: line-through;
    font-size: 14px;
}

.product-price{
    font-size: 26px;
    font-weight: 700;
    color: #e63946;
    margin-top: 3px;
}

.discount-badge{
    background: #111;
    color: #fff;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
}

/* BUTTONS */

.button-group{
    display: flex;
    gap: 12px;
}

.cart-btn{
    flex: 1;
    padding: 13px;
    border: none;
    border-radius: 12px;
    background: #111;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.cart-btn:hover{
    background: #e63946;
}

.wishlist-btn{
    width: 55px;
    border: none;
    border-radius: 12px;
    background: #f3f3f3;
    font-size: 18px;
    cursor: pointer;
    transition: 0.3s;
}

.wishlist-btn:hover{
    background: #e63946;
    color: #fff;
    }

    .option-item {
        cursor: pointer;
        padding: 5px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        transition: 0.3s;
}

    .option-item:has(input:checked) {
        background: #e63946;
        color: #fff;
        border-color: #e63946;
    }

/* MOBILE */

@media(max-width:768px){

    .section-header{
        flex-direction: column;
        align-items: flex-start;
    }

    .section-header h2{
        font-size: 28px;
    }

    .product-image-wrapper{
        height: 220px;
    }

}

</style>

<script>
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            addToCart(this.dataset.id);
        });
    });

    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('attr-pav-checkbox')) {
            return;
        }
        const card = e.target.closest('.product-card');
        if (!card) {
            return;
        }
        const group = e.target.closest('.attribute-selection');
        if (group && e.target.checked) {
            group.querySelectorAll('.attr-pav-checkbox').forEach((cb) => {
                if (cb !== e.target) {
                    cb.checked = false;
                }
            });
        }
        const productId = parseInt(card.getAttribute('data-id'), 10);
        if (Number.isFinite(productId)) {
            updatePriceForProductCard(productId);
        }
    });

    function readPricing(card) {
        const raw = card.getAttribute('data-pricing');
        if (!raw) {
            return { baseFinal: 0, baseList: 0, variants: [] };
        }
        try {
            return JSON.parse(raw);
        } catch (err) {
            return { baseFinal: 0, baseList: 0, variants: [] };
        }
    }

    function formatInr(n) {
        return '₹' + Number(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function normAttrMap(map) {
        const o = {};
        if (!map || typeof map !== 'object') {
            return '{}';
        }
        Object.keys(map).forEach((k) => {
            const kk = parseInt(String(k), 10);
            const vv = parseInt(String(map[k]), 10);
            if (Number.isFinite(kk) && Number.isFinite(vv)) {
                o[kk] = vv;
            }
        });
        const keys = Object.keys(o)
            .map((x) => parseInt(x, 10))
            .sort((a, b) => a - b);
        const sorted = {};
        keys.forEach((k) => {
            sorted[k] = o[k];
        });
        return JSON.stringify(sorted);
    }

    function selectedAttributeValueMap(card) {
        const map = {};
        card.querySelectorAll('.attr-pav-checkbox:checked').forEach((cb) => {
            const aid = cb.getAttribute('data-attribute-id');
            const vid = cb.getAttribute('data-attribute-value-id');
            if (!aid || !vid || vid === '') {
                return;
            }
            map[aid] = vid;
        });
        return map;
    }

    function findVariantPrice(pricing, attrMap) {
        const target = normAttrMap(attrMap);
        const list = pricing.variants || [];
        for (let i = 0; i < list.length; i += 1) {
            if (normAttrMap(list[i].attributes) === target) {
                return list[i].price;
            }
        }
        return null;
    }

    function updatePriceForProductCard(productId) {
        const card = document.querySelector('.product-card[data-id="' + productId + '"]');
        if (!card) {
            return;
        }
        const pricing = readPricing(card);
        const priceEl = card.querySelector('.js-product-price');
        const oldEl = card.querySelector('.js-old-price');
        const variantSelect = card.querySelector('.product-variant-select');

        let final = pricing.baseFinal;
        let list = Math.max(pricing.baseList || 0, final * 1.08);
        if (list <= final) {
            list = final * 1.12;
        }

        if (variantSelect) {
            const opt = variantSelect.selectedOptions[0];
            if (opt && opt.dataset.price !== undefined && opt.dataset.price !== '') {
                final = Number(opt.dataset.price);
                list = Math.max(pricing.baseList || 0, final * 1.08);
                if (list <= final) {
                    list = final * 1.12;
                }
            }
        } else {
            const groups = card.querySelectorAll('.attribute-selection');
            const map = selectedAttributeValueMap(card);
            const selectedKeys = Object.keys(map);
            if (groups.length > 0 && selectedKeys.length === groups.length) {
                const vp = findVariantPrice(pricing, map);
                if (vp !== null && !Number.isNaN(vp)) {
                    final = vp;
                    list = Math.max(pricing.baseList || 0, final * 1.08);
                    if (list <= final) {
                        list = final * 1.12;
                    }
                }
            }
        }

        if (priceEl) {
            priceEl.textContent = formatInr(final);
        }
        if (oldEl) {
            oldEl.textContent = formatInr(list > final ? list : final * 1.1);
        }
    }

    function updateProductPrice(productId) {
        updatePriceForProductCard(productId);
    }

    function handleAddToCart(productId) {
        const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
        if (!productCard) {
            return;
        }

        const variantSelect = document.getElementById('variant-select-' + productId);
        const variantId = variantSelect ? variantSelect.value : null;

        const pavIds = [];
        productCard.querySelectorAll('.attribute-selection input.attr-pav-checkbox:checked').forEach((el) => {
            pavIds.push(parseInt(el.value, 10));
        });

        addToCart(productId, variantId, pavIds);
    }

    function addToCart(productId, variantId = null, selectedProductAttributeValueIds = []) {
        const body = {
            product_id: productId,
            quantity: 1,
            product_variant_id: variantId ? parseInt(variantId, 10) : null,
            selected_product_attribute_value_ids: selectedProductAttributeValueIds,
        };

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(body),
        })
            .then((res) => res.json().then((data) => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    alert(data.message || 'Could not add to cart');
                    return;
                }
                alert(data.message || 'Added to cart');
            })
            .catch(() => alert('Network error'));
    }
</script>



<script>

document.querySelectorAll('.wishlist-btn')
.forEach(button => {

    button.addEventListener('click', async function () {

        let productId = this.dataset.product;

        try {

            let response = await fetch(
                `/wishlist/${productId}`,
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN':
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                    }
                }
            );

            let data = await response.json();

            if (data.added) {

                alert('Added to wishlist');

            } else {

                alert('Removed from wishlist');
            }

        } catch (error) {

            console.error(error);

            alert('Something went wrong');
        }
    });
});

</script>