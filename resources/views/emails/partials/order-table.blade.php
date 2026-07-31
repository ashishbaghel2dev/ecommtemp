@php
    $addressLine = \App\Services\StoreMailService::addressLine($order);
    $productTotal = (float) $order->items->sum(fn ($item) => (float) data_get($item->meta, 'pricing.line_original_total', (float) $item->price * (int) $item->quantity));
    $productDiscount = (float) $order->items->sum(fn ($item) => (float) data_get($item->meta, 'pricing.product_discount', 0));
@endphp

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-top:18px;font-size:14px;">
    <tr>
        <td style="padding:9px 0;color:#64748b;width:190px;">Order ID</td>
        <td style="padding:9px 0;font-weight:700;color:#172033;">{{ $order->order_number }}</td>
    </tr>
    <tr>
        <td style="padding:9px 0;color:#64748b;">Order Date</td>
        <td style="padding:9px 0;font-weight:700;color:#172033;">{{ optional($order->created_at)->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <td style="padding:9px 0;color:#64748b;">Customer Name</td>
        <td style="padding:9px 0;font-weight:700;color:#172033;">{{ $order->customer_name }}</td>
    </tr>
    <tr>
        <td style="padding:9px 0;color:#64748b;">Customer Phone</td>
        <td style="padding:9px 0;font-weight:700;color:#172033;">{{ $order->customer_phone ?: '-' }}</td>
    </tr>
    <tr>
        <td style="padding:9px 0;color:#64748b;">Customer Email</td>
        <td style="padding:9px 0;font-weight:700;color:#172033;">{{ $order->customer_email ?: '-' }}</td>
    </tr>
    <tr>
        <td style="padding:9px 0;color:#64748b;vertical-align:top;">Full Address</td>
        <td style="padding:9px 0;font-weight:700;color:#172033;line-height:1.6;">{{ $addressLine }}</td>
    </tr>
</table>

<h3 style="margin:24px 0 10px;color:#172033;font-size:18px;">Order Items</h3>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5edf5;font-size:13px;">
    <thead>
        <tr style="background:#f8fafc;">
            <th align="left" style="padding:10px;border-bottom:1px solid #e5edf5;color:#475569;">Product</th>
            <th align="left" style="padding:10px;border-bottom:1px solid #e5edf5;color:#475569;">Size / Option</th>
            <th align="center" style="padding:10px;border-bottom:1px solid #e5edf5;color:#475569;">Qty</th>
            <th align="right" style="padding:10px;border-bottom:1px solid #e5edf5;color:#475569;">Price</th>
            <th align="right" style="padding:10px;border-bottom:1px solid #e5edf5;color:#475569;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td style="padding:10px;border-bottom:1px solid #edf2f7;color:#172033;font-weight:700;">{{ $item->product_name }}</td>
                <td style="padding:10px;border-bottom:1px solid #edf2f7;color:#64748b;">{{ \App\Services\StoreMailService::itemOptions($item->meta ?? []) }}</td>
                <td align="center" style="padding:10px;border-bottom:1px solid #edf2f7;color:#172033;">{{ $item->quantity }}</td>
                <td align="right" style="padding:10px;border-bottom:1px solid #edf2f7;color:#172033;">
                    @if((float) data_get($item->meta, 'pricing.original_price', $item->price) > (float) $item->price)
                        <span style="display:block;color:#94a3b8;text-decoration:line-through;">₹{{ number_format((float) data_get($item->meta, 'pricing.original_price'), 2) }}</span>
                    @endif
                    ₹{{ number_format((float) $item->price, 2) }}
                </td>
                <td align="right" style="padding:10px;border-bottom:1px solid #edf2f7;color:#172033;font-weight:700;">₹{{ number_format((float) $item->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-top:16px;font-size:14px;">
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Product Total</td>
        <td align="right" style="padding:6px;width:140px;color:#172033;font-weight:700;">₹{{ number_format($productTotal, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Product Discount</td>
        <td align="right" style="padding:6px;color:#15803d;font-weight:700;">-₹{{ number_format($productDiscount, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Sub Total</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">₹{{ number_format((float) $order->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Coupon Discount</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">-₹{{ number_format((float) $order->coupon_discount, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Delivery Charge</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">₹{{ number_format((float) $order->shipping_total, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Tax</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">₹{{ number_format((float) $order->tax_total, 2) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Payment Status</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">{{ ucfirst($order->payment_status) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Payment Mode</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">Cash on Delivery</td>
    </tr>
    <tr>
        <td align="right" style="padding:6px;color:#64748b;">Delivery Status</td>
        <td align="right" style="padding:6px;color:#172033;font-weight:700;">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
    </tr>
    <tr>
        <td align="right" style="padding:10px 6px;border-top:1px solid #e5edf5;color:#172033;font-size:16px;font-weight:800;">Total Amount</td>
        <td align="right" style="padding:10px 6px;border-top:1px solid #e5edf5;color:#15803d;font-size:16px;font-weight:800;">₹{{ number_format((float) $order->grand_total, 2) }}</td>
    </tr>
</table>
