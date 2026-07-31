<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AdminSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function defaults(): array
    {
        return [
            'app_name' => 'Go Sowa',
            'user_app_name' => 'Go Sowa',
            'admin_subtitle' => 'Go Sowa Admin',
            'dashboard_label' => 'Admin Dashboard',
            'dashboard_theme' => 'green',
            'site_logo_path' => '',
            'shipping_enabled' => '1',
            'shipping_amount' => '50',
            'shipping_free_above' => '600',
            'shipping_apply_cod' => '1',
            'shipping_apply_online' => '1',
            'shipping_product_ids' => '',
            'tax_enabled' => '0',
            'tax_rate' => '0',
            'handling_charge' => '0',
            'packaging_charge' => '0',
            'other_charge_label' => 'Other Charge',
            'other_charge_amount' => '0',
            'google_search_console_verification' => '',
            'sitemap_last_updated_at' => '',
            'seo_pages' => '',
            'home_popup_enabled' => '0',
            'home_popup_eyebrow' => 'Special Offer',
            'home_popup_title' => 'Sip wellness every day',
            'home_popup_body' => 'Discover natural herbal tea blends crafted for calm mornings, better routines, and thoughtful gifting.',
            'home_popup_button_text' => 'Shop Now',
            'home_popup_button_url' => '/',
            'home_popup_image_path' => '',
            'home_popup_delay_seconds' => '10',
            'home_popup_show_once' => '1',
        ];
    }

    public static function dashboardConfig(): array
    {
        $defaults = self::defaults();

        if (! Schema::hasTable('admin_settings')) {
            return $defaults;
        }

        $saved = self::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->all();

        return array_merge($defaults, $saved);
    }

    public static function shippingConfig(): array
    {
        $settings = self::dashboardConfig();

        return [
            'shipping_enabled' => (bool) (int) ($settings['shipping_enabled'] ?? 1),
            'shipping_amount' => (float) ($settings['shipping_amount'] ?? 79),
            'shipping_free_above' => (float) ($settings['shipping_free_above'] ?? 999),
            'shipping_apply_cod' => (bool) (int) ($settings['shipping_apply_cod'] ?? 1),
            'shipping_apply_online' => (bool) (int) ($settings['shipping_apply_online'] ?? 1),
            'shipping_product_ids' => (string) ($settings['shipping_product_ids'] ?? ''),
        ];
    }

    public static function taxConfig(): array
    {
        $settings = self::dashboardConfig();

        return [
            'tax_enabled' => (bool) (int) ($settings['tax_enabled'] ?? 0),
            'tax_rate' => (float) ($settings['tax_rate'] ?? 0),
        ];
    }

    public static function homePopupConfig(): array
    {
        $settings = self::dashboardConfig();

        return [
            'enabled' => (bool) (int) ($settings['home_popup_enabled'] ?? 0),
            'eyebrow' => (string) ($settings['home_popup_eyebrow'] ?? ''),
            'title' => (string) ($settings['home_popup_title'] ?? ''),
            'body' => (string) ($settings['home_popup_body'] ?? ''),
            'button_text' => (string) ($settings['home_popup_button_text'] ?? ''),
            'button_url' => (string) ($settings['home_popup_button_url'] ?? ''),
            'image_path' => (string) ($settings['home_popup_image_path'] ?? ''),
            'delay_seconds' => max(1, (int) ($settings['home_popup_delay_seconds'] ?? 10)),
            'show_once' => (bool) (int) ($settings['home_popup_show_once'] ?? 1),
        ];
    }

    public static function seoPageDefinitions(): array
    {
        return [
            'home' => ['label' => 'Home', 'route' => 'home', 'path' => '/'],
            'about' => ['label' => 'About', 'route' => 'about', 'path' => '/about'],
            'gallery' => ['label' => 'Gallery', 'route' => 'client.gallery.index', 'path' => '/gallery'],
            'contact' => ['label' => 'Contact', 'route' => 'contact', 'path' => '/contact'],
            'faq' => ['label' => 'FAQ', 'route' => 'client.faqs.index', 'path' => '/faq'],
            'products' => ['label' => 'All Products', 'route' => 'client.products.index', 'path' => '/products'],
            'blogs' => ['label' => 'Blogs Listing', 'route' => 'client.blogs.index', 'path' => '/blog'],
            'tags' => ['label' => 'Tags Listing', 'route' => 'client.tags.index', 'path' => '/tags'],
            'reviews' => ['label' => 'Reviews', 'route' => 'reviews.index', 'path' => '/reviews'],
            'cart' => ['label' => 'Cart', 'route' => 'cart.index', 'path' => '/cart'],
            'checkout' => ['label' => 'Checkout', 'route' => 'checkout.index', 'path' => '/checkout'],
            'login' => ['label' => 'Login', 'route' => 'login', 'path' => '/login'],
            'register' => ['label' => 'Register', 'route' => 'register', 'path' => '/register'],
            'payment_policy' => ['label' => 'Payment Policy', 'route' => 'payment-policy', 'path' => '/payment-policy'],
            'privacy_policy' => ['label' => 'Privacy Policy', 'route' => 'privacy-policy', 'path' => '/privacy-policy'],
            'return_refund_policy' => ['label' => 'Return & Refund Policy', 'route' => 'return-refund-policy', 'path' => '/return-refund-policy'],
            'shipping_policy' => ['label' => 'Shipping Policy', 'route' => 'shipping-policy', 'path' => '/shipping-policy'],
            'terms_conditions' => ['label' => 'Terms & Conditions', 'route' => 'terms-conditions', 'path' => '/terms-conditions'],
        ];
    }

    public static function defaultSeoPages(): array
    {
        $brand = 'Go Sowa';

        return [
            'home' => [
                'title' => 'Go Sowa Herbal Tea | Premium Wellness Tea Blends',
                'description' => 'Shop Go Sowa herbal tea blends crafted for natural wellness, mindful routines, gifting, and everyday refreshment.',
                'keywords' => 'Go Sowa, herbal tea, wellness tea, premium tea blends, natural tea',
            ],
            'about' => [
                'title' => 'About Go Sowa | Herbal Tea & Wellness Brand',
                'description' => 'Learn about Go Sowa, our herbal tea journey, quality standards, wellness focus, and passion for natural tea blends.',
                'keywords' => 'about Go Sowa, herbal tea brand, wellness tea company',
            ],
            'gallery' => [
                'title' => 'Go Sowa Gallery | Herbal Tea Products & Moments',
                'description' => 'Explore Go Sowa gallery images featuring herbal tea products, wellness moments, packaging, and brand updates.',
                'keywords' => 'Go Sowa gallery, herbal tea photos, tea product images',
            ],
            'contact' => [
                'title' => 'Contact Go Sowa | Herbal Tea Support & Bulk Orders',
                'description' => 'Contact Go Sowa for herbal tea orders, product support, wholesale inquiries, dealership opportunities, and customer care.',
                'keywords' => 'Go Sowa contact, herbal tea support, bulk tea orders, wholesale tea',
            ],
            'faq' => [
                'title' => 'Go Sowa FAQs | Herbal Tea Questions Answered',
                'description' => 'Find answers to common questions about Go Sowa herbal teas, orders, shipping, returns, wellness blends, and support.',
                'keywords' => 'Go Sowa FAQ, herbal tea questions, tea shipping, tea returns',
            ],
            'products' => [
                'title' => 'All Herbal Tea Products | Go Sowa',
                'description' => 'Shop all Go Sowa herbal tea products with filters for price, category, availability, best sellers, and wellness tea blends.',
                'keywords' => 'Go Sowa products, herbal tea products, wellness tea, best herbal tea, buy tea online',
            ],
            'blogs' => [
                'title' => 'Go Sowa Blogs | Herbal Tea & Wellness Insights',
                'description' => 'Read Go Sowa blogs about herbal tea benefits, wellness routines, tea buying guides, product education, and natural living.',
                'keywords' => 'Go Sowa blogs, herbal tea blog, wellness tips, tea buying guide',
            ],
            'tags' => [
                'title' => 'Go Sowa Topics | Herbal Tea Articles by Tag',
                'description' => 'Browse Go Sowa blog topics and tags covering herbal tea, wellness, tea blends, ingredients, and natural routines.',
                'keywords' => 'Go Sowa tags, herbal tea topics, wellness articles',
            ],
            'reviews' => [
                'title' => 'Go Sowa Reviews | Customer Herbal Tea Feedback',
                'description' => 'Read customer reviews and feedback for Go Sowa herbal tea products, wellness blends, packaging, and shopping experience.',
                'keywords' => 'Go Sowa reviews, herbal tea reviews, customer feedback',
            ],
            'cart' => [
                'title' => 'Shopping Cart | '.$brand,
                'description' => 'Review your Go Sowa cart, update herbal tea quantities, apply offers, and continue to secure checkout.',
                'keywords' => 'Go Sowa cart, herbal tea cart, shopping cart',
            ],
            'checkout' => [
                'title' => 'Checkout | '.$brand,
                'description' => 'Complete your Go Sowa herbal tea order with shipping details, payment options, coupons, and secure checkout.',
                'keywords' => 'Go Sowa checkout, herbal tea order, secure checkout',
            ],
            'login' => [
                'title' => 'Login | '.$brand,
                'description' => 'Login to your Go Sowa account to manage orders, addresses, wishlist, and herbal tea purchases.',
                'keywords' => 'Go Sowa login, customer account, herbal tea account',
            ],
            'register' => [
                'title' => 'Register | '.$brand,
                'description' => 'Create a Go Sowa account for faster checkout, order tracking, wishlist access, and herbal tea offers.',
                'keywords' => 'Go Sowa register, create account, tea offers',
            ],
            'payment_policy' => [
                'title' => 'Payment Policy | '.$brand,
                'description' => 'Read the Go Sowa payment policy for accepted payment methods, prepaid orders, COD, payment security, and support.',
                'keywords' => 'Go Sowa payment policy, tea payment, COD policy',
            ],
            'privacy_policy' => [
                'title' => 'Privacy Policy | '.$brand,
                'description' => 'Read the Go Sowa privacy policy to understand how customer data, cookies, account details, and order information are handled.',
                'keywords' => 'Go Sowa privacy policy, customer data, cookies policy',
            ],
            'return_refund_policy' => [
                'title' => 'Return & Refund Policy | '.$brand,
                'description' => 'Review the Go Sowa return and refund policy for order issues, replacements, refunds, eligibility, and support steps.',
                'keywords' => 'Go Sowa return policy, refund policy, tea returns',
            ],
            'shipping_policy' => [
                'title' => 'Shipping Policy | '.$brand,
                'description' => 'Read the Go Sowa shipping policy for delivery timelines, shipping charges, COD fees, dispatch details, and support.',
                'keywords' => 'Go Sowa shipping policy, tea delivery, COD shipping',
            ],
            'terms_conditions' => [
                'title' => 'Terms & Conditions | '.$brand,
                'description' => 'Read Go Sowa terms and conditions for website use, orders, products, payments, shipping, returns, and customer responsibilities.',
                'keywords' => 'Go Sowa terms, terms and conditions, tea store policy',
            ],
        ];
    }

    public static function seoPagesConfig(): array
    {
        $settings = self::dashboardConfig();
        $saved = json_decode((string) ($settings['seo_pages'] ?? ''), true);
        $saved = is_array($saved) ? $saved : [];

        return collect(self::defaultSeoPages())
            ->mapWithKeys(fn (array $defaults, string $key) => [$key => array_merge($defaults, $saved[$key] ?? [])])
            ->all();
    }

    public static function seoForRouteName(?string $routeName): array
    {
        if (! $routeName) {
            return [];
        }

        foreach (self::seoPageDefinitions() as $key => $definition) {
            if (($definition['route'] ?? null) === $routeName) {
                return self::seoPagesConfig()[$key] ?? [];
            }
        }

        if ($routeName === 'client.blogs.alias') {
            return self::seoPagesConfig()['blogs'] ?? [];
        }

        return [];
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
