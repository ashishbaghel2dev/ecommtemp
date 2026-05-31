@php
    $showcaseBanners = $showcaseBanners ?? $homeSliders ?? collect();
@endphp

<section class="showcase-banner container-fluid" aria-label="Featured banners">
    @if($showcaseBanners->count())
        <div id="showcaseBannerCarousel"
             class="showcase-banner-carousel carousel slide carousel-fade"
             data-bs-ride="carousel"
             data-bs-interval="3500">

            @if($showcaseBanners->count() > 1)
                <div class="carousel-indicators showcase-banner-indicators">
                    @foreach($showcaseBanners as $key => $banner)
                        <button type="button"
                                data-bs-target="#showcaseBannerCarousel"
                                data-bs-slide-to="{{ $key }}"
                                class="{{ $key === 0 ? 'active' : '' }}"
                                aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                                aria-label="Banner {{ $key + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="carousel-inner">
                @foreach($showcaseBanners as $key => $banner)
                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                        @if(!empty($banner->link))
                            <a href="{{ $banner->link }}" class="showcase-banner-link">
                        @endif

                        <img src="{{ asset($banner->image) }}"
                             class="showcase-banner-image"
                             alt="Showcase banner {{ $key + 1 }}"
                             loading="{{ $key === 0 ? 'eager' : 'lazy' }}">

                        @if(!empty($banner->link))
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($showcaseBanners->count() > 1)
                <button class="carousel-control-prev showcase-banner-control"
                        type="button"
                        data-bs-target="#showcaseBannerCarousel"
                        data-bs-slide="prev"
                        aria-label="Previous banner">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>

                <button class="carousel-control-next showcase-banner-control"
                        type="button"
                        data-bs-target="#showcaseBannerCarousel"
                        data-bs-slide="next"
                        aria-label="Next banner">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            @endif
        </div>
    @endif
</section>
