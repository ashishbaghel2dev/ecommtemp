<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { margin: 0; background: #f4f6f8; color: #111827; font-family: Arial, sans-serif; }
        .invoice { width: min(900px, calc(100% - 32px)); margin: 32px auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 28px; }
        .top, .row { display: flex; justify-content: space-between; gap: 24px; }
        h1, h2, p { margin-top: 0; }
        h1 { color: #315412; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f9fafb; }
        .totals { width: min(360px, 100%); margin-left: auto; margin-top: 24px; }
        .totals div { display: flex; justify-content: space-between; padding: 8px 0; }
        .grand { color: #315412; font-size: 20px; font-weight: 800; }
        .actions { margin-top: 24px; }
        button { border: 0; border-radius: 8px; background: #315412; color: #fff; padding: 12px 16px; font-weight: 800; }
        @media print { body { background: #fff; } .invoice { border: 0; margin: 0; width: auto; } .actions { display: none; } }
    </style>
</head>
<body>
    @php
        $productTotal = (float) $order->items->sum(fn ($item) => (float) data_get($item->meta, 'pricing.line_original_total', (float) $item->price * (int) $item->quantity));
        $productDiscount = (float) $order->items->sum(fn ($item) => (float) data_get($item->meta, 'pricing.product_discount', 0));
    @endphp
    <main class="invoice">
        <div class="top">
            <div>
                <h1>Invoice</h1>
                <p><strong>{{ $order->order_number }}</strong></p>
                <p>Date: {{ $order->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <h2>Bill To</h2>
                <p>{{ $order->customer_name }}<br>{{ $order->customer_email }}<br>{{ $order->customer_phone }}</p>
            </div>
        </div>

        <section>
            <h2>Delivery Address</h2>
            <p>{{ $order->shipping_address_line_1 }}, {{ $order->shipping_address_line_2 }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}</p>
        </section>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_sku ?: '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            @if((float) data_get($item->meta, 'pricing.original_price', $item->price) > (float) $item->price)
                                <span style="display:block;color:#9ca3af;text-decoration:line-through;">₹{{ number_format((float) data_get($item->meta, 'pricing.original_price'), 2) }}</span>
                            @endif
                            ₹{{ number_format((float) $item->price, 2) }}
                        </td>
                        <td>₹{{ number_format((float) $item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>Product Total</span><strong>₹{{ number_format($productTotal, 2) }}</strong></div>
            <div><span>Product Discount</span><strong>-₹{{ number_format($productDiscount, 2) }}</strong></div>
            <div><span>Subtotal</span><strong>₹{{ number_format((float) $order->subtotal, 2) }}</strong></div>
            <div><span>Coupon Discount</span><strong>-₹{{ number_format((float) $order->coupon_discount, 2) }}</strong></div>
            <div><span>Shipping</span><strong>₹{{ number_format((float) $order->shipping_total, 2) }}</strong></div>
            <div><span>Tax</span><strong>₹{{ number_format((float) $order->tax_total, 2) }}</strong></div>
            <div class="grand"><span>Total</span><strong>₹{{ number_format((float) $order->grand_total, 2) }}</strong></div>
        </div>

        <div class="actions">
            <button onclick="window.print()">Print / Save as PDF</button>
        </div>
    </main>
</body>
</html>
