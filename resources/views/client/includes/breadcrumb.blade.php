@php
    $routeName = request()->route()?->getName();
    $isHome = $routeName === 'home';
    $crumbs = [['label' => 'Home', 'url' => route('home')]];
    $currentTitle = trim($__env->yieldContent('breadcrumb_title')) ?: trim($__env->yieldContent('title'));
    $currentTitle = preg_replace('/\s*\|\s*Go Sowa\s*$/i', '', $currentTitle);
    $pageIcon = 'ti ti-diamond';
    $breadcrumbImage = trim($__env->yieldContent('breadcrumb_image')) ?: 'banners/1785252221_banner_fb-desktop-2-7-26.webp';

    $routeTitles = [
        'about' => 'About Us',
        'contact' => 'Contact Us',
        'wishlist.index' => 'Wishlist',
        'cart.index' => 'Shopping Cart',
        'checkout.index' => 'Checkout',
        'checkout.review' => 'Review Order',
        'checkout.success' => 'Order Placed',
        'checkout.payment.failed' => 'Payment Failed',
        'client.faqs.index' => 'Frequently Asked Questions',
        'client.gallery.index' => 'Gallery',
        'client.blogs.index' => 'Blogs',
        'client.blogs.alias' => 'Blogs',
        'client.tags.index' => 'Blog Tags',
        'reviews.index' => 'Customer Reviews',
        'dashboard' => 'My Dashboard',
        'dashboard.profile' => 'My Profile',
        'dashboard.profile.edit' => 'Edit Profile',
        'login' => 'Login',
        'register' => 'Register',
        'password.request' => 'Forgot Password',
        'password.otp.form' => 'Reset Password',
        'payment-policy' => 'Payment Policy',
        'privacy-policy' => 'Privacy Policy',
        'return-refund-policy' => 'Return & Refund Policy',
        'shipping-policy' => 'Shipping Policy',
        'terms-conditions' => 'Terms & Conditions',
    ];

    if ($routeName === 'products.show' && isset($product)) {
        $currentTitle = $product->name;
        $breadcrumbImage = $breadcrumbImage;
        if ($product->category) {
            $crumbs[] = ['label' => $product->category->name, 'url' => route('categories.show', $product->category->slug)];
            $breadcrumbImage = $breadcrumbImage ?: $breadcrumbImage ?: $breadcrumbImage;
        }
    } elseif ($routeName === 'categories.show' && isset($category)) {
        $currentTitle = $category->name;
        $breadcrumbImage = $breadcrumbImage ?: $breadcrumbImage ?: $breadcrumbImage;
        $pageIcon = 'ti ti-category-2';
    } elseif (in_array($routeName, ['client.blogs.show'], true) && isset($blog)) {
        $crumbs[] = ['label' => 'Blogs', 'url' => route('client.blogs.index')];
        $currentTitle = $blog->title;
        $breadcrumbImage = $blog->image ?? $breadcrumbImage;
        $pageIcon = 'ti ti-news';
    } elseif ($routeName === 'client.tags.show' && isset($tag)) {
        $crumbs[] = ['label' => 'Blog Tags', 'url' => route('client.tags.index')];
        $currentTitle = $tag->title;
        $pageIcon = 'ti ti-tag';
    } elseif (str_starts_with((string) $routeName, 'dashboard.')) {
        $crumbs[] = ['label' => 'My Dashboard', 'url' => route('dashboard')];
        if ($routeName === 'dashboard.profile.edit') {
            $crumbs[] = ['label' => 'My Profile', 'url' => route('dashboard.profile')];
        }
        $pageIcon = 'ti ti-user-circle';
    } elseif (str_starts_with((string) $routeName, 'checkout.')) {
        $crumbs[] = ['label' => 'Shopping Cart', 'url' => route('cart.index')];
        if (! in_array($routeName, ['checkout.index'], true)) {
            $crumbs[] = ['label' => 'Checkout', 'url' => route('checkout.index')];
        }
        $pageIcon = 'ti ti-shopping-bag-check';
    } elseif (in_array($routeName, ['payment-policy', 'privacy-policy', 'return-refund-policy', 'shipping-policy', 'terms-conditions'], true)) {
        $crumbs[] = ['label' => 'Info', 'url' => null];
        $pageIcon = 'ti ti-file-text';
    } elseif (in_array($routeName, ['client.blogs.index', 'client.blogs.alias'], true)) {
        $pageIcon = 'ti ti-news';
    } elseif (in_array($routeName, ['wishlist.index', 'cart.index'], true)) {
        $pageIcon = $routeName === 'wishlist.index' ? 'ti ti-heart' : 'ti ti-shopping-bag';
    }

    $currentTitle = $currentTitle ?: ($routeTitles[$routeName] ?? 'Page');
@endphp

@unless($isHome || trim($__env->yieldContent('hide_breadcrumb')))
    <section class="site-breadcrumb-wrap" aria-label="Page breadcrumb" style="--breadcrumb-image: url('{{ asset($breadcrumbImage) }}')">
        <div class="container">
            <div class="site-breadcrumb-card">
                <div>
                    <nav class="site-breadcrumb" aria-label="Breadcrumb">
                        @foreach($crumbs as $crumb)
                            @if(!empty($crumb['url']))
                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                            @else
                                <span>{{ $crumb['label'] }}</span>
                            @endif
                            <i class="ti ti-chevron-right" aria-hidden="true"></i>
                        @endforeach
                        <span aria-current="page">{{ $currentTitle }}</span>
                    </nav>
                    <h1>{{ $currentTitle }}</h1>
                </div>
                <span class="site-breadcrumb-icon" aria-hidden="true">
                    <i class="{{ $pageIcon }}"></i>
                </span>
            </div>
        </div>
    </section>
@endunless
