(function () {
    const defaultDuration = 11000;
    const iconMap = {
        success: 'ti-circle-check',
        error: 'ti-circle-x',
        warning: 'ti-alert-triangle',
        info: 'ti-info-circle',
    };

    const titleMap = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Notice',
    };

    function getViewport() {
        let viewport = document.querySelector('[data-toast-viewport]');

        if (!viewport) {
            viewport = document.createElement('div');
            viewport.className = 'toast-viewport';
            viewport.setAttribute('data-toast-viewport', '');
            viewport.setAttribute('aria-live', 'polite');
            viewport.setAttribute('aria-atomic', 'true');
            document.body.appendChild(viewport);
        }

        return viewport;
    }

    function normalizeToast(input, fallbackType) {
        if (typeof input === 'string') {
            return {
                type: fallbackType || 'info',
                message: input,
            };
        }

        return {
            type: input?.type || fallbackType || 'info',
            title: input?.title,
            message: input?.message || input?.text || '',
            duration: Number(input?.duration || defaultDuration),
        };
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('is-hiding')) {
            return;
        }

        toast.classList.add('is-hiding');
        toast.classList.remove('is-visible');
        window.setTimeout(() => toast.remove(), 220);
    }

    function showToast(input, fallbackType) {
        const options = normalizeToast(input, fallbackType);
        const message = String(options.message || '').trim();

        if (!message) {
            return null;
        }

        const type = ['success', 'error', 'warning', 'info'].includes(options.type) ? options.type : 'info';
        const duration = Number.isFinite(options.duration) ? options.duration : defaultDuration;
        const toast = document.createElement('div');

        toast.className = `app-toast is-${type}`;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.style.setProperty('--toast-duration', `${duration}ms`);
        toast.innerHTML = `
            <span class="app-toast-icon"><i class="ti ${iconMap[type] || iconMap.info}"></i></span>
            <div class="app-toast-body">
                <p class="app-toast-title"></p>
                <p class="app-toast-message"></p>
            </div>
            <button type="button" class="app-toast-close" aria-label="Close notification">
                <i class="ti ti-x"></i>
            </button>
            <div class="app-toast-progress" aria-hidden="true"><span></span></div>
        `;

        toast.querySelector('.app-toast-title').textContent = options.title || titleMap[type] || titleMap.info;
        toast.querySelector('.app-toast-message').textContent = message;
        toast.querySelector('.app-toast-close').addEventListener('click', () => dismissToast(toast));

        getViewport().appendChild(toast);
        window.requestAnimationFrame(() => toast.classList.add('is-visible'));
        window.setTimeout(() => dismissToast(toast), duration);

        return toast;
    }

    function notifyFromPayload(payload) {
        if (!payload) {
            return;
        }

        if (Array.isArray(payload)) {
            payload.forEach((toast) => showToast(toast));
            return;
        }

        showToast(payload);
    }

    window.AppToast = {
        show: showToast,
        success: (message, options = {}) => showToast({ ...options, type: 'success', message }),
        error: (message, options = {}) => showToast({ ...options, type: 'error', message }),
        warning: (message, options = {}) => showToast({ ...options, type: 'warning', message }),
        info: (message, options = {}) => showToast({ ...options, type: 'info', message }),
        dismiss: dismissToast,
    };

    window.updateNavbarWishlistCount = function (count) {
        document.querySelectorAll('[data-navbar-wishlist-count]').forEach((item) => {
            item.textContent = Number.isFinite(Number(count)) ? Number(count) : 0;
        });
    };

    window.updateNavbarCartSummary = function (cart) {
        document.querySelectorAll('[data-navbar-cart-count]').forEach((item) => {
            item.textContent = cart?.total_quantity ?? 0;
        });

        document.querySelectorAll('[data-navbar-cart-total]').forEach((item) => {
            item.textContent = Number(cart?.grand_total ?? cart?.subtotal ?? 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        });
    };

    const nativeAlert = window.alert;
    window.alert = function (message) {
        if (window.AppToast) {
            window.AppToast.error(message);
            return;
        }

        nativeAlert.call(window, message);
    };

    if (typeof window.fetch === 'function') {
        const nativeFetch = window.fetch.bind(window);

        window.fetch = function (...args) {
            const request = args[0];
            const options = args[1] || {};
            const method = String(options.method || request?.method || 'GET').toUpperCase();

            return nativeFetch(...args).then((response) => {
                if (method === 'GET') {
                    return response;
                }

                const contentType = response.headers.get('content-type') || '';

                if (!contentType.includes('application/json')) {
                    return response;
                }

                response.clone().json().then((data) => {
                    const message = typeof data?.message === 'string' ? data.message : '';
                    const url = typeof request === 'string' ? request : request?.url || '';

                    if (url.includes('/wishlist/') && Number.isFinite(Number(data?.count))) {
                        window.updateNavbarWishlistCount(data.count);
                    }

                    if (!message) {
                        return;
                    }

                    const isSuccessful = response.ok && data.status !== false && data.success !== false;
                    showToast({
                        type: isSuccessful ? 'success' : 'error',
                        message,
                    });
                }).catch(() => {});

                return response;
            }).catch((error) => {
                showToast({
                    type: 'error',
                    message: error?.message || 'Something went wrong. Please try again.',
                });

                throw error;
            });
        };
    }

    document.addEventListener('app:toast', (event) => notifyFromPayload(event.detail));
    document.addEventListener('DOMContentLoaded', () => notifyFromPayload(window.__APP_TOASTS__ || []));
}());
