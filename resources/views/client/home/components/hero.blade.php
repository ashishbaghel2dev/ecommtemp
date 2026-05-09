<!-- home.blade.php -->

@if($homeSliders->count())

<div id="homeBannerCarousel"
     class="carousel slide carousel-fade"
     data-bs-ride="carousel"
     data-bs-interval="3000">

    <!-- INDICATORS -->
    <div class="carousel-indicators">

        @foreach($homeSliders as $key => $banner)

            <button type="button"
                    data-bs-target="#homeBannerCarousel"
                    data-bs-slide-to="{{ $key }}"
                    class="{{ $key == 0 ? 'active' : '' }}"
                    aria-current="{{ $key == 0 ? 'true' : 'false' }}"
                    aria-label="Slide {{ $key + 1 }}">
            </button>

        @endforeach

    </div>

    <!-- SLIDES -->
    <div class="carousel-inner">

        @foreach($homeSliders as $key => $banner)

            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">

                @if(!empty($banner->link))
                    <a href="{{ $banner->link }}"
                       target="_blank">
                @endif

                <!-- IMAGE -->
                <img src="{{ asset($banner->image) }}"
                     class="d-block w-100"
                     alt="Banner {{ $banner->id }}"
                     loading="{{ $key == 0 ? 'eager' : 'lazy' }}">

                @if(!empty($banner->link))
                    </a>
                @endif

            </div>

        @endforeach

    </div>

    <!-- PREVIOUS -->
    @if($homeSliders->count() > 1)

    <button class="carousel-control-prev"
            type="button"
            data-bs-target="#homeBannerCarousel"
            data-bs-slide="prev">

        <span class="carousel-control-prev-icon"></span>

    </button>

    <!-- NEXT -->
    <button class="carousel-control-next"
            type="button"
            data-bs-target="#homeBannerCarousel"
            data-bs-slide="next">

        <span class="carousel-control-next-icon"></span>

    </button>

    @endif

</div>

@else

<!-- NO BANNER -->
<div class="text-center p-5">
    <h4>No Banner Available</h4>
</div>

@endif