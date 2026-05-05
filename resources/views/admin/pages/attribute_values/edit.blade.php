<div class="container-fluid">

    <h4>Edit Attribute Value</h4>

    <form action="{{ route('attribute-values.update', $value->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card p-3">

            <div class="mb-3">
                <label>Attribute</label>
                <select name="attribute_id" class="form-control">
                    @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}" {{ $value->attribute_id == $attr->id ? 'selected' : '' }}>
                            {{ $attr->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Value</label>
                <input type="text" name="value" value="{{ $value->value }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Color Code</label>
                <input type="text" name="color_code" value="{{ $value->color_code }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="{{ $value->sort_order }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ $value->is_active ? 'checked' : '' }}>
                    Active
                </label>
            </div>

            <button class="btn btn-primary">Update</button>

        </div>

    </form>

</div>