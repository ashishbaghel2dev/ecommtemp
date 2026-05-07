<div class="container">

    <h2 class="mb-4">Create Product</h2>

    <form action="{{ route('products.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="row">

            {{-- PRODUCT NAME --}}
            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Product Name
                </label>

                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       required>

                @error('name')

                    <small class="text-danger">
                        {{ $message }}
                    </small>

                @enderror

            </div>

            {{-- SKU --}}
            <div class="col-md-6 mb-3">

                <label class="form-label">
                    SKU
                </label>

                <input type="text"
                       name="sku"
                       class="form-control"
                       value="{{ old('sku') }}"
                       required>

                @error('sku')

                    <small class="text-danger">
                        {{ $message }}
                    </small>

                @enderror

            </div>

            {{-- PRICE --}}
            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Price
                </label>

                <input type="number"
                       step="0.01"
                       name="price"
                       class="form-control"
                       value="{{ old('price') }}"
                       required>

                @error('price')

                    <small class="text-danger">
                        {{ $message }}
                    </small>

                @enderror

            </div>

            {{-- DISCOUNT PRICE --}}
            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Discount Price
                </label>

                <input type="number"
                       step="0.01"
                       name="discount_price"
                       class="form-control"
                       value="{{ old('discount_price') }}">

            </div>

            {{-- STOCK --}}
            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Stock
                </label>

                <input type="number"
                       name="stock"
                       class="form-control"
                       value="{{ old('stock', 0) }}">

            </div>

            {{-- CATEGORY --}}
            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Category
                </label>

                <select name="category_id"
                        id="category_id"
                        class="form-control"
                        required>

                    <option value="">
                        Select Category
                    </option>

                    @foreach($categories as $category)

                        <option value="{{ $category->id }}"
                            {{ old('category_id') == $category->id ? 'selected' : '' }}>

                            {{ $category->name }}

                        </option>

                        {{-- CHILD CATEGORIES --}}
                        @foreach($category->children as $child)

                            <option value="{{ $child->id }}"
                                {{ old('category_id') == $child->id ? 'selected' : '' }}>

                                └── {{ $child->name }}

                            </option>

                        @endforeach

                    @endforeach

                </select>

                @error('category_id')

                    <small class="text-danger">
                        {{ $message }}
                    </small>

                @enderror

            </div>

            {{-- PRODUCT IMAGE --}}
            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Product Image
                </label>

                <input type="file"
                       name="image"
                       class="form-control">

            </div>

            {{-- SHORT DESCRIPTION --}}
            <div class="col-md-12 mb-3">

                <label class="form-label">
                    Short Description
                </label>

                <textarea name="short_description"
                          class="form-control"
                          rows="3">{{ old('short_description') }}</textarea>

            </div>

            {{-- DESCRIPTION --}}
            <div class="col-md-12 mb-3">

                <label class="form-label">
                    Description
                </label>

                <textarea name="description"
                          class="form-control"
                          rows="6">{{ old('description') }}</textarea>

            </div>

            {{-- STATUS --}}
            <div class="col-md-3 mb-3">

                <div class="form-check">

                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           checked>

                    <label class="form-check-label">
                        Active
                    </label>

                </div>

            </div>

            {{-- FEATURED --}}
            <div class="col-md-3 mb-3">

                <div class="form-check">

                    <input type="checkbox"
                           name="is_featured"
                           value="1"
                           class="form-check-input">

                    <label class="form-check-label">
                        Featured
                    </label>

                </div>

            </div>

        </div>

        <div class="mt-4">

            <button type="submit"
                    class="btn btn-success">

                Save Product

            </button>

            <a href="{{ route('products.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>