<!DOCTYPE html>
<html>
<head>
    <title>Cart Page</title>


<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        .container {
            width: 80%;
            margin: 30px auto;
        }

        .cart-item {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
        }

        .qty-box button {
            padding: 5px 10px;
        }

        .summary {
            background: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
        }

        .btn-danger { background: red; color: white; }
        .btn-primary { background: blue; color: white; }
        .btn-success { background: green; color: white; }
    </style>
</head>

<body>

<div class="container">

    <h2>Your Cart</h2>

    <div id="cart-wrapper">

        @foreach($cart->items as $item)

        <div class="cart-item" id="item-{{ $item->id }}">

            <div>
                <h4>{{ $item->product_name }}</h4>
                @if($item->product_variant_id && $item->variant)
                    <div style="font-size: 13px; color: #666; margin-top: 5px;">
                        @php
                            $variantDetails = [];
                            // Construct readable variant description from stored attributes JSON
                            if (is_array($item->variant->attributes)) {
                                foreach ($item->variant->attributes as $attrId => $valId) {
                                    // In a production app, eager load these to avoid N+1 queries
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

                <p>Price: ₹{{ $item->price }}</p>
            </div>

            <div class="qty-box">
                <button onclick="decrement({{ $item->id }})">-</button>

                <span id="qty-{{ $item->id }}">{{ $item->quantity }}</span>

                <button onclick="increment({{ $item->id }})">+</button>
            </div>

            <div>
                <p>₹<span id="subtotal-{{ $item->id }}">{{ $item->subtotal }}</span></p>
            </div>

            <div>
                <button class="btn btn-danger" onclick="removeItem({{ $item->id }})">
                    Remove
                </button>

                <!-- POST fallback button -->
                <form action="/cart/remove/{{ $item->id }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-danger">POST Remove</button>
                </form>
            </div>

        </div>

        @endforeach

    </div>

    <div class="summary">

        <h3>Total: ₹<span id="cart-total">{{ $cart->subtotal }}</span></h3>

        <button class="btn btn-danger" onclick="clearCart()">Clear Cart</button>

        <!-- POST fallback -->
        <form action="/cart/clear" method="POST" style="display:inline;">
            @csrf
            <button class="btn btn-danger">POST Clear</button>
        </form>

        <button class="btn btn-success">Checkout</button>

    </div>

</div>

<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').content;

/*
|--------------------------------------------------------------------------
| INCREMENT
|--------------------------------------------------------------------------
*/
function increment(itemId) {
    axios.post('/cart/increment/' + itemId)
        .then(res => {
            if (res.data.status) {
                const item = res.data.item;
                document.getElementById('qty-' + itemId).innerText = item.quantity;
                document.getElementById('subtotal-' + itemId).innerText = item.subtotal;
                document.getElementById('cart-total').innerText = res.data.cart.grand_total;
            } else {
                alert(res.data.message);
            }
        })
        .catch(err => console.error(err));
}

/*
|--------------------------------------------------------------------------
| DECREMENT
|--------------------------------------------------------------------------
 */
function decrement(itemId) {
    axios.post('/cart/decrement/' + itemId)
        .then(res => {
            if (res.data.status) {
                const item = res.data.item;
                if (item) { // Item still exists
                    document.getElementById('qty-' + itemId).innerText = item.quantity;
                    document.getElementById('subtotal-' + itemId).innerText = item.subtotal;
                } else { // Item was removed (quantity went to 0)
                    document.getElementById('item-' + itemId).remove();
                }
                document.getElementById('cart-total').innerText = res.data.cart.grand_total;
            } else {
                alert(res.data.message);
            }
        })
        .catch(err => console.error(err));
}
/*
|--------------------------------------------------------------------------
| REMOVE ITEM
|--------------------------------------------------------------------------
*/
function removeItem(id) {
    axios.delete('/cart/remove/' + id)
        .then(res => {
            if (res.data.status) {
                document.getElementById('item-' + id).remove();
                document.getElementById('cart-total').innerText = res.data.cart.grand_total;
            } else {
                alert(res.data.message);
            }
        });
}

/*
|--------------------------------------------------------------------------
| CLEAR CART
|--------------------------------------------------------------------------
*/
function clearCart() {
    axios.delete('/cart/clear')
        .then(res => {
            if (res.data.status) {
                document.getElementById('cart-wrapper').innerHTML = '<p>Your cart is empty.</p>';
                document.getElementById('cart-total').innerText = 0;
            } else {
                alert(res.data.message);
            }
        });
}
/*
 |--------------------------------------------------------------------------
 | UPDATE TOTAL (simple UI sync) - No longer needed as updates are dynamic
 |--------------------------------------------------------------------------
 */
</script>

</body>
</html>