@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">Edit Product</h2>

    <form action="{{ route('products.update', $product->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="row">

            <div class="col-md-6 mb-3">
                <label>Name</label>

                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ $product->name }}"
                       required>
            </div>

            <div class="col-md-6 mb-3">
                <label>SKU</label>

                <input type="text"
                       name="sku"
                       class="form-control"
                       value="{{ $product->sku }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Price</label>

                <input type="number"
                       step="0.01"
                       name="price"
                       class="form-control"
                       value="{{ $product->price }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Discount Price</label>

                <input type="number"
                       step="0.01"
                       name="discount_price"
                       class="form-control"
                       value="{{ $product->discount_price }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Stock</label>

                <input type="number"
                       name="stock"
                       class="form-control"
                       value="{{ $product->stock }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>Short Description</label>

                <textarea name="short_description"
                          class="form-control"
                          rows="3">{{ $product->short_description }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Description</label>

                <textarea name="description"
                          class="form-control"
                          rows="5">{{ $product->description }}</textarea>
            </div>

            <div class="col-md-3 mb-3">

                <label>
                    <input type="checkbox"
                           name="is_active"
                           {{ $product->is_active ? 'checked' : '' }}>

                    Active
                </label>

            </div>

            <div class="col-md-3 mb-3">

                <label>
                    <input type="checkbox"
                           name="is_featured"
                           {{ $product->is_featured ? 'checked' : '' }}>

                    Featured
                </label>

            </div>

        </div>

        <button type="submit"
                class="btn btn-primary">
            Update Product
        </button>

    </form>

</div>

@endsection