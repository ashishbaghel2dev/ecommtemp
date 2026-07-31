<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AboutPart;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect($this->staticUrls())
            ->merge($this->productUrls())
            ->merge($this->categoryUrls())
            ->merge($this->blogUrls())
            ->values();

        return response()
            ->view('sitemap.xml', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function staticUrls(): array
    {
        $aboutUpdatedAt = AboutPart::query()
            ->where('status', true)
            ->latest('updated_at')
            ->value('updated_at');

        $galleryUpdatedAt = Gallery::query()
            ->where('status', true)
            ->latest('updated_at')
            ->value('updated_at');

        return [
            $this->url(route('home'), now(), 'daily', '1.0'),
            $this->url(route('about'), $aboutUpdatedAt, 'monthly', '0.8'),
            $this->url(route('contact'), null, 'monthly', '0.7'),
            $this->url(route('client.faqs.index'), null, 'monthly', '0.6'),
            $this->url(route('client.gallery.index'), $galleryUpdatedAt, 'weekly', '0.7'),
            $this->url(route('client.blogs.index'), $this->latestBlogDate(), 'weekly', '0.8'),
            $this->url(route('reviews.index'), null, 'weekly', '0.6'),
            $this->url(route('payment-policy'), null, 'yearly', '0.4'),
            $this->url(route('privacy-policy'), null, 'yearly', '0.4'),
            $this->url(route('return-refund-policy'), null, 'yearly', '0.4'),
            $this->url(route('shipping-policy'), null, 'yearly', '0.4'),
            $this->url(route('terms-conditions'), null, 'yearly', '0.4'),
        ];
    }

    private function productUrls()
    {
        return Product::query()
            ->active()
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->map(fn (Product $product) => $this->url(
                route('products.show', $product->slug),
                $product->updated_at,
                'weekly',
                '0.9'
            ));
    }

    private function categoryUrls()
    {
        return Category::query()
            ->active()
            ->sorted()
            ->get(['slug', 'updated_at'])
            ->map(fn (Category $category) => $this->url(
                route('categories.show', $category->slug),
                $category->updated_at,
                'weekly',
                '0.8'
            ));
    }

    private function blogUrls()
    {
        return Blog::query()
            ->where('status', true)
            ->where('publish_status', 'posted')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['slug', 'updated_at'])
            ->map(fn (Blog $blog) => $this->url(
                route('client.blogs.show', $blog->slug),
                $blog->updated_at,
                'weekly',
                '0.8'
            ));
    }

    private function latestBlogDate(): ?Carbon
    {
        $updatedAt = Blog::query()
            ->where('status', true)
            ->where('publish_status', 'posted')
            ->latest('updated_at')
            ->value('updated_at');

        return $updatedAt ? Carbon::parse($updatedAt) : null;
    }

    private function url(string $loc, $lastmod = null, string $changefreq = 'weekly', string $priority = '0.7'): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod ? Carbon::parse($lastmod)->toDateString() : null,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
