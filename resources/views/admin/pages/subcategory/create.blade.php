<div class="container-fluid">

    <h4>Create subcategories</h4>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <form action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card p-3">

            {{-- Name --}}
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            {{-- Parent --}}
            <div class="mb-3">
                <label>Parent Category</label>
                <select name="category_id" class="form-control">
                    <option value="">None</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            {{-- Image --}}
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            {{-- Banner --}}
            <div class="mb-3">
                <label>Banner</label>
                <input type="file" name="banner" class="form-control">
            </div>

            {{-- Sort Order --}}
            <div class="mb-3">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label>Status</label>
                <select name="is_active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <button class="btn btn-primary">Create  Sub Category</button>

        </div>
    </form>

</div>