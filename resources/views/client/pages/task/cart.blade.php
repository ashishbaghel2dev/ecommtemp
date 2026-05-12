<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cart</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            width: 80%;
            max-width: 960px;
            margin: 30px auto;
        }

        .cart-item {
            background: #fff;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 12px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .cart-item-main {
            flex: 1;
            min-width: 200px;
        }

        .cart-line-attrs {
            margin-top: 8px;
            font-size: 13px;
            color: #555;
        }

        .cart-line-attrs span {
            display: inline-block;
            margin-right: 12px;
        }

        .qty-box button {
            padding: 5px 10px;
            cursor: pointer;
        }

        .summary {
            background: #fff;
            padding: 16px;
            margin-top: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
        }

        .btn-danger {
            background: #c0392b;
            color: #fff;
        }

        .btn-success {
            background: #27ae60;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Your cart</h2>

    <div id="cart-wrapper">

        @forelse($cart->items as $item)

            <div class="cart-item" id="item-{{ $item->id }}">

                <div class="cart-item-main">
                    <h4>{{ $item->product_name }}</h4>

                    @if(!empty($item->meta['product_attribute_values']))
                        <div class="cart-line-attrs">
                            @foreach($item->meta['product_attribute_values'] as $row)
                                <span>
                                    {{ $row['attribute_name'] ?? 'Option' }}:
                                    {{ $row['attribute_value_label'] ?? $row['value'] ?? '—' }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($item->product_variant_id && $item->variant)
                        <div style="font-size: 13px; color: #666; margin-top: 6px;">
                            @php
                                $variantDetails = [];
                                if (is_array($item->variant->attributes)) {
                                    foreach ($item->variant->attributes as $attrId => $valId) {
                                        $attr = \App\Models\Attribute::find($attrId);
                                        $val = \App\Models\AttributeValue::find($valId);
                                        if ($attr && $val) {
                                            $variantDetails[] = "{$attr->name}: {$val->value}";
                                        }
                                    }
                                }
                            @endphp
                            <strong>Variant:</strong> {{ implode(', ', $variantDetails) ?: $item->product_sku }}
                        </div>
                    @endif

                    <p style="margin-top: 8px;">Price: ₹{{ number_format($item->price, 2) }}</p>
                </div>

                <div class="qty-box">
                    <button type="button" onclick="decrement({{ $item->id }})">−</button>
                    <span id="qty-{{ $item->id }}">{{ $item->quantity }}</span>
                    <button type="button" onclick="increment({{ $item->id }})">+</button>
                </div>

                <div>
                    <p>₹<span id="subtotal-{{ $item->id }}">{{ number_format($item->subtotal, 2) }}</span></p>
                </div>

                <div>
                    <button type="button" class="btn btn-danger" onclick="removeItem({{ $item->id }})">Remove</button>
                </div>

            </div>

        @empty

            <p>Your cart is empty.</p>

        @endforelse

    </div>

    <div class="summary">

        <h3>Total: ₹<span id="cart-total">{{ number_format($cart->subtotal, 2) }}</span></h3>

        <button type="button" class="btn btn-danger" onclick="clearCart()">Clear cart</button>
        <button type="button" class="btn btn-success">Checkout</button>

    </div>

</div>

<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] =
        document.querySelector('meta[name="csrf-token"]').content;

    function increment(itemId) {
        axios.post('/cart/increment/' + itemId)
            .then((res) => {
                if (res.data.status) {
                    const item = res.data.item;
                    document.getElementById('qty-' + itemId).innerText = item.quantity;
                    document.getElementById('subtotal-' + itemId).innerText = Number(item.subtotal).toFixed(2);
                    document.getElementById('cart-total').innerText = Number(res.data.cart.grand_total).toFixed(2);
                } else {
                    alert(res.data.message || 'Could not update');
                }
            })
            .catch((err) => console.error(err));
    }

    function decrement(itemId) {
        axios.post('/cart/decrement/' + itemId)
            .then((res) => {
                if (!res.data.status) {
                    alert(res.data.message || 'Could not update');
                    return;
                }
                const item = res.data.item;
                if (item) {
                    document.getElementById('qty-' + itemId).innerText = item.quantity;
                    document.getElementById('subtotal-' + itemId).innerText = Number(item.subtotal).toFixed(2);
                } else {
                    const row = document.getElementById('item-' + itemId);
                    if (row) {
                        row.remove();
                    }
                }
                document.getElementById('cart-total').innerText = Number(res.data.cart.grand_total).toFixed(2);
            })
            .catch((err) => console.error(err));
    }

    function removeItem(id) {
        axios.delete('/cart/remove/' + id)
            .then((res) => {
                if (res.data.status) {
                    const row = document.getElementById('item-' + id);
                    if (row) {
                        row.remove();
                    }
                    document.getElementById('cart-total').innerText = Number(res.data.cart.grand_total).toFixed(2);
                } else {
                    alert(res.data.message || 'Remove failed');
                }
            });
    }

    function clearCart() {
        axios.delete('/cart/clear')
            .then((res) => {
                if (res.data.status) {
                    document.getElementById('cart-wrapper').innerHTML = '<p>Your cart is empty.</p>';
                    document.getElementById('cart-total').innerText = '0.00';
                } else {
                    alert(res.data.message || 'Clear failed');
                }
            });
    }
</script>

</body>
</html>
