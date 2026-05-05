<div class="container-fluid">

    <h4>Edit Attribute</h4>

    <form action="{{ route('attributes.update', $attribute->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card p-3">

            <div class="mb-3">
                <label>SubCategory</label>
                <select name="subcategory_id" class="form-control">
                    @foreach($subcategories as $sub)
                        <option value="{{ $sub->id }}" {{ $attribute->subcategory_id == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ $attribute->name }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Code</label>
                <input type="text" name="code" value="{{ $attribute->code }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="text" {{ $attribute->type == 'text' ? 'selected' : '' }}>Text</option>
                    <option value="select" {{ $attribute->type == 'select' ? 'selected' : '' }}>Select</option>
                    <option value="number" {{ $attribute->type == 'number' ? 'selected' : '' }}>Number</option>
                    <option value="boolean" {{ $attribute->type == 'boolean' ? 'selected' : '' }}>Boolean</option>
                </select>
            </div>

            <div class="mb-3">
                <label>
                    <input type="checkbox" name="is_required" value="1" {{ $attribute->is_required ? 'checked' : '' }}>
                    Required
                </label>

                <label>
                    <input type="checkbox" name="is_filterable" value="1" {{ $attribute->is_filterable ? 'checked' : '' }}>
                    Filterable
                </label>
            </div>

            <button class="btn btn-primary">Update</button>

        </div>

    </form>

</div>