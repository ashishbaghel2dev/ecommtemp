@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')
<div class="container-fluid">

    <h4>Create Attribute Value</h4>

    <form action="{{ route('attribute-values.store') }}" method="POST">
        @csrf

        <div class="card p-3">

            <div class="mb-3">
                <label>Attribute</label>
                <select name="attribute_id" class="form-control">
                    @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Value</label>
                <input type="text" name="value" class="form-control">
            </div>

            <div class="mb-3">
                <label>Color Code (optional)</label>
                <input type="text" name="color_code" class="form-control" placeholder="#ffffff">
            </div>

            <div class="mb-3">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>

            <div class="mb-3">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked> Active
                </label>
            </div>

            <button class="btn btn-success">Save</button>

        </div>

    </form>

</div>
@endsection