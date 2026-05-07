
@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Products</h2>

        <a href="{{ route('products.create') }}"
           class="btn btn-primary">
            Add Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-bordered align-middle">

        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Labels</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th width="180">Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse($products as $product)

                <tr>

                    <td>{{ $product->id }}</td>

                    <td>
                        @if($product->image || $product->images->first())
                            <img src="{{ asset($product->image ?: $product->images->first()->image) }}"
                                 width="60"
                                 height="60"
                                 style="object-fit:cover;">
                        @endif
                    </td>

                    <td>{{ $product->name }}</td>

                    <td>{{ $product->category->name ?? '-' }}</td>

                    <td>
                        @forelse($product->labels as $label)
                            <span class="badge" style="background: {{ $label->color ?: '#6c757d' }}">
                                {{ $label->name }}
                            </span>
                        @empty
                            -
                        @endforelse
                    </td>

                    <td>{{ $product->sku }}</td>

                    <td>₹{{ $product->price }}</td>

                    <td>{{ $product->stock }}</td>

                    <td>
                        @if($product->is_active)
                            <span class="badge bg-success">
                                Active
                            </span>
                        @else
                            <span class="badge bg-danger">
                                Inactive
                            </span>
                        @endif
                    </td>

                    <td>

                        <a href="{{ route('products.edit', $product->id) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="{{ route('products.destroy', $product->id) }}"
                              method="POST"
                              style="display:inline-block">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete Product?')">
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="10" class="text-center">
                        No Products Found
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    {{ $products->links() }}

</div>

@endsection
