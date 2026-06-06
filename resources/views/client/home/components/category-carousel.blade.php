@php
    $categoryCarouselItems = $homeCategories ?? collect();
@endphp

@if($categoryCarouselItems->count())
    <section class="category-carousel-section container-fluid" aria-label="Shop categories">
        <div class="category-carousel-head">
            <div>
                <span>Shop by category</span>
                <h2>Explore Categories</h2>
            </div>

            <div class="category-carousel-actions" aria-label="Category carousel controls">
                <button type="button" class="category-carousel-control" data-category-carousel-prev aria-label="Previous categories">
                    <i class="ti ti-chevron-left"></i>
                </button>
                <button type="button" class="category-carousel-control" data-category-carousel-next aria-label="Next categories">
                    <i class="ti ti-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="category-carousel-track" data-category-carousel-track>
            @foreach($categoryCarouselItems as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                    <span class="category-card-media">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}"
                                 alt="{{ $category->name }}"
                                 loading="lazy">
                        @else
                            <i class="ti ti-category"></i>
                        @endif
                    </span>

                    <span class="category-card-content">
                        @if($category->parent)
                            <em>{{ $category->parent->name }}</em>
                        @endif

                        <strong>{{ $category->name }}</strong>
                        @if($category->description)
                            <small>{{ \Illuminate\Support\Str::limit($category->description, 82) }}</small>
                        @else
                            <small>{{ $category->children_count }} subcategories / {{ $category->products_count }} products</small>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <script>
        (() => {
            const track = document.querySelector('[data-category-carousel-track]');
            const previousButton = document.querySelector('[data-category-carousel-prev]');
            const nextButton = document.querySelector('[data-category-carousel-next]');

            if (!track || !previousButton || !nextButton) {
                return;
            }

            const scrollByCard = (direction) => {
                const card = track.querySelector('.category-card');
                const cardWidth = card ? card.getBoundingClientRect().width : 260;

                track.scrollBy({
                    left: direction * (cardWidth + 18),
                    behavior: 'smooth',
                });
            };

            previousButton.addEventListener('click', () => scrollByCard(-1));
            nextButton.addEventListener('click', () => scrollByCard(1));
        })();
    </script>
@endif
