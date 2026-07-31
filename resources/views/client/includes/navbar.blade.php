@php
    $navCategories = $navCategories ?? collect();
    $navSocialLinks = $navSocialLinks ?? collect();
    $navBestsellers = $navBestsellers ?? collect();
    $navAllProducts = $navAllProducts ?? collect();
    $navCarouselImage = $navCarouselImage ?? null;
    $siteLogoPath = $siteSettings['site_logo_path'] ?? '';
    $siteName = $siteSettings['user_app_name'] ?? 'Tanvi Fashion Jewellery';
    $brandDisplayName = 'Tanvi Fashion Jewellery';
    $logoFallbackPath = 'uploads/site/1785248312_logo.png';
    $navFeatureImage = $navCarouselImage?->image ?: 'uploads/site/1785248312_logo.png';
    $navFeatureTitle = $navCarouselImage?->title ?: 'Fresh jewellery picks from Tanvi';
    $jewelleryMenu = [
        ['label' => 'All Jewellery', 'icon' => 'ti ti-sparkles', 'dropdown' => true],
        ['label' => 'Gold', 'icon' => 'ti ti-diamond', 'dropdown' => true],
        ['label' => 'Diamond', 'icon' => 'ti ti-diamond'],
        ['label' => 'Earrings', 'icon' => 'ti ti-ear'],
        ['label' => 'Rings', 'icon' => 'ti ti-circle-dot'],
        ['label' => 'Daily Wear', 'icon' => 'ti ti-sparkles'],
        ['label' => 'Gemstone', 'icon' => 'ti ti-rosette'],
        ['label' => 'Wedding', 'icon' => 'ti ti-heart-handshake'],
        ['label' => 'Gifting', 'icon' => 'ti ti-gift'],
        ['label' => 'More', 'icon' => 'ti ti-pi'],
    ];

    
    $fallbackSocials = collect([
        ['name' => 'Facebook', 'url' => '#', 'icon' => 'ti ti-brand-facebook'],
        ['name' => 'Instagram', 'url' => '#', 'icon' => 'ti ti-brand-instagram'],
        ['name' => 'YouTube', 'url' => '#', 'icon' => 'ti ti-brand-youtube'],
    ]);

    $companyLinks = [
        ['label' => 'About Us', 'url' =>  route('about')],
        ['label' => 'FAQ', 'url' => route('client.faqs.index')],
        ['label' => 'Gallery', 'url' => route('client.gallery.index')],
        ['label' => 'Reviews', 'url' => route('reviews.index')],
          ['label' => 'Blog', 'url' => '/blog'],
    ];

    $navbarSearchQuery = request('q', '');

    $socialIconMap = [
        'facebook' => 'ti ti-brand-facebook',
        'instagram' => 'ti ti-brand-instagram',
        'youtube' => 'ti ti-brand-youtube',
        'twitter' => 'ti ti-brand-x',
        'x' => 'ti ti-brand-x',
        'linkedin' => 'ti ti-brand-linkedin',
        'pinterest' => 'ti ti-brand-pinterest',
        'whatsapp' => 'ti ti-brand-whatsapp',
    ];
@endphp

