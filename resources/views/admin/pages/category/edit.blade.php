@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')


<div class="container-fluid">

    <h4>Edit Category</h4>
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


<form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    @method('PUT')


        <div class="card p-3">

            {{-- Name --}}
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control"
                       value="{{ $category->name }}" required>
            </div>

            {{-- Parent Category --}}
            <div class="mb-3">
                <label>Parent Category</label>
                <select name="parent_id" class="form-control">
                    <option value="">None</option>
                    @foreach($parents as $cat)
                        <option value="{{ $cat->id }}"
                            {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">
                    {{ $category->description }}
                </textarea>
            </div>

            {{-- Image --}}
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">

                @if($category->image)
                    <img src="{{ asset( $category->image) }}"
                         width="80" class="mt-2">
                @endif
            </div>

            {{-- Sort Order --}}
            <div class="mb-3">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control"
                       value="{{ $category->sort_order }}">
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label>Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $category->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$category->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button class="btn btn-success">Update Category</button>

        </div>

    </form>

</div>



@endsection