
@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')



<div class="container">

    <h2 class="mb-4">Edit Product Label</h2>

    <form action="{{ route('productlabels.update', $product_label->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>

            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ $product_label->name }}"
                   required>
        </div>

        <div class="mb-3">
            <label>Color</label>

            <input type="color"
                   name="color"
                   value="{{ $product_label->color }}"
                   class="form-control form-control-color">
        </div>

        <div class="mb-3">

            <label>
                <input type="checkbox"
                       name="is_active"
                       {{ $product_label->is_active ? 'checked' : '' }}>

                Active
            </label>

        </div>

        <button type="submit"
                class="btn btn-primary">
            Update Label
        </button>

    </form>

</div>

@endsection