<nav class="main-nav" aria-label="Main navigation">
    <div class="top-bar">
        <div class="topbar-marquee" aria-label="Store announcements">
            <div>
                <span>Announcement: New handcrafted jewellery collections are live.</span>
                <span>Free shipping on selected festive picks.</span>
                <span>Secure checkout with wishlist and cart saved for you.</span>
                <span>Announcement: New handcrafted jewellery collections are live.</span>
            </div>
        </div>
    </div>

    <div class="top-header">
        <div class="header-search-wrap">
            <form action="{{ route('client.products.index') }}" method="GET" class="header-search-form" role="search">
                <label for="navbarSearchInput">Search Tanvi products</label>
                <i class="ti ti-search"></i>
                <input id="navbarSearchInput" type="search" name="q" value="{{ $navbarSearchQuery }}" placeholder="Search for diamond jewellery">
                <button type="submit">Search</button>
                
            </form>
        </div>

        <div class="logo-box">
            <a href="{{ route('home') }}" class="logo" aria-label="{{ $brandDisplayName }} home">
                <img src="{{ asset($siteLogoPath ?: $logoFallbackPath) }}" alt="{{ $brandDisplayName }} Logo">
                <span>{{ $brandDisplayName }}</span>
            </a>
        </div>

        <div class="header-actions">
            <a href="{{ route('client.products.index') }}" class="header-icon-card" aria-label="Explore jewellery">
                <i class="ti ti-diamond"></i>
                <span>Jewellery</span>
            </a>

            <a href="{{ route('client.gallery.index') }}" class="header-icon-card" aria-label="Visit store gallery">
                <i class="ti ti-building-store"></i>
                <span>Store</span>
            </a>

            <a href="{{ route('wishlist.index') }}" class="header-icon-card" aria-label="View wishlist">
                <div class="icon-box">
                    <i class="ti ti-heart"></i>
                    <span data-navbar-wishlist-count>{{ $navWishlistCount ?? 0 }}</span>
                </div>
                <span>Wishlist</span>
            </a>

            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="header-icon-card" aria-label="{{ auth()->check() ? 'Open dashboard' : 'Login account' }}">
                <i class="ti ti-user"></i>
                <span>{{ auth()->check() ? 'Profile' : 'Login' }}</span>
            </a>

            <a href="{{ route('cart.index') }}" class="header-icon-card header-cart-card" aria-label="View cart">
                <div class="icon-box">
                    <i class="ti ti-shopping-bag"></i>
                    <span data-navbar-cart-count>{{ $navCartCount ?? 0 }}</span>
                </div>
                <span>Cart</span>
            </a>
        </div>

        <div class="mobile-header-actions" aria-label="Mobile quick actions">
            <a href="{{ route('client.products.index') }}#productSearch" class="mobile-nav-icon" aria-label="Search products">
                <i class="ti ti-search"></i>
            </a>

            <a href="{{ route('wishlist.index') }}" class="mobile-nav-icon" aria-label="View wishlist">
                <i class="ti ti-heart"></i>
                <span data-navbar-wishlist-count>{{ $navWishlistCount ?? 0 }}</span>
            </a>

            <a href="{{ route('cart.index') }}" class="mobile-nav-icon" aria-label="View cart">
                <i class="ti ti-shopping-cart"></i>
                <span data-navbar-cart-count>{{ $navCartCount ?? 0 }}</span>
            </a>

            <button type="button" class="mobile-menu-toggle" aria-label="Open menu" aria-controls="mobileNavDrawer" aria-expanded="false">
                <i class="ti ti-menu-2"></i>
            </button>
        </div>
    </div>

    <div class="menu-header">
        <ul class="main-menu">
            @foreach($jewelleryMenu as $menuItem)
                <li class="{{ !empty($menuItem['dropdown']) ? 'has-dropdown has-category-dropdown' : '' }}">
                    <a href="{{ route('client.products.index') }}" @if(!empty($menuItem['dropdown'])) aria-haspopup="true" aria-expanded="false" @endif>
                        <i class="{{ $menuItem['icon'] }}"></i>
                        <span>{{ $menuItem['label'] }}</span>
                    </a>

                    @if(!empty($menuItem['dropdown']))
                        <div class="nav-dropdown nav-dropdown-categories">
                            <div class="mega-menu-grid">
                                <aside class="mega-filter-rail">
                                    <a href="{{ route('client.products.index') }}" class="is-active">Category</a>
                                    <a href="{{ route('client.products.index') }}">Price</a>
                                    <a href="{{ route('client.products.index') }}">Occasion</a>
                                    <a href="{{ route('client.products.index') }}">Gold Coin</a>
                                    <a href="{{ route('client.products.index') }}">Men</a>
                                    <a href="{{ route('client.products.index') }}">Metal</a>
                                </aside>

                                <div class="mega-category-panel">
                                    @forelse($navCategories->take(3) as $category)
                                        <div class="category-menu-column">
                                            <div class="category-menu-links">
                                                <a href="{{ route('categories.show', $category->slug) }}">
                                                    <span class="mega-link-icon">
                                                        <i class="{{ $menuItem['icon'] }}"></i>
                                                    </span>
                                                    <strong>All {{ $category->name }}</strong>
                                                </a>

                                                @foreach($category->children->take(3) as $child)
                                                    <a href="{{ route('categories.show', $child->slug) }}">
                                                        <span class="mega-link-icon"><i class="ti ti-sparkles"></i></span>
                                                        <strong>{{ $child->name }}</strong>
                                                    </a>
                                                @endforeach

                                                @foreach($category->products->take(3) as $product)
                                                    <a href="{{ route('products.show', $product->slug) }}">
                                                        <span class="mega-link-icon"><i class="ti ti-diamond"></i></span>
                                                        <strong>{{ $product->name }}</strong>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="category-menu-column">
                                            <div class="category-menu-links">
                                                <span>
                                                    <span class="mega-link-icon"><i class="ti ti-diamond"></i></span>
                                                    <strong>No categories found.</strong>
                                                </span>
                                            </div>
                                        </div>
                                    @endforelse

                                    <div class="mega-view-all">
                                        <span>
                                            <img src="{{ asset($navFeatureImage) }}" alt="">
                                        </span>
                                        <div>
                                            <strong>From Classic to Contemporary.</strong>
                                            <small>Explore 6000+ stunning designs.</small>
                                        </div>
                                        <a href="{{ route('client.products.index') }}">View All</a>
                                    </div>
                                </div>

                                <aside class="category-menu-feature">
                                    <a href="{{ route('client.products.index') }}" class="mega-feature-card">
                                        <img src="{{ asset($navFeatureImage) }}" alt="{{ $navFeatureTitle }}">
                                        <span>{{ $navFeatureTitle }}</span>
                                        <strong>Explore Now <i class="ti ti-arrow-up-right"></i></strong>
                                    </a>
                                </aside>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mobile-nav-overlay" data-mobile-nav-close></div>
    <aside class="mobile-nav-drawer" id="mobileNavDrawer" aria-label="Mobile navigation" aria-hidden="true">
        <div class="mobile-drawer-head">
            <a href="{{ route('home') }}" class="mobile-drawer-logo" aria-label="{{ $siteName }} home">
                <img src="{{ asset($siteLogoPath ?: $logoFallbackPath) }}" alt="{{ $brandDisplayName }} Logo">
            </a>
            <button type="button" class="mobile-drawer-close" aria-label="Close menu" data-mobile-nav-close>
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div class="mobile-account-card">
            <i class="ti ti-user-circle"></i>
            <div>
                @auth
                    <span>{{ auth()->user()->name ?? 'My Account' }}</span>
                    <strong>{{ auth()->user()->email ?? 'Dashboard' }}</strong>
                @else
                    <span>Welcome to Tanvi</span>
                    <strong>Login for faster checkout</strong>
                @endauth
            </div>
        </div>

        <div class="mobile-account-actions">
            @auth
                <a href="{{ route('dashboard') }}"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><i class="ti ti-logout"></i> Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"><i class="ti ti-login-2"></i> Login</a>
                <a href="{{ route('register') }}"><i class="ti ti-user-plus"></i> Register</a>
            @endauth
        </div>

        <nav class="mobile-menu-list" aria-label="Mobile menu links">
            <a href="{{ route('client.products.index') }}#productSearch"><i class="ti ti-search"></i> Search Products</a>
            <a href="{{ route('home') }}"><i class="ti ti-home"></i> Home</a>
            <a href="{{ route('labels.show', 'new-arrived') }}"><i class="ti ti-sparkles"></i> New Arrivals</a>
            <a href="{{ route('labels.show', 'best-product') }}"><i class="ti ti-award"></i> Bestsellers</a>
            <a href="{{ route('client.products.index') }}"><i class="ti ti-diamond"></i> Products</a>
            <a href="{{ route('reviews.index') }}"><i class="ti ti-star"></i> Reviews</a>
            <a href="{{ route('wishlist.index') }}"><i class="ti ti-heart"></i> Wishlist</a>
            <a href="{{ route('cart.index') }}"><i class="ti ti-shopping-cart"></i> Cart</a>
            <a href="{{ route('home') }}#contact"><i class="ti ti-phone"></i> Contact</a>
        </nav>

        <div class="mobile-drawer-section">
            <span>Categories</span>
            <div>
                @forelse($navCategories->take(8) as $category)
                    <a href="{{ route('categories.show', $category->slug) }}">{{ $category->name }}</a>
                @empty
                    <small>No categories found.</small>
                @endforelse
            </div>
        </div>
    </aside>
