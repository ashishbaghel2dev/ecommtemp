@php
    $footerCategories = $footerCategories ?? collect();
    $footerSocialLinks = $footerSocialLinks ?? collect();
    $siteLogoPath = $siteSettings['site_logo_path'] ?? '';
    $siteName = $siteSettings['user_app_name'] ?? 'Tanvi Fashion Jewellery';

    $footerSocialIconMap = [
        'facebook' => 'ti ti-brand-facebook',
        'instagram' => 'ti ti-brand-instagram',
        'youtube' => 'ti ti-brand-youtube',
        'twitter' => 'ti ti-brand-x',
        'x' => 'ti ti-brand-x',
        'linkedin' => 'ti ti-brand-linkedin',
        'pinterest' => 'ti ti-brand-pinterest',
        'whatsapp' => 'ti ti-brand-whatsapp',
    ];

    $fallbackFooterSocials = collect([
        ['name' => 'Instagram', 'url' => '#', 'icon' => 'ti ti-brand-instagram'],
        ['name' => 'Facebook', 'url' => '#', 'icon' => 'ti ti-brand-facebook'],
        ['name' => 'Pinterest', 'url' => '#', 'icon' => 'ti ti-brand-pinterest'],
        ['name' => 'WhatsApp', 'url' => 'https://wa.me/919818610666', 'icon' => 'ti ti-brand-whatsapp'],
    ]);

    $seoTags = [
        'Best Jewellery Seller in Noida',
        'Best Wholesale Jewellery in Delhi NCR',
        'Wholesale Jewellery in Gurgaon',
        'Fashion Jewellery Supplier in Delhi',
        'Artificial Jewellery Wholesaler',
        'Best Jewellery Supplier in Ghaziabad',
        'Bulk Jewellery for Resellers',
        'Wholesale Earrings in India',
        'Wholesale Bangles Supplier',
        'Necklace Set Wholesaler',
        'Mangalsutra Wholesale Dealer',
        'Bridal Jewellery Wholesale',
        'Boutique Jewellery Supplier',
        'Jewellery for Resale Business',
        'All India Jewellery Delivery',
        'Premium Fashion Jewellery',
    ];

    $primeLocations = [
        'Delhi NCR',
        'Noida',
        'Greater Noida',
        'Ghaziabad',
        'Gurgaon',
        'Faridabad',
        'New Delhi',
        'Mumbai',
        'Pune',
        'Ahmedabad',
        'Surat',
        'Jaipur',
        'Lucknow',
        'Kanpur',
        'Chandigarh',
        'Ludhiana',
        'Amritsar',
        'Indore',
        'Bhopal',
        'Kolkata',
        'Patna',
        'Ranchi',
        'Bhubaneswar',
        'Hyderabad',
        'Bengaluru',
        'Chennai',
        'Coimbatore',
        'Kochi',
        'Thiruvananthapuram',
        'Goa',
        'Nagpur',
        'Raipur',
        'Guwahati',
        'Dehradun',
        'Jammu',
    ];
@endphp

