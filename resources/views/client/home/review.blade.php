

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            Customer Reviews
        </h2>

    </div>

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    <div class="row">

        @forelse($reviews as $review)

            <div class="col-md-6 mb-4">

                <div class="card h-100 shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-2">

                            <h5 class="mb-0">
                                {{ $review->title }}
                            </h5>

                            <span>
                                ⭐ {{ $review->rating }}
                            </span>

                        </div>

                        <p class="text-muted small">
                            By {{ $review->user->name ?? 'User' }}
                        </p>

                        <p>
                            {{ $review->comment }}
                        </p>

                        {{-- Images --}}
                        @if($review->images->count())

                            <div class="d-flex gap-2 flex-wrap mt-3">

                                @foreach($review->images as $image)

                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        width="80"
                                        height="80"
                                        class="rounded border object-fit-cover"
                                    >

                                @endforeach

                            </div>

                        @endif

                        <div class="mt-4">

                            <a href="{{ route('reviews.show', $review->id) }}"
                               class="btn btn-dark btn-sm">
                                View Review
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-info">
                    No reviews found.
                </div>

            </div>

        @endforelse

    </div>

   {{-- Review Submit Form --}}


<div class="card shadow-sm mt-5">

    <div class="card-body">

        <h4 class="mb-4">
            Write a Review
        </h4>

        {{-- Success Message --}}
        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif

        {{-- Validation Errors --}}
        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form method="POST"
              action="{{ route('reviews.store') }}"
              enctype="multipart/form-data">

            @csrf

            {{-- Product ID --}}
            <input type="hidden"
                   name="product_id"
                   value="{{ $product->id }}">

            {{-- Title --}}
            <div class="mb-3">

                <label class="form-label">
                    Review Title
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    placeholder="Enter review title"
                    value="{{ old('title') }}"
                >

            </div>

            {{-- Rating --}}
            <div class="mb-3">

                <label class="form-label">
                    Rating
                </label>

                <select name="rating"
                        class="form-control"
                        required>

                    <option value="">
                        Select Rating
                    </option>

                    @for($i = 5; $i >= 1; $i--)

                        <option value="{{ $i }}"
                            {{ old('rating') == $i ? 'selected' : '' }}>

                            {{ $i }} Star

                        </option>

                    @endfor

                </select>

            </div>

            {{-- Comment --}}
            <div class="mb-3">

                <label class="form-label">
                    Comment
                </label>

                <textarea
                    name="comment"
                    rows="5"
                    class="form-control"
                    placeholder="Write your review..."
                >{{ old('comment') }}</textarea>

            </div>

            {{-- Images --}}
            <div class="mb-4">

                <label class="form-label">
                    Upload Images
                </label>

                <input
                    type="file"
                    name="images[]"
                    class="form-control"
                    multiple
                >

                <small class="text-muted">
                    JPG, PNG allowed. Max 2MB each.
                </small>

            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="btn btn-dark">

                Submit Review

            </button>

        </form>

    </div>

</div>




</div>

