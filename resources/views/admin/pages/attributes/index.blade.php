@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')


<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h4>Attributes</h4>
        <a href="{{ route('attributes.create') }}" class="btn btn-primary">+ Add Attribute</a>
    </div>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Code</th>
                <th>Category</th>
                <th>Type</th>
                <th>Required</th>
                <th>Filterable</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($attributes as $attr)
                <tr>
                    <td>{{ $attr->id }}</td>
                    <td>{{ $attr->name }}</td>
                    <td>{{ $attr->code }}</td>
                    <td>{{ $attr->category->name ?? '-' }}</td>
                    <td>{{ $attr->type }}</td>
                    <td>{{ $attr->is_required ? 'Yes' : 'No' }}</td>
                    <td>{{ $attr->is_filterable ? 'Yes' : 'No' }}</td>
                    <td>{{ $attr->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('attributes.edit', $attr->id) }}" class="btn btn-sm btn-info">Edit</a>

                        <form action="{{ route('attributes.destroy', $attr->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

@endsection