<div class="dashboard-sidebar" id="sidebar">


    <div class="app-sidebar-logo">
        <a href="/" class="logo-text">
            <h2>EcommTemp</h2>
        </a>
    </div>


    <ul class="dashboard-menu">

        <li class="side-menu {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="menu-icon ti ti-layout-dashboard"></i>

                <span class="menu-text">Dashboard</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>

        <li class="side-menu {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-shopping-cart"></i>
                <span class="menu-text">Sales</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
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
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('banners.*', 'social-links.*', 'reviews.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-clipboard-check"></i>
                <span class="menu-text">CMS</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="{{ route('banners.index') }}">Banners</a></li>
                <li><a href="{{ route('social-links.index') }}">Social Links</a></li>
            
            </ul>
        </li>
          <li class="side-menu {{ request()->routeIs('#') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-clipboard-check"></i>
                <span class="menu-text">CMS Pages</span>
            </a>

            <ul class="side-sub-menu">
                 <li><a href="#">Overview</a></li>
            </ul>
        </li>


        <li class="side-menu {{ request()->routeIs('marketing.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-chart-bar-popular"></i>
                <span class="menu-text">Marketing</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>

        <li class="side-menu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-chart-dots"></i>
                <span class="menu-text">Reports</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>
        <li class="side-menu {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-settings-cog"></i>
                <span class="menu-text">Settings</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>

        <li class="side-menu {{ request()->routeIs('configuration.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="menu-icon ti ti-tool"></i>
                <span class="menu-text">Configuration</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>

    </ul>


    <button id="sidebarToggle" class="dashboard-toggle-btn">
        <i class="ti ti-arrow-autofit-left" id="toggleIcon"></i>
    </button>

</div>