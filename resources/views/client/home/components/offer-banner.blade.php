@php
    $offerBannerItems = ($offerBanners ?? collect())->take(4);
@endphp

@if($offerBannerItems->isNotEmpty())
    <section class="tanvi-offer-banners" aria-labelledby="tanviOfferBannerTitle">
        <div class="tanvi-offer-banners__head">
            <span>Special Offers</span>
            <h2 id="tanviOfferBannerTitle">Handpicked Jewellery Deals</h2>
        </div>

        <div class="tanvi-offer-banners__grid">
            @foreach($offerBannerItems as $banner)
                @php
                    $bannerLink = $banner->link ?: route('client.products.index');
                @endphp

                <article class="tanvi-offer-card">
                    <img src="{{ asset($banner->image) }}"
                         alt="{{ $banner->title ?: 'Tanvi jewellery offer' }}"
                         loading="lazy">

                    <div class="tanvi-offer-card__content">
                        @if($banner->eyebrow)
                            <span>{{ $banner->eyebrow }}</span>
                        @endif

                        @if($banner->title)
                            <h3>{{ $banner->title }}</h3>
                        @endif

                        @if($banner->subtitle)
                            <p>{{ $banner->subtitle }}</p>
                        @endif

                        <a href="{{ $bannerLink }}"
                           @if(\Illuminate\Support\Str::startsWith($bannerLink, ['http://', 'https://'])) target="_blank" rel="noopener" @endif>
                            {{ $banner->button_text ?: 'Explore now' }}
                            <i class="ti ti-arrow-up-right"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
