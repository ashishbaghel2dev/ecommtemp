@extends('admin.layouts.app')

@section('content')
    @include('admin.pages.products.form', [
        'product' => null,
        'action' => route('products.store'),
        'method' => 'POST',
        'buttonText' => 'Save Product',
        'selectedAttributes' => old('attributes', []),
        'selectedVariants' => old('variants', []),
    ])
@endsection
