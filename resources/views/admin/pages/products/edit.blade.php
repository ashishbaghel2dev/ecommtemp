@extends('admin.layouts.app')

@section('content')
    @include('admin.pages.products.form', [
        'product' => $product,
        'action' => route('products.update', $product->id),
        'method' => 'PUT',
        'buttonText' => 'Update Product',
        'selectedAttributes' => old('attributes', $selectedAttributes),
        'selectedVariants' => $selectedVariants,
    ])
@endsection