<footer class="site-footer">
    <div class="site-footer-tags" aria-label="Popular jewellery keywords">
        <div class="site-footer-tags-inner">
            <p class="site-footer-tags-title">Popular Searches</p>
            <div class="site-footer-tag-list">
                @foreach($seoTags as $tag)
                    <a href="{{ route('client.products.index') }}?q={{ urlencode($tag) }}">{{ $tag }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="site-footer-prime" aria-label="Our prime location">
        <div class="site-footer-prime-inner">
            <div class="site-footer-prime-head">
                <span>Our Prime Location</span>
                <strong>Serving jewellery buyers across India</strong>
            </div>

            <div class="site-footer-location-list">
                @foreach($primeLocations as $location)
                    <a href="{{ route('client.products.index') }}?q={{ urlencode('Wholesale jewellery in ' . $location) }}">{{ $location }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="site-footer-shell">
        <div class="site-footer-main">
            <div class="site-footer-brand">
                <a href="{{ route('home') }}" class="site-footer-logo" aria-label="{{ $siteName }} home">
                    <img src="{{ asset($siteLogoPath ?: 'asset/logo.svg') }}" alt="{{ $siteName }}">
                    <span>
                        <strong>{{ $siteName }}</strong>
                        <small>Wholesale fashion jewellery across India</small>
                    </span>
                </a>

                <p>
                    Curated jewellery collections for resellers, boutiques, gifting, and festive shopping with reliable ordering and careful packing.
                </p>

                <div class="site-footer-location">
                    <i class="ti ti-map-pin-heart"></i>
                    <div>
                        <strong>Our Location</strong>
                        <span>All over India</span>
                    </div>
                </div>
            </div>

            <div class="site-footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('client.products.index') }}">All Products</a></li>
                    @forelse($footerCategories->take(4) as $category)
                        <li><a href="{{ route('categories.show', $category->slug) }}">{{ $category->name }}</a></li>
                    @empty
                        <li><a href="{{ route('client.products.index') }}">Jewellery</a></li>
                    @endforelse
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>

            <div class="site-footer-column">
                <h3>Our Info</h3>
                <ul>
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('payment-policy') }}">Payment Policy</a></li>
                    <li><a href="{{ route('shipping-policy') }}">Shipping Policy</a></li>
                    <li><a href="{{ route('terms-conditions') }}">Terms & Conditions</a></li>
                    <li><a href="{{ route('return-refund-policy') }}">Return & Refund Policy</a></li>
                </ul>
            </div>

            <div class="site-footer-column site-footer-contact-block">
                <h3>Our Contact</h3>
                <ul class="site-footer-contact">
                    <li>
                        <i class="ti ti-phone"></i>
                        <a href="tel:+919818610666">+91 9818610666</a>
                    </li>
                    <li>
                        <i class="ti ti-mail"></i>
                        <a href="mailto:support@tanvifashionjewellery.com">support@tanvifashionjewellery.com</a>
                    </li>
                    <li>
                        <i class="ti ti-map-pin"></i>
                        <span>Serving customers, boutiques, and resellers across India.</span>
                    </li>
                </ul>

                <div class="site-footer-social" aria-label="Social links">
                    @forelse($footerSocialLinks as $socialLink)
                        @php
                            $socialKey = strtolower($socialLink->slug ?: $socialLink->name);
                            $iconClass = str_starts_with((string) $socialLink->icon, 'ti ')
                                ? $socialLink->icon
                                : ($footerSocialIconMap[$socialKey] ?? 'ti ti-link');
                        @endphp
                        <a href="{{ $socialLink->url }}" target="_blank" rel="noopener" aria-label="{{ $socialLink->name }}">
                            <i class="{{ $iconClass }}"></i>
                        </a>
                    @empty
                        @foreach($fallbackFooterSocials as $socialLink)
                            <a href="{{ $socialLink['url'] }}" @if($socialLink['url'] !== '#') target="_blank" rel="noopener" @endif aria-label="{{ $socialLink['name'] }}">
                                <i class="{{ $socialLink['icon'] }}"></i>
                            </a>
                        @endforeach
                    @endforelse
                </div>
            </div>

            <div class="site-footer-column site-footer-business">
                <h3>Business Details</h3>
                <div class="site-footer-business-list">
                    <div>
                        <span>Business Name</span>
                        <strong>Tanvi Fashion Jewellery</strong>
                    </div>
                    <div>
                        <span>GST Details</span>
                        <strong>Available on invoice</strong>
                    </div>
                    <div>
                        <span>Service Area</span>
                        <strong>All over India</strong>
                    </div>
                </div>

                <h3 class="site-footer-payment-title">Mode of Payment</h3>
                <div class="site-footer-payments" aria-label="Accepted payment options">
                    <span>UPI</span>
                    <span>Visa</span>
                    <span>RuPay</span>
                    <span>GPay</span>
                    <span>Paytm</span>
                    <span>COD</span>
                </div>
            </div>
        </div>

        <div class="site-footer-bottom">
            <p>© {{ now()->year }} {{ $siteName }}. All rights reserved.</p>
            <p>Developed by <span class="developer">❤️ Ashish Baghel</span></p>
        </div>
    </div>
</footer>
