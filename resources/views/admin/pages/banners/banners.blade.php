@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')

<div class="main-content">

    <div class="top-bar">
        <h2 class="page-title">Banner</h2>
        <p class="page-subtitle">Create and manage Banner</p>
        <a href="{{ route('banners.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Add Banner
        </a>
    </div>



   <div class="table-card">
        <table class="custom-table">
               <thead>
    <tr>
        <th>Image</th>
           <th>Position </th>
        <th>Link</th>
                 <th>
priority </th>
        <th>Status</th>
        <th>Action</th>
    </tr>

</thead>
   <tbody>
  @foreach($banners->sortBy('priority') as $banner)
    <tr>
        <td>
            <img src="{{ asset($banner->image) }}" width="360">
        </td>
    <td>{{ $banner->position }}</td>
        <td>{{ $banner->link }}</td>
           <td>{{ $banner->priority }}</td>
       <td>
        @if($banner->is_active)
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
<a href="{{ route('banners.edit', $banner->id) }}" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" class="d-inline">
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
       <tbody>
</table>

</div>


@endsection