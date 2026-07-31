@php
    $categoryItems = ($homeCategories ?? collect())->take(7);
    $categoryCount = ($homeCategories ?? collect())->count();
@endphp

@if($categoryItems->count())
<section class="home-category-match-section" aria-labelledby="homeCategoryMatchTitle">
    <div class="home-category-match-inner">
        <div class="home-category-match-head">
            <h2 id="homeCategoryMatchTitle">Find Your Perfect Match</h2>
            <p>Shop by Categories</p>
        </div>

        <div class="home-category-match-grid">
            @foreach($categoryItems as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="home-category-match-card">
                    <span class="home-category-match-media">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                        @else
                            <span class="home-category-match-placeholder">
                                <i class="ti ti-diamond"></i>
                            </span>
                        @endif
                    </span>
                    <strong>{{ strtoupper($category->name) }}</strong>
                </a>
            @endforeach

            <a href="{{ route('client.products.index') }}" class="home-category-match-card home-category-match-all">
                <span>
                    <strong>{{ max($categoryCount, 10) }}+</strong>
                    <small>Categories to chose from</small>
                </span>
                <b>VIEW ALL</b>
            </a>
        </div>
    </div>
</section>
@endif
