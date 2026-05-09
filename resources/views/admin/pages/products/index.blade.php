@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div class="main-content">
    <div class="top-bar">
        <div>
            <h2 class="page-title">Products</h2>
            <p class="page-subtitle">Create and manage products</p>
        </div>

        <a href="{{ route('products.create') }}" class="btn-primary">
            <i class="ti ti-plus"></i> Add Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Sr.No</th>
                    <th>Name / Category / Slug</th>
                    <th>Image / Stock / ID</th>
                    <th>Price / Discount / Sale</th>
                    <th>SKU / Type / Label</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $key => $product)
                    <tr>
                        <td>{{ $products->firstItem() + $key }}</td>

                        <td>
                            <div class="product-image-box">
                                <div>
                                    <p class="pr-title">{{ \Illuminate\Support\Str::limit($product->name, 50) }}</p>
                                    <p class="catg">Category : {{ $product->category->name ?? '-' }}</p>
                                    <p>
                                        Slug :
                                        <span class="slug">{{ \Illuminate\Support\Str::limit($product->slug, 50) }}</span>
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="product-image-box">
                                <div>
                                    @if($product->image || $product->images->first())
                                        <img src="{{ asset($product->image ?: $product->images->first()->image) }}"
                                             alt="{{ $product->name }}"
                                             class="table-img2">
                                    @else
                                        <span class="no-image">No Image</span>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-muted">No of Image : {{ max(1, $product->images->count()) }}</p>
                                    <p>
                                        @if($product->stock > 0)
                                            <span style="color: green; font-weight: 600;">{{ $product->stock }} Available</span>
                                        @else
                                            <span style="color: red; font-weight: 600;">0 in stock</span>
                                        @endif
                                    </p>
                                    <p>ID : {{ $product->id }}</p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="product-image-box">
                                <div>
                                    <div class="product-price-box">
                                        @if($product->discount_price)
                                            <p class="product-old-price">₹{{ $product->price }}</p>
                                            <p class="product-new-price">₹{{ $product->discount_price }}</p>
                                        @else
                                            <p class="product-new-price">₹{{ $product->price }}</p>
                                        @endif
                                    </div>

                                    @if($product->sale_price)
                                        <p>
                                            Sale Till :
                                            @if($product->sale_start && $product->sale_end)
                                                ({{ \Carbon\Carbon::parse($product->sale_start)->format('d M') }}
                                                to
                                                {{ \Carbon\Carbon::parse($product->sale_end)->format('d M') }})
                                            @else
                                                -
                                            @endif
                                        </p>
                                        <p class="sale-new-price">Sale Price : ₹{{ $product->sale_price }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="product-image-box">
                                <div>
                                    <p class="catg">SKU : {{ $product->sku }}</p>
                                    <p class="catg">Type : {{ ucfirst($product->type) }}</p>
                                    <div style="display: flex; align-items: flex-start; flex-wrap: wrap; gap: 0 4px; max-width: 180px;">
                                        <span>Labels :</span>
                                        @forelse($product->labels as $label)
                                            <span>{{ $label->name }}{{ ! $loop->last ? ',' : '' }}</span>
                                        @empty
                                            <span>-</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($product->is_active)
                                <span class="status-badge active">Active</span>
                            @else
                                <span class="status-badge inactive">Inactive</span>
                            @endif
                        </td>

                        <td class="action-cell">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn-icon edit">
                                <i class="ti ti-pencil-minus"></i>
                            </a>

                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon delete" onclick="return confirm('Delete this product?')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">No Products Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
@endsection
