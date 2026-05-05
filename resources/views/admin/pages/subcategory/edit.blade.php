<div class="container-fluid">

    <h4>Edit Sub Category</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('subcategories.update', $subcategory->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card p-3">

            {{-- Name --}}
            <div class="mb-3">
                <label>Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $subcategory->name) }}">
            </div>

            {{-- Parent --}}
            <div class="mb-3">
                <label>Parent Category</label>
                <select name="category_id" class="form-control">
                    <option value="">None</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ $subcategory->description }}</textarea>
            </div>

            {{-- IMAGE --}}
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">

                @if($subcategory->image)
                    <img src="{{ asset($subcategory->image) }}" width="80" class="mt-2">
                @endif
            </div>

            {{-- BANNER --}}
            <div class="mb-3">
                <label>Banner</label>
                <input type="file" name="banner" class="form-control">

                @if($subcategory->banner)
                    <img src="{{ asset($subcategory->banner) }}" width="120" class="mt-2">
                @endif
            </div>

            {{-- SORT --}}
            <div class="mb-3">
                <label>Sort Order</label>
                <input type="number"
                       name="sort_order"
                       class="form-control"
                       value="{{ $subcategory->sort_order }}">
            </div>

            {{-- STATUS --}}
            <div class="mb-3">
                <label>Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $subcategory->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$subcategory->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button class="btn btn-success">Update Sub Category</button>

        </div>

    </form>

</div>