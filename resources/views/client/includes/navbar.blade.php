<!-- Tabler Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

<nav class="main-nav">

   <div class="top-bar">
    <p>Best Computer shop in Noida city</p>
   </div>
    <div class="top-header">

        <a href="{{ route('home') }}" class="logo" aria-label="Computer Shop home">
            <img src="{{ asset('asset/logo.svg') }}" alt="Computer Shop Logo">
        </a>

        <form class="search-container" action="{{ route('home') }}" method="GET">

            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search for products..." aria-label="Search products">

            <button type="submit" aria-label="Search"><i class="ti ti-search"></i></button>

        </form>

        <div class="header-actions">

            <a href="{{ route('login') }}" class="action-item">
                <i class="ti ti-user"></i>
                <div>
                    <span>Login</span>
                    <strong>Account</strong>
                </div>
            </a>

            <a href="{{ route('wishlist.index') }}" class="nav-icon-link" aria-label="View wishlist">
            <div class="icon-box">
                <i class="ti ti-heart"></i>
                    <span data-navbar-wishlist-count>{{ $navWishlistCount ?? 0 }}</span>
            </div>
            </a>

            <a href="{{ route('cart.index') }}" class="action-item2">
                <div class="icon-box">
                    <i class="ti ti-shopping-cart"></i>
                    <span data-navbar-cart-count>{{ $navCartCount ?? 0 }}</span>
                </div>
                <div class="cart-info">
                    <span>Your Cart</span>
                    <strong>Rs. <b data-navbar-cart-total>{{ number_format((float) ($navCartTotal ?? 0), 2) }}</b></strong>
                </div>
            </a>

        </div>

    </div>

    <div class="menu-header">

        <button type="button" class="browse" data-category-drawer-open aria-controls="categoryDrawer" aria-expanded="false">
            <i class="ti ti-menu-2"></i>
            <strong>Browse All Categories</strong>
        </button>

        <ul class="main-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="has-mega-menu">
                <a href="#">Gaming <i class="ti ti-chevron-down"></i></a>
                <div class="mega-menu">
                    <div class="mega-menu-inner">
                        <div class="mega-menu-feature">
                            <span>Gaming</span>
                            <strong>High FPS gear</strong>
                            <p>Build-ready parts, fast displays, and accessories for competitive setups.</p>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Shop Gaming</h3>
                            <a href="#">Gaming laptops</a>
                            <a href="#">Gaming desktops</a>
                            <a href="#">Graphics cards</a>
                            <a href="#">Gaming keyboards</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>By Use</h3>
                            <a href="#">Esports builds</a>
                            <a href="#">4K gaming</a>
                            <a href="#">Streaming ready</a>
                            <a href="#">RGB setups</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Top Brands</h3>
                            <a href="#">Asus ROG</a>
                            <a href="#">MSI Gaming</a>
                            <a href="#">Gigabyte Aorus</a>
                            <a href="#">Corsair</a>
                        </div>
                    </div>
                </div>
            </li>
            <li class="has-mega-menu">
                <a href="#">Streaming <i class="ti ti-chevron-down"></i></a>
                <div class="mega-menu">
                    <div class="mega-menu-inner">
                        <div class="mega-menu-feature">
                            <span>Streaming</span>
                            <strong>Go live cleanly</strong>
                            <p>Cameras, capture cards, microphones, and creator PCs for smooth production.</p>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Creator Gear</h3>
                            <a href="#">Webcams</a>
                            <a href="#">Microphones</a>
                            <a href="#">Capture cards</a>
                            <a href="#">Stream decks</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Lighting</h3>
                            <a href="#">Key lights</a>
                            <a href="#">Ring lights</a>
                            <a href="#">LED panels</a>
                            <a href="#">Background lights</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Workstations</h3>
                            <a href="#">Editing PCs</a>
                            <a href="#">Dual PC setups</a>
                            <a href="#">Storage drives</a>
                            <a href="#">Audio interfaces</a>
                        </div>
                    </div>
                </div>
            </li>
            <li class="has-mega-menu">
                <a href="#">Components <i class="ti ti-chevron-down"></i></a>
                <div class="mega-menu">
                    <div class="mega-menu-inner">
                        <div class="mega-menu-feature">
                            <span>Components</span>
                            <strong>Pick your parts</strong>
                            <p>Core hardware for upgrades, custom builds, and workstation refreshes.</p>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Core Parts</h3>
                            <a href="#">Processors</a>
                            <a href="#">Motherboards</a>
                            <a href="#">Memory</a>
                            <a href="#">Storage</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Performance</h3>
                            <a href="#">Graphics cards</a>
                            <a href="#">Power supplies</a>
                            <a href="#">Liquid cooling</a>
                            <a href="#">Cabinets</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Accessories</h3>
                            <a href="#">Thermal paste</a>
                            <a href="#">Case fans</a>
                            <a href="#">Cable kits</a>
                            <a href="#">Adapters</a>
                        </div>
                    </div>
                </div>
            </li>
            <li class="has-mega-menu">
                <a href="#">PC Builds <i class="ti ti-chevron-down"></i></a>
                <div class="mega-menu">
                    <div class="mega-menu-inner">
                        <div class="mega-menu-feature">
                            <span>PC Builds</span>
                            <strong>Ready to power on</strong>
                            <p>Curated builds for gaming, editing, office work, and compact desks.</p>
                        </div>
                        <div class="mega-menu-column">
                            <h3>By Budget</h3>
                            <a href="#">Under Rs. 50,000</a>
                            <a href="#">Rs. 50,000 - 1 lakh</a>
                            <a href="#">Rs. 1 lakh - 2 lakh</a>
                            <a href="#">Premium builds</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>By Purpose</h3>
                            <a href="#">Gaming PC</a>
                            <a href="#">Editing PC</a>
                            <a href="#">Office PC</a>
                            <a href="#">Mini PC</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Services</h3>
                            <a href="#">Custom quote</a>
                            <a href="#">Assembly</a>
                            <a href="#">Upgrade advice</a>
                            <a href="#">Support plans</a>
                        </div>
                    </div>
                </div>
            </li>
            <li class="has-mega-menu">
                <a href="#">Monitors <i class="ti ti-chevron-down"></i></a>
                <div class="mega-menu">
                    <div class="mega-menu-inner">
                        <div class="mega-menu-feature">
                            <span>Monitors</span>
                            <strong>Sharper screens</strong>
                            <p>Fast refresh gaming panels, color-accurate creator displays, and office screens.</p>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Resolution</h3>
                            <a href="#">Full HD</a>
                            <a href="#">2K QHD</a>
                            <a href="#">4K UHD</a>
                            <a href="#">Ultrawide</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Refresh Rate</h3>
                            <a href="#">75Hz</a>
                            <a href="#">144Hz</a>
                            <a href="#">240Hz</a>
                            <a href="#">OLED monitors</a>
                        </div>
                        <div class="mega-menu-column">
                            <h3>Use Case</h3>
                            <a href="#">Gaming</a>
                            <a href="#">Design</a>
                            <a href="#">Office</a>
                            <a href="#">Console</a>
                        </div>
                    </div>
                </div>
            </li>
            <li><a href="#">Custom PC Quote</a></li>
            <li><a href="#">Our Stores</a></li>
        </ul>

        <div class="deal">
            <i class="ti ti-discount-2"></i>
            <strong>Asus Graphic Cards</strong>
        </div>

    </div>

