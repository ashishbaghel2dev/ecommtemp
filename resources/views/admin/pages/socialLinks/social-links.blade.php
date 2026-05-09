@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')
<div class="main-content">

    <div class="top-bar">
        <h2 class="page-title">Social Links</h2>
        <p class="page-subtitle">Create and manage Social Links</p>
        <a href="{{ route('social-links.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add New Social Link
        </a>
    </div>




   <div class="table-card">
        <table class="custom-table">
            <thead>
    <tr>
        <th>Name</th>
        <th>Icon</th>
        <th>URL</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
            </thead>
   <tbody>
    @foreach($links as $link)
    <tr>
        <td>{{ $link->name }}</td>
        <td class="icon"><i class="{{ $link->icon }}"></i></td>
        <td>{{ $link->url }}</td>
            <td>
        @if($link->is_active)
        <span class="status-badge active">
            Active
        </span>
        @else
        <span class="status-badge inactive">
            Inactive
        </span>
        @endif
    </td>
    <td >
                <div class="product-image-box">
<a href="{{ route('social-links.edit', $link->id) }}" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="{{ route('social-links.destroy', $link->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" 
                                            onclick="return confirm('Delete this category?')">
                                      <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                </div>
                                
                            </td>

    
    </tr>
    @endforeach
</tbody>
</table>
   </div>
</div>


@endsection