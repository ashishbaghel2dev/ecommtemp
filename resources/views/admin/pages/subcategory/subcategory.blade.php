<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h4>All SubCategories</h4>
        <a href="{{ route('subcategories.create') }}" class="btn btn-primary">
            + Add SubCategory
        </a>
    </div>

    {{-- SUCCESS / ERROR --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-3">

        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Main Category</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Sort</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($subcategories as $sub)

                    <tr>
                        <td>{{ $sub->id }}</td>

                        {{-- NAME --}}
                        <td>
                            <strong>{{ $sub->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $sub->slug }}</small>
                        </td>

                        {{-- CATEGORY --}}
                        <td>
                            {{ $sub->category->name ?? '—' }}
                        </td>

                        {{-- IMAGE --}}
                        <td>
                            @if($sub->image)
                                <img src="{{ asset($sub->image) }}"
                                     width="50"
                                     height="50"
                                     style="object-fit: cover;">
                            @else
                                —
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td>
                            @if($sub->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>

                        {{-- SORT --}}
                        <td>{{ $sub->sort_order }}</td>

                        {{-- ACTIONS --}}
                        <td>

                            <a href="{{ route('subcategories.edit', $sub->id) }}"
                               class="btn btn-sm btn-info">
                                Edit
                            </a>

                            <form action="{{ route('subcategories.destroy', $sub->id) }}"
                                  method="POST"
                                  style="display:inline-block">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center">
                            No SubCategories Found
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>
</div>