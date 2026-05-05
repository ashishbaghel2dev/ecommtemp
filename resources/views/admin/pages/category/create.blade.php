<div class="container-fluid">

    <h4 class="mb-3">Add Category</h4>
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
    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card p-4">

            {{-- NAME --}}
            <div class="mb-3">
                <label>Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- PARENT --}}
            <div class="mb-3">
                <label>Parent Category</label>
                <select name="parent_id" class="form-control">
                    <option value="">None</option>
                    @foreach($parents as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            {{-- IMAGE --}}
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
                @error('image')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- BANNER (IMPORTANT ADDITION) --}}
            <div class="mb-3">
                <label>Banner</label>
                <input type="file" name="banner" class="form-control">
                @error('banner')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- SORT ORDER --}}
            <div class="mb-3">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
            </div>

            {{-- STATUS --}}
            <div class="mb-3">
                <label>Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button class="btn btn-primary">Save Category</button>

        </div>

    </form>

</div>