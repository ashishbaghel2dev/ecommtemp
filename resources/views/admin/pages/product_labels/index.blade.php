

<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h2>Product Labels</h2>

        <a href="{{ route('productlabels.create') }}"
           class="btn btn-primary">
            Add Label
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Color</th>
                <th>Status</th>
                <th width="180">Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse($labels as $label)

                <tr>

                    <td>{{ $label->id }}</td>

                    <td>{{ $label->name }}</td>

                    <td>{{ $label->slug }}</td>

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
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>

                    <td>

                        <a href="{{ route('productlabels.edit', $label->id) }}"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <form action="{{ route('productlabels.destroy', $label->id) }}"
                              method="POST"
                              style="display:inline-block">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this label?')">
                                Delete
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

