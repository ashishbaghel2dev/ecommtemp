<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h4>Attribute Values</h4>
        <a href="{{ route('attribute-values.create') }}" class="btn btn-primary">+ Add Value</a>
    </div>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>#</th>
                <th>Attribute</th>
                <th>Value</th>
                <th>Slug</th>
                <th>Color</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($values as $val)
                <tr>
                    <td>{{ $val->id }}</td>
                    <td>{{ $val->attribute->name ?? '-' }}</td>
                    <td>{{ $val->value }}</td>
                    <td>{{ $val->slug }}</td>
                    <td>
                        @if($val->color_code)
                            <span style="display:inline-block;width:20px;height:20px;background:{{ $val->color_code }}"></span>
                        @endif
                    </td>
                    <td>{{ $val->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('attribute-values.edit', $val->id) }}" class="btn btn-sm btn-info">Edit</a>

                        <form action="{{ route('attribute-values.destroy', $val->id) }}" method="POST" style="display:inline-block">
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