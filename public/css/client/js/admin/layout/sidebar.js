// sidebar.js
function initSidebar() {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("sidebarToggle");
    const icon = document.getElementById("toggleIcon");

    if (!sidebar || !toggleBtn) return;

    /* =========================
       SIDEBAR TOGGLE
    ========================= */
    toggleBtn.addEventListener("click", function () {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("mobile-show");
            sidebar.classList.remove("hide");
        } else {
            sidebar.classList.toggle("hide");
            sidebar.classList.remove("mobile-show");
        }

        // Fix: Clean up all submenu states when toggling sidebar
        document.querySelectorAll(".side-menu").forEach(item => {
            item.classList.remove("active");
            item.classList.remove("open-popup");
        });

        // Update icon based on state
        const isClosed = sidebar.classList.contains("hide") ||
            (window.innerWidth <= 768 && !sidebar.classList.contains("mobile-show"));

        if (isClosed) {
            icon.classList.replace("ti-arrow-autofit-left", "ti-arrow-autofit-right");
        } else {
            icon.classList.replace("ti-arrow-autofit-right", "ti-arrow-autofit-left");
        }
    });

    /* =========================
       SUBMENU HANDLING
    ========================= */
    document.querySelectorAll(".side-menu > a").forEach(link => {
        link.addEventListener("click", function (e) {
            const menu = this.parentElement;
            const isCollapsed = sidebar.classList.contains("hide");
            const hasSubmenu = menu.querySelector(".side-sub-menu");

            if (!hasSubmenu) return;

            e.preventDefault();
            e.stopPropagation();

            if (isCollapsed) {
                // Collapsed mode: Toggle floating popup
                const isOpen = menu.classList.contains("open-popup");

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
}
