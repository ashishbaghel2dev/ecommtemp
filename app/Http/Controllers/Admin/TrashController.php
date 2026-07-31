<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPart;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\SocialMediaLink;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class TrashController extends Controller
{
    public function index(string $module = 'blogs'): View
    {
        $config = $this->moduleConfig($module);
        $modelClass = $config['model'];

        $items = $modelClass::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.pages.trash.index', [
            'items' => $items,
            'module' => $module,
            'modules' => $this->modules(),
            'config' => $config,
        ]);
    }

    public function restore(string $module, int $id): RedirectResponse
    {
        $config = $this->moduleConfig($module);
        $item = $config['model']::onlyTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', $config['single'] . ' restored successfully.');
    }

    public function forceDelete(string $module, int $id): RedirectResponse
    {
        $config = $this->moduleConfig($module);
        $item = $config['model']::onlyTrashed()->findOrFail($id);

        $this->deleteFiles($module, $item);
        $this->deleteRelations($module, $item);
        $item->forceDelete();

        return back()->with('success', $config['single'] . ' permanently deleted successfully.');
    }

    private function moduleConfig(string $module): array
    {
        $modules = $this->modules();

        abort_unless(isset($modules[$module]), 404);

        return $modules[$module];
    }

    private function modules(): array
    {
        return [
            'blogs' => [
                'label' => 'Blogs',
                'single' => 'Blog',
                'model' => Blog::class,
                'icon' => 'ti ti-news',
                'primary' => 'title',
                'secondary' => 'slug',
            ],
            'faqs' => [
                'label' => 'FAQ',
                'single' => 'FAQ',
                'model' => Faq::class,
                'icon' => 'ti ti-message-question',
                'primary' => 'question',
                'secondary' => 'written_by',
            ],
            'gallery' => [
                'label' => 'Gallery',
                'single' => 'Gallery image',
                'model' => Gallery::class,
                'icon' => 'ti ti-photo',
                'primary' => 'title',
                'secondary' => 'alt_text',
            ],
            'tags' => [
                'label' => 'Tags',
                'single' => 'Tag',
                'model' => Tag::class,
                'icon' => 'ti ti-tags',
                'primary' => 'title',
                'secondary' => 'slug',
            ],
            'inquiries' => [
                'label' => 'Inquiries',
                'single' => 'Inquiry',
                'model' => Inquiry::class,
                'icon' => 'ti ti-message-circle',
                'primary' => 'name',
                'secondary' => 'phone',
            ],
            'about-parts' => [
                'label' => 'About Parts',
                'single' => 'About part',
                'model' => AboutPart::class,
                'icon' => 'ti ti-info-circle',
                'primary' => 'title',
                'secondary' => 'slug',
            ],
            'banners' => [
                'label' => 'Banners',
                'single' => 'Banner',
                'model' => Banner::class,
                'icon' => 'ti ti-photo-up',
                'primary' => 'position',
                'secondary' => 'link',
            ],
            'social-links' => [
                'label' => 'Social Links',
                'single' => 'Social link',
                'model' => SocialMediaLink::class,
                'icon' => 'ti ti-share',
                'primary' => 'name',
                'secondary' => 'url',
            ],
            'products' => [
                'label' => 'Products',
                'single' => 'Product',
                'model' => Product::class,
                'icon' => 'ti ti-package',
                'primary' => 'name',
                'secondary' => 'sku',
            ],
            'attributes' => [
                'label' => 'Attributes',
                'single' => 'Attribute',
                'model' => Attribute::class,
                'icon' => 'ti ti-list-details',
                'primary' => 'name',
                'secondary' => 'code',
            ],
            'attribute-values' => [
                'label' => 'Attribute Values',
                'single' => 'Attribute value',
                'model' => AttributeValue::class,
                'icon' => 'ti ti-checklist',
                'primary' => 'value',
                'secondary' => 'slug',
            ],
            'categories' => [
                'label' => 'Categories',
                'single' => 'Category',
                'model' => Category::class,
                'icon' => 'ti ti-category',
                'primary' => 'name',
                'secondary' => 'slug',
            ],
        ];
    }

    private function deleteFiles(string $module, Model $item): void
    {
        $paths = match ($module) {
            'blogs' => [$item->image],
            'gallery' => [$item->image],
            'about-parts' => [$item->image_1, $item->image_2, $item->image_3],
            'banners' => [$item->image],
            'categories' => [$item->image, $item->banner],
            'products' => array_merge(
                [$item->image],
                $item->images()->pluck('image')->all(),
                $item->variants()->pluck('image')->filter()->all(),
            ),
            default => [],
        };

        foreach (array_filter($paths) as $path) {
            File::delete(public_path($path));
        }
    }

    private function deleteRelations(string $module, Model $item): void
    {
        if ($module !== 'products') {
            return;
        }

        $item->labels()->detach();
        $item->attributeValues()->delete();
        $item->variants()->delete();
        $item->images()->delete();
    }
}
