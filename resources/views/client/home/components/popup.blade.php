@if(!empty($homePopup['enabled']))
    <div
        class="home-scroll-popup"
        data-home-scroll-popup
        data-delay="{{ (int) ($homePopup['delay_seconds'] ?? 10) }}"
        data-show-once="{{ !empty($homePopup['show_once']) ? '1' : '0' }}"
        hidden
    >
        <div class="home-scroll-popup-backdrop" data-home-popup-close></div>

        <section class="home-scroll-popup-card" role="dialog" aria-modal="true" aria-labelledby="home-scroll-popup-title">
            <button type="button" class="home-scroll-popup-close" aria-label="Close popup" data-home-popup-close>
                <i class="ti ti-x"></i>
            </button>

            <!-- Media Side -->
            @if(!empty($homePopup['image_path']))
                <div class="home-scroll-popup-media">
                    <img src="{{ asset($homePopup['image_path']) }}" alt="{{ $homePopup['title'] ?? 'Popup Image' }}">
                </div>
            @endif

            <!-- Content Side -->
            <div class="home-scroll-popup-content">
                @if(!empty($homePopup['eyebrow']))
                    <span class="home-popup-eyebrow">{{ $homePopup['eyebrow'] }}</span>
                @else
                    <span class="home-popup-eyebrow">SPECIAL OFFER</span>
                @endif

                <h2 id="home-scroll-popup-title" class="home-popup-title">
                    {!! $homePopup['title'] ?? 'Sip wellness <em>every day</em>' !!}
                </h2>

                <p class="home-popup-body">
                    {{ $homePopup['body'] ?? 'Discover natural herbal tea blends crafted for calm mornings, better routines, and thoughtful gifting. Experience the purity of Go Sowa.' }}
                </p>

                <!-- Static "FIRST10" Coupon Card -->
                <div class="home-popup-coupon-wrapper">
                    <span class="coupon-label">YOUR WELCOME GIFT:</span>
                    <div class="coupon-box">
                        <div class="coupon-details">
                            <span class="coupon-code" id="popupCouponCode">FIRST10</span>
                            <span class="coupon-subtext">10% OFF on your first purchase</span>
                        </div>
                        <button type="button" class="btn-copy-coupon" id="copyCouponBtn" onclick="copyStaticCoupon('FIRST10')">
                            <i class="ti ti-copy" id="copyIcon"></i> 
                            <span id="copyBtnText">COPY</span>
                        </button>
                    </div>
                </div>

                <!-- Call to Action Button -->
                @if(!empty($homePopup['button_text']) && !empty($homePopup['button_url']))
                    <a href="{{ $homePopup['button_url'] }}" class="home-popup-btn">
                        {{ $homePopup['button_text'] }} <i class="ti ti-arrow-right"></i>
                    </a>
                @else
                    <a href="/shop" class="home-popup-btn">
                        SHOP THE COLLECTION <i class="ti ti-arrow-right"></i>
                    </a>
                @endif
            </div>
        </section>
    </div>
@endif
<style>
/* Container & Backdrop */
.home-scroll-popup {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.home-scroll-popup[hidden] {
    display: none;
}

.home-scroll-popup-backdrop {
    position: absolute;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(2px);
}

/* Main Card */
.home-scroll-popup-card {
    position: relative;
    z-index: 10;
    display: flex;
    width: 100%;
    max-width: 820px;
    background-color: #fcf8f5;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Close Button */
.home-scroll-popup-close {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 20;
    background: transparent;
    border: none;
    font-size: 20px;
    color: #333;
    cursor: pointer;
}

/* Media Side */
.home-scroll-popup-media {
    flex: 1 1 45%;
    min-height: 100%;
}

.home-scroll-popup-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Content Side */
.home-scroll-popup-content {
    flex: 1 1 55%;
    padding: 40px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}

/* Eyebrow Badge */
.home-popup-eyebrow {
    background-color: #f7ded0;
    color: #a35d3d;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 20px;
    margin-bottom: 16px;
}

/* Title & Body */
.home-popup-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 32px;
    line-height: 1.2;
    color: #2b3a2b;
    margin: 0 0 12px 0;
}

.home-popup-title em {
    font-style: italic;
    font-weight: normal;
}

.home-popup-body {
    font-size: 14px;
    line-height: 1.6;
    color: #555555;
    margin-bottom: 20px;
}

/* Static Coupon Card Styling */
.home-popup-coupon-wrapper {
    width: 100%;
    margin-bottom: 24px;
}

.coupon-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
    color: #888;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.coupon-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1.5px dashed #d0c5bc;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f7f3ee;
}

.coupon-details {
    display: flex;
    flex-direction: column;
    padding: 8px 16px;
    flex: 1;
}

.coupon-code {
    font-family: 'Courier New', monospace;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 4px;
    color: #2b3a2b;
}

.coupon-subtext {
    font-size: 11px;
    color: #777;
    margin-top: 2px;
}

.btn-copy-coupon {
    background-color: #2f4034;
    color: #ffffff;
    border: none;
    padding: 18px 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.btn-copy-coupon:hover {
    background-color: #1e2b22;
}

.btn-copy-coupon.copied {
    background-color: #4a6b53;
}

/* CTA Button */
.home-popup-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    background-color: #2f4034;
    color: #ffffff;
    text-decoration: none;
    padding: 14px 24px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: background-color 0.2s ease;
}

.home-popup-btn:hover {
    background-color: #1e2b22;
}

/* Mobile Responsive */
@media (max-width: 640px) {
    .home-scroll-popup-card {
        flex-direction: column;
    }
    
    .home-scroll-popup-media {
        height: 180px;
    }

    .home-scroll-popup-content {
        padding: 24px 20px;
    }

    .coupon-box {
        flex-direction: column;
        text-align: center;
    }

    .btn-copy-coupon {
        width: 100%;
        justify-content: center;
        padding: 12px;
    }
}

    </style>

    <script>
        function copyStaticCoupon(code) {
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('copyCouponBtn');
        const btnText = document.getElementById('copyBtnText');
        const icon = document.getElementById('copyIcon');

        // Change button feedback state
        btnText.innerText = 'COPIED!';
        icon.className = 'ti ti-check';
        btn.classList.add('copied');

        // Reset back to original state after 2 seconds
        setTimeout(() => {
            btnText.innerText = 'COPY';
            icon.className = 'ti ti-copy';
            btn.classList.remove('copied');
        }, 2000);
    });
}
    </script>