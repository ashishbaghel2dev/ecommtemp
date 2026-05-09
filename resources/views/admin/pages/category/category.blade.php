@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')

    <div class="main-content">

 
        <div class="top-bar">
            <h2 class="page-title">Categories</h2>
            <p class="page-subtitle">Create and manage categories</p>
            <a href="{{ route('categories.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Add Category
            </a>
        </div>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="filter-card">
            <form method="GET" class="filter-form">
                <input type="text" name="search" class="input-field" 
                       placeholder="Search category..." value="{{ request('search') }}">

                <select name="parent_id" class="input-field">
                    <option value="">All Parent Categories</option>
                    @foreach(\App\Models\Category::whereNull('parent_id')->get() as $parent)
                        <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>

                <select name="sort_by" class="input-field">
                    <option value="">Default Sort</option>
                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                </select>

                <button type="submit" class="btn-filter">Filter</button>
            </form>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Sr.No</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Parent</th>
                        <th>Slug</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="table-img">
                                @else
                                    <span class="no-image">No Image</span>
                                @endif
                            </td>
                            <td class="fw-medium">{{ $category->name }}</td>
                            <td class="text-muted">{{ Str::limit($category->description ?? '', 50) }}</td>
                            <td>{{ $category->parent->name ?? '-' }}</td>
                            <td><span class="slug">{{ $category->slug }}</span></td>
                            <td>{{ $category->sort_order ?? 0 }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="status-badge active">Active</span>
                                @else
                                    <span class="status-badge inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="action-cell">
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" 
                                            onclick="return confirm('Delete this category?')">
                                      <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">No categories found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>




@endsection