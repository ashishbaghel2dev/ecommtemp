@php
    $about = $aboutPart;

    $images = collect([
        $about?->image_1 ? asset($about->image_1) : asset('images/about/about-1.jpg'),
        $about?->image_2 ? asset($about->image_2) : null,
        $about?->image_3 ? asset($about->image_3) : null,
    ])->filter()->values();
@endphp

{{-- Owl Carousel CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

<section class="home-about-section py-5" id="about">

    <div class="container">

        <div class="row align-items-center g-5">

            {{-- Left Image Slider --}}
            <div class="col-lg-5">

                <div class="owl-carousel about-image-carousel">

                    @foreach($images as $img)
                        <div class="item">
                            <div class="about-img-box">
                                <img src="{{ $img }}" alt="About Image">
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>

            {{-- Right Content --}}
            <div class="col-lg-7">

                <div class="home-about-content">

                    <span class="about-subtitle">
                        About Us
                    </span>

                    <h2>
                        {{ $about?->title ?? 'Welcome to Go Sowa' }}
                    </h2>

                    @if($about?->description)
                        <div class="about-description">
                            {!! $about->description !!}
                        </div>
                    @endif

                    <div class="row g-3 mt-4">

                        <div class="col-md-6">
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="ti ti-circle-check"></i>
                                </div>
                                <div>
                                    <h5>Premium Quality</h5>
                                    <p>Carefully selected herbs for maximum purity and wellness.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="ti ti-users"></i>
                                </div>
                                <div>
                                    <h5>Expert Team</h5>
                                    <p>Experienced Ayurvedic experts crafting every blend.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="ti ti-leaf"></i>
                                </div>
                                <div>
                                    <h5>100% Natural</h5>
                                    <p>No artificial flavors or chemicals. Pure herbal goodness.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="ti ti-heart"></i>
                                </div>
                                <div>
                                    <h5>Healthy Lifestyle</h5>
                                    <p>Supporting your daily wellness journey naturally.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4">

                        <a href="{{ route('contact') }}" class="about-btn">
                            Contact Us
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- jQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- Owl Carousel JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
$(document).ready(function(){

    $('.about-image-carousel').owlCarousel({
        items:1,
        loop:true,
        margin:20,
        nav:false,
        dots:true,
        autoplay:true,
        autoplayTimeout:4000,
        autoplayHoverPause:true,
        smartSpeed:800
    });

});
</script>

<style>

.home-about-section{
    padding:90px 0;
   background:#effff9;
}

.home-about-content{
    padding-left:20px;
}

.about-subtitle{
    display:inline-block;
    color:#2c7a4b;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:2px;
    margin-bottom:10px;
}

.home-about-content h2{
    font-size:42px;
    line-height:1.3;
    font-weight:700;
    color:#174B33;
    margin-bottom:20px;
}

.about-description{
    color:#666;
    line-height:1.9;
    font-size:16px;
}

.about-img-box{
    height:720px;
    overflow:hidden;
    border-radius:25px;
}

.about-img-box img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}

.feature-box{
    display:flex;
    align-items:flex-start;
    gap:15px;
    background:#fff;
    padding:22px;
    border-radius:16px;
    border:1px solid #e9ecef;
    height:100%;
    transition:.35s;
}

.feature-box:hover{
    transform:translateY(-6px);
    box-shadow:0 15px 40px rgba(0,0,0,.08);
}

.feature-icon{
    width:55px;
    height:55px;
    border-radius:50%;
    background:#eaf7ef;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}

.feature-icon i{
    font-size:28px;
    color:#2c7a4b;
}

.feature-box h5{
    font-size:18px;
    margin-bottom:6px;
    color:#174B33;
    font-weight:700;
}

.feature-box p{
    margin:0;
    color:#666;
    font-size:14px;
    line-height:1.7;
}

.about-btn{
    display:inline-block;
    padding:14px 35px;
    background:#174B33;
    color:#fff;
    border-radius:50px;
    text-decoration:none;
    transition:.3s;
    font-weight:600;
}

.about-btn:hover{
    background:#2c7a4b;
    color:#fff;
}

.about-image-carousel .owl-dots{
    margin-top:20px;
    text-align:center;
}

.about-image-carousel .owl-dot span{
    width:10px;
    height:10px;
    transition:.3s;
}

.about-image-carousel .owl-dot.active span{
    width:28px;
}

@media(max-width:991px){

    .home-about-content{
        padding-left:0;
        margin-top:0px;
    }
.about-image-carousel .owl-dots {
    display: none  !important;
}
    .home-about-content h2{
        font-size:32px;
    }

    .about-img-box{
        height: auto;
    }

}

@media(max-width:576px){

    .about-img-box{
        height:auto;
    }

    .home-about-content h2{
        font-size:28px;
    }

}

</style>