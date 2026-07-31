<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $routeSeo = \App\Models\AdminSetting::seoForRouteName(request()->route()?->getName());
        $yieldTitle = trim($__env->yieldContent('title'));
        $yieldDescription = trim($__env->yieldContent('meta_description'));
        $yieldKeywords = trim($__env->yieldContent('meta_keywords'));
        $seoTitle = trim($routeSeo['title'] ?? '') ?: ($yieldTitle ?: ($siteSettings['user_app_name'] ?? $siteSettings['app_name'] ?? 'Go Sowa'));
        $seoDescription = trim($routeSeo['description'] ?? '') ?: $yieldDescription;
        $seoKeywords = trim($routeSeo['keywords'] ?? '') ?: $yieldKeywords;
        $seoImage = trim($__env->yieldContent('seo_image')) ?: asset($siteSettings['site_logo_path'] ?: 'asset/logo.svg');
        $canonicalUrl = trim($__env->yieldContent('canonical')) ?: url()->current();
    @endphp
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $seoTitle }}</title>
    @if($seoDescription)
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    @if($seoDescription)
        <meta property="og:description" content="{{ $seoDescription }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    @if($seoDescription)
        <meta name="twitter:description" content="{{ $seoDescription }}">
    @endif
    <meta name="twitter:image" content="{{ $seoImage }}">
    @if(!empty($siteSettings['google_search_console_verification']))
        <meta name="google-site-verification" content="{{ $siteSettings['google_search_console_verification'] }}">
    @endif
    @stack('head')
     
    <link rel="stylesheet" href="{{ asset('css/client/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    @stack('styles')
</head>
<body>

    @include('client.includes.navbar')


    <main>
        @include('client.includes.breadcrumb')
        @yield('content')
    </main>

    @include('client.includes.footer')
<button type="button" class="back-to-top-btn" id="backToTop" aria-label="Back to top">
    <i class="ti ti-arrow-up"></i>
</button>


<div class="floating-contact">

    <a href="https://wa.me/919818610666"
       target="_blank"
       class="floating-btn whatsapp"
       aria-label="Chat on WhatsApp">
        <i class="ti ti-brand-whatsapp"></i>
    </a>

    <a href="tel:+919818610666"
       class="floating-btn call"
       aria-label="Call Now">
        <i class="ti ti-phone-call"></i>
    </a>

</div>


<script>
    const backToTopBtn = document.getElementById("backToTop");

// Show/Hide button
window.addEventListener("scroll", () => {
    if (window.scrollY > 300) {
        backToTopBtn.classList.add("show");
    } else {
        backToTopBtn.classList.remove("show");
    }
});

// Scroll to top
backToTopBtn.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@php
    $toasts = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('status') ? ['type' => 'success', 'message' => session('status')] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
        session('info') ? ['type' => 'info', 'message' => session('info')] : null,
    ])->filter()->values();

    if (isset($errors) && $errors->any()) {
        $toasts = $toasts->merge(
            collect($errors->all())->map(fn ($message) => ['type' => 'error', 'message' => $message])
        );
    }
@endphp
<script>
    window.__APP_TOASTS__ = @json($toasts->values());
</script>
<script src="{{ asset('js/toast.js') }}"></script>
<script>
    (() => {
        const button = document.querySelector('[data-back-to-top]');
        if (!button) {
            return;
        }

        const toggleButton = () => {
            button.classList.toggle('is-visible', window.scrollY > 420);
        };

        button.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });

        window.addEventListener('scroll', toggleButton, { passive: true });
        toggleButton();
    })();
</script>
@stack('scripts')
</body>
</html>
