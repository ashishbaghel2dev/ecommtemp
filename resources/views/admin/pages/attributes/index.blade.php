@extends('admin.layouts.app')

@section('title', 'Attributes')

@section('content')
    <div class="main-content">

        <div class="top-bar">
            <h2 class="page-title">Attributes</h2>
            <p class="page-subtitle">Create and manage attributes</p>
            <a href="{{ route('attributes.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Add Attribute
            </a>
        </div>
 

  <div class="table-card">
          <table class="custom-table">
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
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attributes as $attr)
                        <tr>
                            <td class="text-muted">{{ $attr->id }}</td>
                            <td class="fw-bold text-dark">{{ $attr->name }}</td>
                            <td><span class="slug">{{ $attr->code }}</span></td>
                            <td><span class="text-muted">{{ $attr->category->name ?? '-' }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($attr->type) }}</span></td>
                            <td>
                                @if($attr->is_required)
                                    <span class="status-yes">Yes</span>
                                @else
                                    <span class="status-no">No</span>
                                @endif
                            </td>
                            <td>
                                @if($attr->is_filterable)
                                   <span class="status-yes">Yes</span>
                                @else
                                    <span class="status-no">No</span>
                                @endif
                            </td>
                          
                            <td>
                                @if($attr->is_active)
                                    <span class="status-badge active ">Active</span>
                                @else
                                    <span class="status-badge inactive   ">Inactive</span>
                                @endif
                            </td>
                            <td class="action-cell">
                                 <a href="{{ route('attributes.edit', $attr->id) }}" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="{{ route('attributes.destroy', $attr->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" 
                                            onclick="return confirm('Delete this category?')">
                                      <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


   
  

@endsection