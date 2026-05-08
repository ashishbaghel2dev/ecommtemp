@extends('client.layouts.app')

@section('title', 'Home Page')

@section('content')


    <h1>Products</h1>

    <div style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        @forelse($products as $product)
            <div style="border: 1px solid #ddd; padding: 16px; border-radius: 8px;">
                @if($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 180px; object-fit: cover; margin-bottom: 12px;">
                @endif

                <h2 style="margin: 0 0 8px;">{{ $product->name }}</h2>
                <p style="margin: 0 0 8px;">Category: {{ $product->category->name ?? '-' }}</p>
                <p style="margin: 0 0 8px;">Price: ₹{{ $product->final_price }}</p>

                @if($product->labels->isNotEmpty())
                    <div style="margin-bottom: 12px;">
                        @foreach($product->labels as $label)
                            <span style="display: inline-block; color: #fff; background: {{ $label->color ?: '#555' }}; padding: 3px 8px; border-radius: 4px; margin-right: 4px;">
                                {{ $label->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @foreach($product->attributeValues->groupBy('attribute_id') as $attributeItems)
                    @php
                        $attribute = $attributeItems->first()->attribute;
                        $values = $attributeItems
                            ->map(fn ($item) => $item->attributeValue->value ?? $item->value)
                            ->filter()
                            ->values();
                    @endphp

                    @if($attribute && $values->isNotEmpty())
                        <div style="margin-top: 8px;">
                            <strong>{{ $attribute->name }}:</strong>
                            @foreach($values as $value)
                                <label style="display: inline-block; margin-left: 8px;">
                                    <input type="checkbox" checked disabled>
                                    {{ $value }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                @endforeach

                @if($product->type === 'configurable' && $product->variants->isNotEmpty())
                    <div style="margin-top: 14px;">
                        <strong>Available Configurations:</strong>

                        @foreach($product->variants as $variant)
                            <div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: 8px;">
                                @foreach(($variant->attributes ?? []) as $attributeId => $valueId)
                                    <label style="display: inline-block; margin-right: 8px;">
                                        <input type="checkbox" checked disabled>
                                        {{ $variantAttributes[$attributeId] ?? 'Attribute' }}:
                                        {{ $variantValues[$valueId] ?? $valueId }}
                                    </label>
                                @endforeach

                                <div style="margin-top: 4px;">
                                    Price: ₹{{ $variant->final_price }}
                                    <span style="margin-left: 8px;">Stock: {{ $variant->stock }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p>No products found.</p>
        @endforelse


        @include('client.home.review')
    </div>
@endsection
