<div class="dashboard-sidebar" id="sidebar">

    <!-- LOGO -->
    <div class="app-sidebar-logo">
        <a href="/" class="logo-text">
            <h2>EcommTemp</h2>
        </a>
    </div>

    <!-- MENU -->
    <ul class="dashboard-menu">

        <li class="side-menu {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="menu-icon ti ti-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>

        <li class="side-menu {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="menu-icon ti ti-shopping-cart"></i>
                <span class="menu-text">Sales</span>
            </a>

            <ul class="side-sub-menu">
                <li><a href="#">Overview</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </li>

    </ul>

    <!-- TOGGLE -->
    <button id="sidebarToggle" class="dashboard-toggle-btn">
        <i class="ti ti-arrow-autofit-left" id="toggleIcon"></i>
    </button>

</div>

<script>document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("sidebarToggle");
    const icon = document.getElementById("toggleIcon");

    /* =========================
       SIDEBAR TOGGLE
    ========================= */
    toggleBtn.addEventListener("click", function () {
        // Prevent event bubbling if necessary
        
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("mobile-show");
            sidebar.classList.remove("hide"); // Ensure desktop hide doesn't interfere with mobile
        } else {
            sidebar.classList.toggle("hide");
            sidebar.classList.remove("mobile-show"); // Ensure mobile show doesn't interfere
        }

        // Fix: Clean up all submenu states when toggling sidebar to prevent flickering
        document.querySelectorAll(".side-menu").forEach(item => {
            item.classList.remove("active");
            item.classList.remove("open-popup");
        });

        // Determine if sidebar is currently in a "closed" or "collapsed" state
        const isClosed = sidebar.classList.contains("hide") || 
                        (window.innerWidth <= 768 && !sidebar.classList.contains("mobile-show"));

        if (isClosed) {
            icon.classList.remove("ti-arrow-autofit-left");
            icon.classList.add("ti-arrow-autofit-right");
        } else {
            icon.classList.remove("ti-arrow-autofit-right");
            icon.classList.add("ti-arrow-autofit-left");
        }
    });

    /* =========================
       SUBMENU CLICK ONLY WHEN EXPANDED
    ========================= */
    document.querySelectorAll(".side-menu > a").forEach(link => {
        link.addEventListener("click", function (e) {
            e.stopPropagation(); // Prevent bubbling to document click listener
            const menu = this.parentElement;
            const isCollapsed = sidebar.classList.contains("hide");
            const hasSubmenu = menu.querySelector(".side-sub-menu");

            if (!hasSubmenu) return;

            e.preventDefault();

            if (isCollapsed) {
                // Collapsed mode: Toggle floating popup
                const isOpen = menu.classList.contains("open-popup");
                
                // Close all other popups
                document.querySelectorAll(".side-menu").forEach(item => item.classList.remove("open-popup"));
                
                if (!isOpen) {
                    menu.classList.add("open-popup");
                }
            } else {
                // Expanded mode: Standard Accordion
                document.querySelectorAll(".side-menu").forEach(item => {
                    if (item !== menu) {
                        item.classList.remove("active");
                    }
                });
                menu.classList.toggle("active");
            }
        });
    });

    // Close popups when clicking outside the sidebar
    document.addEventListener("click", function (e) {
        if (!sidebar.contains(e.target)) {
            document.querySelectorAll(".side-menu").forEach(item => item.classList.remove("open-popup"));
        }
    });

});</script>