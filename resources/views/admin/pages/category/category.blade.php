
@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')


<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Categories</h4>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            + Add Category
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


    {{-- 🔍 FILTER SECTION --}}
    <form method="GET" class="card p-3 mb-3">
        <div class="row">

            {{-- Search --}}
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                    placeholder="Search category..."
                    value="{{ request('search') }}">
            </div>

            {{-- Parent Filter --}}
            <div class="col-md-3">
                <select name="parent_id" class="form-control">
                    <option value="">All Parent</option>
                    @foreach(\App\Models\Category::whereNull('parent_id')->get() as $parent)
                        <option value="{{ $parent->id }}"
                            {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sort --}}
            <div class="col-md-3">
                <select name="sort_by" class="form-control">
                    <option value="">Default Sort</option>
                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="order" {{ request('sort_by') == 'order' ? 'selected' : '' }}>Sort Order</option>
                </select>
            </div>

            {{-- Button --}}
            <div class="col-md-2">
                <button class="btn btn-dark w-100">Filter</button>
            </div>

        </div>
    </form>

    {{-- 📋 TABLE --}}
    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead>
                    <tr>
                        <th>#</th>
                             <th>Image</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Slug</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @forelse($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" width="50">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ $category->name }}</td>

                            {{-- Parent --}}
                            <td>
                                {{ $category->parent->name ?? '-' }}
                            </td>

                            <td>{{ $category->slug }}</td>

                            <td>{{ $category->sort_order ?? 0 }}</td>

                            {{-- Status --}}
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <a href="{{ route('categories.edit', $category->id) }}"
                                   class="btn btn-sm btn-primary">
                                    Edit
                                </a>

                                <form action="{{ route('categories.destroy', $category->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this category?')">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No categories found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>


@endsection