</nav>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.main-nav');
    const drawer = document.getElementById('mobileNavDrawer');
    const toggle = document.querySelector('.mobile-menu-toggle');
    const closeTargets = document.querySelectorAll('[data-mobile-nav-close]');

    if (!nav || !drawer || !toggle) {
        return;
    }

    const setOpen = (open) => {
        nav.classList.toggle('mobile-nav-is-open', open);
        document.body.classList.toggle('mobile-nav-lock', open);
        drawer.setAttribute('aria-hidden', String(!open));
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => setOpen(true));
    closeTargets.forEach((target) => target.addEventListener('click', () => setOpen(false)));
    drawer.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));

    const topBar = nav.querySelector('.top-bar');
    const topHeader = nav.querySelector('.top-header');

    const syncFixedHeaderOffset = () => {
        const fixedHeight = (topBar?.offsetHeight || 0) + (topHeader?.offsetHeight || 0);
        document.documentElement.style.setProperty('--tanvi-fixed-header-height', `${fixedHeight}px`);
        document.documentElement.style.setProperty('--tanvi-topbar-height', `${topBar?.offsetHeight || 0}px`);
    };

    syncFixedHeaderOffset();
    window.addEventListener('resize', syncFixedHeaderOffset, { passive: true });

    if ('ResizeObserver' in window) {
        const headerObserver = new ResizeObserver(syncFixedHeaderOffset);
        if (topBar) {
            headerObserver.observe(topBar);
        }
        if (topHeader) {
            headerObserver.observe(topHeader);
        }
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
});
</script>
@endpush
