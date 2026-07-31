@extends('client.layouts.app')

@section('title', 'Gallery')
@section('meta_description', 'Explore our gallery.')
@section('meta_keywords', 'gallery')
@section('breadcrumb_title', 'Gallery')

@push('styles')
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

    <style>
        .gallery-section{
            padding:70px 0;
        }

        .gallery-heading{
            text-align:center;
            margin-bottom:40px;
        }

        .gallery-heading h2{
            font-size:38px;
            font-weight:700;
            margin-bottom:10px;
        }

        .gallery-heading p{
            color:#666;
            max-width:700px;
            margin:auto;
        }

        .gallery-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:20px;
        }

        .gallery-item{
            overflow:hidden;
            border-radius:12px;
            background:#fff;
            box-shadow:0 8px 25px rgba(0,0,0,.08);
        }

        .gallery-item img{
            width:100%;
            height:auto;
            object-fit:contain;
            display:block;
            transition:.4s;
        }

        .gallery-item:hover img{
            transform:scale(1.08);
        }

        @media(max-width:768px){

            .gallery-heading h2{
                font-size:28px;
            }

            .gallery-grid{
                grid-template-columns:repeat(2,1fr);
                gap:15px;
            }

            .gallery-item img{
                height:180px;
            }
        }

        @media(max-width:576px){

            .gallery-grid{
                grid-template-columns:1fr;
            }

            .gallery-item img{
                height:240px;
            }

        }
    </style>
@endpush

@section('content')

<section class="gallery-section">
    <div class="container">

        <div class="gallery-heading">
       <h2>Explore Our Creative and Offers</h2>
        </div>

        <div class="gallery-grid">

            @forelse($galleries as $gallery)

                <div class="gallery-item">

                    <a
                        href="{{ asset($gallery->image) }}"
                        data-fancybox="gallery"
                        data-caption="{{ $gallery->title }}"
                    >
                        <img
                            src="{{ asset($gallery->image) }}"
                            alt="{{ $gallery->alt_text ?: $gallery->title }}"
                            loading="lazy"
                        >
                    </a>

                </div>

            @empty

                <div class="text-center w-100 py-5">
                    <h4>No Gallery Images Found</h4>
                </div>

            @endforelse

        </div>

    </div>
</section>

@endsection


@push('scripts')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Fancybox -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <script>
        Fancybox.bind("[data-fancybox='gallery']", {

            Toolbar: {
                display: {
                    left: [],
                    middle: [
                        "zoomIn",
                        "zoomOut",
                        "toggle1to1",
                        "rotateCCW",
                        "rotateCW",
                        "flipX",
                        "flipY"
                    ],
                    right: [
                        "slideshow",
                        "thumbs",
                        "close"
                    ]
                }
            },

            Thumbs: {
                autoStart: false
            },

            Images: {
                zoom: true
            },

            animated: true,

            dragToClose: true,

            hideScrollbar: true,

            wheel: "zoom"

        });
    </script>

@endpush