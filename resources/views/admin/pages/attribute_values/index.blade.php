@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')


    <div class="main-content">

        <div class="top-bar">
            <h2 class="page-title">Attribute Values</h2>
            <p class="page-subtitle">Create and manage attributes values</p>
            <a href="{{ route('attribute-values.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Add Attribute Values
            </a>
        </div>
 

   
  <div class="table-card">
          <table class="custom-table">

        <thead>
            <tr>
                <th>Sr.No</th>
                <th>Attribute</th>
                <th>Value</th>
                <th>Slug</th>
                <th>Color</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($values as $val)
                <tr>
                    <td>{{ $val->id }}</td>
                   <td class="fw-medium">{{ $val->attribute->name ?? '-' }}</td>
                    <td>{{ $val->value }}</td>
                    <td> <span class="slug">{{ $val->slug }} </span></td>
                    <td>
                        @if($val->color_code)
<span class="slug">{{ $val->color_code }}</span>
     @else
                         <span class="slug">-</span>
                        @endif
     
                       
                    </td>
                 <td>
                                @if($val->is_active)
                                    <span class="status-badge active">Active</span>
                                @else
                                    <span class="status-badge inactive">Inactive</span>
                                @endif
                            </td>

                               <td class="action-cell">
                                <a href="{{ route('attribute-values.edit', $val->id) }}" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="{{ route('attribute-values.destroy', $val->id) }}" method="POST" class="d-inline">
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