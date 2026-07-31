<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\SitemapController;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class AdminSettingController extends Controller
{
    public function edit()
    {
        return redirect()->route('settings.general');
    }

    public function general()
    {
        return view('admin.pages.settings.index', [
            'settings' => AdminSetting::dashboardConfig(),
        ]);
    }

    public function theme()
    {
        return view('admin.pages.settings.theme', [
            'settings' => AdminSetting::dashboardConfig(),
            'themes' => $this->themes(),
        ]);
    }

    public function costs()
    {
        return view('admin.pages.settings.costs', [
            'settings' => AdminSetting::dashboardConfig(),
        ]);
    }

    public function search()
    {
        return view('admin.pages.settings.search', [
            'settings' => AdminSetting::dashboardConfig(),
            'sitemapPath' => public_path('sitemap.xml'),
        ]);
    }

    public function seo()
    {
        return view('admin.pages.settings.seo', [
            'definitions' => AdminSetting::seoPageDefinitions(),
            'seoPages' => AdminSetting::seoPagesConfig(),
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:80'],
            'user_app_name' => ['required', 'string', 'max:80'],
            'admin_subtitle' => ['required', 'string', 'max:120'],
            'dashboard_label' => ['required', 'string', 'max:80'],
            'site_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('site_logo')) {
            $validated['site_logo_path'] = $this->storeLogo($request);
        }

        AdminSetting::putMany($validated);

        return redirect()
            ->route('settings.general')
            ->with('success', 'General settings updated.');
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'dashboard_theme' => ['required', Rule::in(array_keys($this->themes()))],
        ]);

        AdminSetting::putMany($validated);

        return redirect()
            ->route('settings.theme')
            ->with('success', 'Theme settings updated.');
    }

    public function updateCosts(Request $request)
    {
        $validated = $request->validate([
            'shipping_enabled' => ['nullable', 'boolean'],
            'shipping_amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'shipping_free_above' => ['required', 'numeric', 'min:0', 'max:999999'],
            'shipping_apply_cod' => ['nullable', 'boolean'],
            'shipping_apply_online' => ['nullable', 'boolean'],
            'shipping_product_ids' => ['nullable', 'string', 'max:1000'],
            'tax_enabled' => ['nullable', 'boolean'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'handling_charge' => ['required', 'numeric', 'min:0', 'max:999999'],
            'packaging_charge' => ['required', 'numeric', 'min:0', 'max:999999'],
            'other_charge_label' => ['nullable', 'string', 'max:80'],
            'other_charge_amount' => ['required', 'numeric', 'min:0', 'max:999999'],
        ]);

        $validated['shipping_enabled'] = $request->boolean('shipping_enabled') ? '1' : '0';
        $validated['shipping_apply_cod'] = $request->boolean('shipping_apply_cod') ? '1' : '0';
        $validated['shipping_apply_online'] = $request->boolean('shipping_apply_online') ? '1' : '0';
        $validated['shipping_amount'] = (string) round((float) $validated['shipping_amount'], 2);
        $validated['shipping_free_above'] = (string) round((float) $validated['shipping_free_above'], 2);
        $validated['tax_enabled'] = $request->boolean('tax_enabled') ? '1' : '0';
        $validated['tax_rate'] = (string) round((float) $validated['tax_rate'], 2);
        $validated['handling_charge'] = (string) round((float) $validated['handling_charge'], 2);
        $validated['packaging_charge'] = (string) round((float) $validated['packaging_charge'], 2);
        $validated['other_charge_amount'] = (string) round((float) $validated['other_charge_amount'], 2);
        $validated['other_charge_label'] = $validated['other_charge_label'] ?: 'Other Charge';

        AdminSetting::putMany($validated);

        return redirect()
            ->route('settings.costs')
            ->with('success', 'Charge settings updated.');
    }

    public function updateSearch(Request $request)
    {
        $validated = $request->validate([
            'google_search_console_verification' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['google_search_console_verification'] = $this->extractGoogleVerification(
            $validated['google_search_console_verification'] ?? ''
        );

        AdminSetting::putMany($validated);

        return redirect()
            ->route('settings.search')
            ->with('success', 'Search Console settings updated.');
    }

    public function updateSeo(Request $request)
    {
        $keys = array_keys(AdminSetting::seoPageDefinitions());

        $validated = $request->validate([
            'seo' => ['required', 'array'],
            'seo.*.title' => ['nullable', 'string', 'max:180'],
            'seo.*.description' => ['nullable', 'string', 'max:320'],
            'seo.*.keywords' => ['nullable', 'string', 'max:500'],
        ]);

        $seoPages = [];
        foreach ($keys as $key) {
            $page = $validated['seo'][$key] ?? [];
            $seoPages[$key] = [
                'title' => trim((string) ($page['title'] ?? '')),
                'description' => trim((string) ($page['description'] ?? '')),
                'keywords' => trim((string) ($page['keywords'] ?? '')),
            ];
        }

        AdminSetting::putMany([
            'seo_pages' => json_encode($seoPages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);

        return redirect()
            ->route('settings.seo')
            ->with('success', 'SEO pages updated.');
    }

    public function updateSitemap(SitemapController $sitemapController)
    {
        File::put(public_path('sitemap.xml'), $sitemapController->__invoke()->getContent());

        AdminSetting::putMany([
            'sitemap_last_updated_at' => now()->toDateTimeString(),
        ]);

        return redirect()
            ->route('settings.search')
            ->with('success', 'sitemap.xml updated.');
    }

    private function storeLogo(Request $request): string
    {
        $file = $request->file('site_logo');
        $directory = public_path('uploads/site');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $name = time().'_logo.'.$file->getClientOriginalExtension();
        $file->move($directory, $name);

        return 'uploads/site/'.$name;
    }

    private function themes(): array
    {
        return [
            'teal' => ['label' => 'Teal', 'color' => '#16877d'],
            'navy' => ['label' => 'Navy', 'color' => '#142f79'],
            'blue' => ['label' => 'Blue', 'color' => '#2f66e8'],
            'violet' => ['label' => 'Violet', 'color' => '#7c3aed'],
            'magenta' => ['label' => 'Magenta', 'color' => '#c026d3'],
            'rose' => ['label' => 'Rose', 'color' => '#e11d48'],
            'red' => ['label' => 'Red', 'color' => '#dc2626'],
            'orange' => ['label' => 'Orange', 'color' => '#f97316'],
            'gold' => ['label' => 'Gold', 'color' => '#d79500'],
            'green' => ['label' => 'Green', 'color' => '#16a34a'],
            'cyan' => ['label' => 'Cyan', 'color' => '#0891b2'],
            'slate' => ['label' => 'Slate', 'color' => '#475569'],
        ];
    }

    private function extractGoogleVerification(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/<meta[^>]+name=["\']google-site-verification["\'][^>]+content=["\']([^"\']+)["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+name=["\']google-site-verification["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return strip_tags($value);
    }
}
