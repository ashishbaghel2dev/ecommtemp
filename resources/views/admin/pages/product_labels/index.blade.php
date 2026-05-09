@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')



    <div class="main-content">

              <div class="top-bar">
            <h2 class="page-title">Product Labels</h2>
            <p class="page-subtitle">Create and manage categories</p>
            <a href="{{ route('productlabels.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>      Add Label
            </a>
        </div>


   <div class="table-card">
            <table class="custom-table">

        <thead>
            <tr>
                <th>Sr.No</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Color</th>
                <th>Status</th>
                <th width="180">Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse($labels as $key => $label)

                <tr>

                    <td>{{ $key + 1 }}</td>



                    <td>{{ $label->name }}</td>

                    <td><span class="slug">{{ $label->slug }}</span></td>

                    <td>
                        <span style="
                            background: {{ $label->color }};
                            padding: 5px 15px;
                            border-radius: 5px;
                            color: #fff;
                        ">
                            {{ $label->color }}
                        </span>
                    </td>

                   <td>
                                @if($label->is_active)
                                    <span class="status-badge active">Active</span>
                                @else
                                    <span class="status-badge inactive">Inactive</span>
                                @endif
                            </td>
                      <td class="action-cell">
                                <a href="{{ route('productlabels.edit', $label->id) }}" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="{{ route('productlabels.destroy', $label->id) }}" method="POST" class="d-inline">
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
                    <td colspan="6" class="text-center">
                        No Labels Found
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    {{ $labels->links() }}
   </div>
    </div>
    

</div>



@endsection