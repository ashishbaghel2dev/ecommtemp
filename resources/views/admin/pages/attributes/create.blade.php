<div class="container-fluid">

    <h4>Create Attribute</h4>

    <form action="{{ route('attributes.store') }}" method="POST">
        @csrf

        <div class="card p-3">

            <div class="mb-3">
                <label>SubCategory</label>
                <select name="subcategory_id" class="form-control">
                    @foreach($subcategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control">
            </div>

            <div class="mb-3">
                <label>Code</label>
                <input type="text" name="code" class="form-control">
            </div>

            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="text">Text</option>
                    <option value="select">Select</option>
                    <option value="number">Number</option>
                    <option value="boolean">Boolean</option>
                </select>
            </div>

            <div class="mb-3">
                <label>
                    <input type="checkbox" name="is_required" value="1"> Required
                </label>
                <label>
                    <input type="checkbox" name="is_filterable" value="1"> Filterable
                </label>
            </div>

            <button class="btn btn-success">Save</button>

        </div>

    </form>

</div>