</nav>

@php
    $navCategories = $navCategories ?? collect();
@endphp

<div class="category-drawer-backdrop" data-category-drawer-close hidden></div>

<aside id="categoryDrawer" class="category-drawer" aria-hidden="true" aria-label="Browse product categories" hidden>
    <div class="category-drawer-sidebar">
        <div class="category-drawer-head">
            <strong>All Categories</strong>
            <button type="button" class="category-drawer-close" data-category-drawer-close aria-label="Close categories">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <ul class="category-drawer-list">
            @forelse($navCategories as $category)
                <li class="category-drawer-item">
                    <a href="{{ route('home', ['category' => $category->slug]) }}" class="category-drawer-link">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="">
                        @else
                            <i class="ti ti-category"></i>
                        @endif
                        <span>{{ $category->name }}</span>
                        <i class="ti ti-chevron-right"></i>
                    </a>

                    <div class="category-drawer-panel">
                        <div class="category-panel-inner">
                            <div class="category-panel-feature">
                                <span>{{ $category->name }}</span>
                                <strong>{{ $category->meta_title ?: $category->name }}</strong>
                                @if($category->description)
                                    <p>{{ $category->description }}</p>
                                @endif
                            </div>

                            <div class="category-panel-links">
                                <h3>{{ $category->name }}</h3>
                                <a href="{{ route('home', ['category' => $category->slug]) }}">View all {{ $category->name }}</a>

                                @forelse($category->children as $child)
                                    <a href="{{ route('home', ['category' => $child->slug]) }}">{{ $child->name }}</a>
                                @empty
                                    <span>No subcategories added yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="category-drawer-empty">No categories found.</li>
            @endforelse
        </ul>
    </div>
</aside>

<script>
    (() => {
        const drawer = document.getElementById('categoryDrawer');
        const openButton = document.querySelector('[data-category-drawer-open]');
        const closeButtons = document.querySelectorAll('[data-category-drawer-close]');
        const backdrop = document.querySelector('.category-drawer-backdrop');
        const items = drawer ? drawer.querySelectorAll('.category-drawer-item') : [];

        if (!drawer || !openButton || !backdrop) {
            return;
        }

        const setActiveItem = (item) => {
            if (!item || item.classList.contains('is-active')) {
                return;
            }

            items.forEach((categoryItem) => categoryItem.classList.remove('is-active'));
            item.classList.add('is-active');
        };

        const clearActiveItem = () => {
            items.forEach((categoryItem) => categoryItem.classList.remove('is-active'));
        };

        const openDrawer = () => {
            drawer.hidden = false;
            drawer.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            openButton.setAttribute('aria-expanded', 'true');
            backdrop.hidden = false;
            document.body.classList.add('category-drawer-open');
            clearActiveItem();
        };

        const closeDrawer = () => {
            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            openButton.setAttribute('aria-expanded', 'false');
            backdrop.hidden = true;
            document.body.classList.remove('category-drawer-open');
            clearActiveItem();
            window.setTimeout(() => {
                if (!drawer.classList.contains('is-open')) {
                    drawer.hidden = true;
                }
            }, 220);
        };

        openButton.addEventListener('click', openDrawer);
        closeButtons.forEach((button) => button.addEventListener('click', closeDrawer));

        drawer.addEventListener('pointerover', (event) => {
            setActiveItem(event.target.closest('.category-drawer-item'));
        });

        drawer.addEventListener('mouseover', (event) => {
            setActiveItem(event.target.closest('.category-drawer-item'));
        });

        drawer.addEventListener('mouseout', (event) => {
            const currentItem = event.target.closest('.category-drawer-item');
            const nextItem = event.relatedTarget?.closest?.('.category-drawer-item');

            if (currentItem && currentItem !== nextItem) {
                currentItem.classList.remove('is-active');
            }
        });

        items.forEach((item) => {
            item.addEventListener('focusin', () => setActiveItem(item));
            item.addEventListener('focusout', (event) => {
                if (!item.contains(event.relatedTarget)) {
                    item.classList.remove('is-active');
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeDrawer();
            }
        });
    })();
</script>
