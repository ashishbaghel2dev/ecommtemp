@php
    $adminAppName = $adminSettings['app_name'] ?? 'Go Sowa';
    $adminSubtitle = $adminSettings['admin_subtitle'] ?? 'Go Sowa Admin';
    $dashboardLabel = $adminSettings['dashboard_label'] ?? 'Admin Dashboard';
    $adminLogoPath = $adminSettings['site_logo_path'] ?? '';
    $appInitials = collect(explode(' ', $adminAppName))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
@endphp

<div class="dashboard-sidebar" id="sidebar">

    <div class="app-sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="logo-text">
            <span class="admin-brand-mark">
                @if($adminLogoPath)
                    <img src="{{ asset($adminLogoPath) }}" alt="{{ $adminAppName }} logo">
                @else
                    {{ $appInitials ?: 'GS' }}
                @endif
            </span>
            <span class="admin-brand-copy">
                <strong>{{ $adminAppName }}</strong>
                <small>{{ $adminSubtitle }}</small>
            </span>
        </a>
    </div>


    <ul class="dashboard-menu">

        <li class="side-menu {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="menu-icon ti ti-layout-dashboard"></i>

                <span class="menu-text">{{ $dashboardLabel }}</span>
            </a>
        </li>

        <li class="side-menu {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-shopping-cart"></i>
                <span class="menu-text">Sales</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="{{ route('sales.index') }}">Orders</a></li>
                <li><a href="{{ route('sales.items') }}">Order Items</a></li>
                <li><a href="{{ route('sales.customers') }}">Customers</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('products.*', 'categories.*', 'attributes.*', 'attribute-values.*', 'productlabels.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-package"></i>
                <span class="menu-text">Catalog</span>
            </a>

            <ul class="side-sub-menu">
                   <li><a href="{{ route('products.index') }}">Products</a></li>
                <li><a href="{{ route('categories.index') }}">Categories</a></li>
                 <li><a href="{{ route('attributes.index') }}">Attributes</a></li>
                  <li><a href="{{ route('attribute-values.index') }}">Attribute Values</a></li>
                   <li><a href="{{ route('productlabels.index') }}">Product Labels</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('customers.*' , 'users.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-users"></i>
                <span class="menu-text">Customers</span>
            </a>
            <ul class="side-sub-menu">
                <li><a href="{{ route('users.index') }}">Active Users</a></li>
                <li><a href="{{ route('sales.customers') }}">Customer History</a></li>
                <li><a href="{{ route('wishlists.index') }}">Wishlist</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('banners.*', 'home-carousel-images.*', 'home-popup.*', 'social-links.*', 'admin.reviews.*', 'about-parts.*', 'gallery.*', 'faqs.*', 'blogs.*', 'tags.*', 'inquiries.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-clipboard-check"></i>
                <span class="menu-text">CMS</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="{{ route('banners.index') }}">Banners</a></li>
                <li><a href="{{ route('home-carousel-images.index') }}">Carousel Images</a></li>
                <li><a href="{{ route('home-popup.edit') }}">Home Popup</a></li>
                <li><a href="{{ route('social-links.index') }}">Social Links</a></li>
                <li><a href="{{ url('/admin/dashboard/reviews') }}">Review Requests</a></li>
                <li><a href="{{ route('about-parts.index') }}">About Parts</a></li>
                <li><a href="{{ route('gallery.index') }}">Gallery</a></li>
                <li><a href="{{ route('faqs.index') }}">FAQ</a></li>
                <li><a href="{{ route('blogs.index') }}">Blogs</a></li>
                <li><a href="{{ route('tags.index') }}">Tags</a></li>
                <li><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
            
            </ul>
        </li>

        <li class="side-menu {{ request()->routeIs('marketing.*', 'coupons.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-chart-bar-popular"></i>
                <span class="menu-text">Marketing</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="{{ route('coupons.index') }}">Coupons</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('trash.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-trash"></i>
                <span class="menu-text">Trash</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="{{ route('trash.index', 'blogs') }}">Blogs Trash</a></li>
                <li><a href="{{ route('trash.index', 'faqs') }}">FAQ Trash</a></li>
                <li><a href="{{ route('trash.index', 'gallery') }}">Gallery Trash</a></li>
                <li><a href="{{ route('trash.index', 'tags') }}">Tags Trash</a></li>
                <li><a href="{{ route('trash.index', 'inquiries') }}">Inquiry Trash</a></li>
                <li><a href="{{ route('trash.index', 'about-parts') }}">About Parts Trash</a></li>
                <li><a href="{{ route('trash.index', 'banners') }}">Banners Trash</a></li>
                <li><a href="{{ route('trash.index', 'social-links') }}">Social Links Trash</a></li>
                <li><a href="{{ route('trash.index', 'products') }}">Products Trash</a></li>
                <li><a href="{{ route('trash.index', 'attributes') }}">Attributes Trash</a></li>
                <li><a href="{{ route('trash.index', 'attribute-values') }}">Attribute Values Trash</a></li>
                <li><a href="{{ route('trash.index', 'categories') }}">Categories Trash</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-settings-cog"></i>
                <span class="menu-text">Settings</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="{{ route('settings.general') }}">General</a></li>
                <li><a href="{{ route('settings.theme') }}">Theme</a></li>
                <li><a href="{{ route('settings.costs') }}">Charges</a></li>
                <li><a href="{{ route('settings.search') }}">Search Console</a></li>
            </ul>
        </li>

    </ul>


    <button id="sidebarToggle" class="dashboard-toggle-btn">
        <i class="ti ti-arrow-autofit-left" id="toggleIcon"></i>
    </button>

</div>
