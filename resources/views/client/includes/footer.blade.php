<footer class="site-footer">
    <div class="site-footer-main">
        <div class="site-footer-brand">
            <a href="{{ route('home') }}" class="site-footer-logo" aria-label="Computer Shop home">
                <img src="{{ asset('asset/logo.svg') }}" alt="Computer Shop Logo">
                <div>
                    <strong>Computer Shop</strong>
                    <span>Noida's accessories hub</span>
                </div>
            </a>

            <p>Shop reliable computer accessories, cabinets, cables, keyboards, mice, WiFi adapters and setup essentials for home, office and gaming builds.</p>

            <div class="site-footer-social" aria-label="Social links">
                <a href="#" aria-label="Facebook"><i class="ti ti-brand-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="ti ti-brand-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="ti ti-brand-youtube"></i></a>
                <a href="#" aria-label="WhatsApp"><i class="ti ti-brand-whatsapp"></i></a>
            </div>
        </div>

        <div class="site-footer-column">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('wishlist.index') }}">Wishlist</a></li>
                <li><a href="{{ route('cart.index') }}">Cart</a></li>
                <li><a href="{{ route('reviews.index') }}">Customer Reviews</a></li>
                <li><a href="{{ route('login') }}">Login</a></li>
            </ul>
        </div>

        <div class="site-footer-column">
            <h3>Shop Categories</h3>
            <ul>
                <li><a href="{{ route('home', ['category' => 'cpu-accessories']) }}">CPU Accessories</a></li>
                <li><a href="{{ route('home', ['category' => 'cabinets']) }}">Cabinets</a></li>
                <li><a href="{{ route('home', ['category' => 'wires-cables']) }}">Wires & Cables</a></li>
                <li><a href="{{ route('home', ['category' => 'keyboards']) }}">Keyboards</a></li>
                <li><a href="{{ route('home', ['category' => 'network-adapters']) }}">Network Adapters</a></li>
            </ul>
        </div>

        <div class="site-footer-column">
            <h3>Support</h3>
            <ul class="site-footer-contact">
                <li>
                    <i class="ti ti-map-pin"></i>
                    <span>Noida, Uttar Pradesh</span>
                </li>
                <li>
                    <i class="ti ti-phone"></i>
                    <a href="tel:+917303758943">+91 73037 58943</a>
                </li>
                <li>
                    <i class="ti ti-mail"></i>
                    <a href="mailto:support@computershop.test">support@computershop.test</a>
                </li>
                <li>
                    <i class="ti ti-clock"></i>
                    <span>Mon to Sat, 10 AM - 7 PM</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="site-footer-newsletter">
        <div>
            <h3>Get deals on PC accessories</h3>
            <p>Receive updates for new arrivals, best products and useful setup essentials.</p>
        </div>

        <form class="site-footer-form" action="{{ route('home') }}" method="GET">
            <input type="email" name="email" placeholder="Enter your email" aria-label="Email address">
            <button type="submit">
                <span>Subscribe</span>
                <i class="ti ti-send"></i>
            </button>
        </form>
    </div>

    <div class="site-footer-bottom">
        <span>© {{ now()->year }} Computer Shop. All rights reserved.</span>

        <div class="site-footer-payments" aria-label="Payment methods">
            <span>UPI</span>
            <span>COD</span>
            <span>VISA</span>
            <span>MC</span>
        </div>
    </div>
</footer>
