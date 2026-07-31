<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\ProductLabel;



class ProductController 
{
    /*
    |--------------------------------------------------------------------------
    | LIST PRODUCTS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $products = Product::with(['category', 'images', 'variants', 'labels'])
            ->latest()
            ->paginate(20);

        return view('admin.pages.products.index', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */

public function create()
{
    $this->ensureDefaultProductLabels();

    $categories = $this->categoryOptions();
    $labels = ProductLabel::where('is_active', true)->orderBy('name')->get();
    $attributesByCategory = $this->attributesByCategory();

    return view(
        'admin.pages.products.create',
        compact('categories', 'labels', 'attributesByCategory')
    );
}
    /*
    |--------------------------------------------------------------------------
    | STORE PRODUCT
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'discount_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'nullable|integer|min:0',
            'min_order_qty' => 'nullable|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:product_labels,id',
            'attributes' => 'nullable|array',
            'type' => 'required|in:simple,configurable',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:255|distinct|unique:product_variants,sku',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.sale_price' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.attributes' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {

            // 1. CREATE PRODUCT
            $imagePaths = $this->storeImages($request);
            $imagePath = $imagePaths[0] ?? $this->storeImage($request);

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $this->uniqueSlug($request->name),
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'sale_price' => $request->sale_price,
                'sale_start' => $request->sale_start,
                'sale_end' => $request->sale_end,
                'stock' => $request->stock ?? 0,
                'min_order_qty' => $request->min_order_qty ?? 1,
                'manage_stock' => $request->has('manage_stock'),
                'in_stock' => $request->has('in_stock'),
                'image' => $imagePath,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'type' => $request->type ?? 'simple',
            ]);

            foreach ($imagePaths ?: array_filter([$imagePath]) as $index => $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_main' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            $product->labels()->sync($request->input('labels', []));
            $this->syncAttributes($product, $request->input('attributes', []));

            if ($product->type === 'configurable') {
                $this->syncVariants($product, $request->input('variants', []));
            } else {
                $product->variants()->delete();
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT FORM
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $this->ensureDefaultProductLabels();

        $product = Product::with([
                'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('sort_order')->orderBy('id'),
                'attributeValues.attributeValue',
                'variants',
                'labels',
            ])
            ->findOrFail($id);
        $categories = $this->categoryOptions();
        $labels = ProductLabel::where('is_active', true)->orderBy('name')->get();
        $attributesByCategory = $this->attributesByCategory();
        $selectedAttributes = $product->attributeValues
            ->groupBy('attribute_id')
            ->mapWithKeys(function ($items, $attributeId) {
                return [
                    $attributeId => [
                        'attribute_value_ids' => $items->pluck('attribute_value_id')->filter()->values(),
                        'value' => optional($items->first())->value,
                    ],
                ];
            });
        $selectedVariants = old(
            'variants',
            $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'stock' => $variant->stock,
                'in_stock' => $variant->in_stock,
                'attributes' => $variant->attributes ?: [],
            ])->values()->all()
        );

        return view(
            'admin.pages.products.edit',
            compact('product', 'categories', 'labels', 'attributesByCategory', 'selectedAttributes', 'selectedVariants')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'discount_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'nullable|integer|min:0',
            'min_order_qty' => 'nullable|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*.sort_order' => 'nullable|integer|min:0',
            'main_image_id' => 'nullable|integer',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:product_labels,id',
            'attributes' => 'nullable|array',
            'type' => 'required|in:simple,configurable',
            'variants' => 'nullable|array',
            'variants.*.sku' => [
                'nullable',
                'string',
                'max:255',
                'distinct',
                Rule::unique('product_variants', 'sku')
                    ->where(fn ($query) => $query->where('product_id', '!=', $product->id)),
            ],
            'variants.*.price' => 'nullable|numeric',
            'variants.*.sale_price' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.attributes' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {

            $imagePaths = $this->storeImages($request);
            $imagePath = $imagePaths[0] ?? $this->storeImage($request);

            if ($imagePath && ! $imagePaths && $product->image) {
                File::delete(public_path($product->image));
            }

            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $this->uniqueSlug($request->name, $product->id),
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'sale_price' => $request->sale_price,
                'sale_start' => $request->sale_start,
                'sale_end' => $request->sale_end,
                'stock' => $request->stock ?? 0,
                'min_order_qty' => $request->min_order_qty ?? 1,
                'manage_stock' => $request->has('manage_stock'),
                'in_stock' => $request->has('in_stock'),
                'image' => $imagePath ?: $product->image,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'type' => $request->type ?? 'simple',
            ]);

            if ($imagePaths) {
                $sortOrder = (int) $product->images()->max('sort_order') + 1;
                $product->images()->update(['is_main' => false]);

                foreach ($imagePaths as $index => $path) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'is_main' => $index === 0,
                        'sort_order' => $sortOrder + $index,
                    ]);
                }
            } elseif ($imagePath) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                    'is_main' => ! $product->images()->exists(),
                    'sort_order' => (int) $product->images()->max('sort_order') + 1,
                ]);
            }

            $this->syncProductImages($product, $request);

            $product->labels()->sync($request->input('labels', []));
            $this->syncAttributes($product, $request->input('attributes', []));
            if ($product->type === 'configurable') {
                $this->syncVariants($product, $request->input('variants', []));
            } else {
                $product->variants()->delete();
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MOVE PRODUCT TO TRASH
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        try {
            $product->delete();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Product moved to trash successfully');
    }

    public function categoryAttributes(Category $category)
    {
        return response()->json($this->formatAttributes($category->id));
    }

    private function attributesByCategory()
    {
        return Category::query()
            ->get()
            ->mapWithKeys(fn ($category) => [$category->id => $this->formatAttributes($category->id)]);
    }

    private function categoryOptions()
    {
        return Category::with('parent')
            ->orderByRaw('parent_id is not null')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function formatAttributes($categoryId)
    {
        return Attribute::active()
            ->with(['values' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('value')])
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->get()
            ->map(fn ($attribute) => [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'type' => $attribute->type,
                'is_required' => (bool) $attribute->is_required,
                'values' => $attribute->values->map(fn ($value) => [
                    'id' => $value->id,
                    'value' => $value->value,
                ])->values(),
            ])
            ->values();
    }

    private function syncAttributes(Product $product, array $attributes)
    {
        $product->attributeValues()->delete();

        foreach ($attributes as $attributeId => $data) {
            $attribute = Attribute::find($attributeId);

            if (! $attribute) {
                continue;
            }

            $attributeValueIds = $data['attribute_value_ids'] ?? [];
            $value = $data['value'] ?? null;

            if (! is_array($attributeValueIds)) {
                $attributeValueIds = array_filter([$attributeValueIds]);
            }

            foreach (array_filter($attributeValueIds) as $attributeValueId) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attributeId,
                    'attribute_value_id' => $attributeValueId,
                    'value' => null,
                ]);
            }

            if (! $attributeValueIds && filled($value)) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attributeId,
                    'attribute_value_id' => null,
                    'value' => $value,
                ]);
            }
        }
    }

    private function syncVariants(Product $product, array $variants)
    {
        $product->variants()->delete();

        foreach ($variants as $variant) {
            $sku = $variant['sku'] ?? null;
            $price = $variant['price'] ?? null;
            $attributes = array_filter($variant['attributes'] ?? []);

            if (! $sku || ! $price || empty($attributes)) {
                continue;
            }

            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $sku,
                'price' => $price,
                'sale_price' => $variant['sale_price'] ?? null,
                'stock' => $variant['stock'] ?? 0,
                'in_stock' => ! empty($variant['in_stock']),
                'attributes' => $attributes,
                'image' => $variant['image'] ?? null,
                'is_active' => ! empty($variant['is_active']),
            ]);
        }
    }

    private function syncProductImages(Product $product, Request $request): void
    {
        $removeIds = collect($request->input('remove_image_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($removeIds->isNotEmpty()) {
            $imagesToRemove = $product->images()
                ->whereIn('id', $removeIds)
                ->get();

            foreach ($imagesToRemove as $image) {
                File::delete($this->productImageStoragePath($image->image));
                $image->delete();
            }
        }

        foreach ($request->input('existing_images', []) as $imageId => $data) {
            $product->images()
                ->whereKey((int) $imageId)
                ->update([
                    'sort_order' => max(0, (int) ($data['sort_order'] ?? 0)),
                    'is_main' => false,
                ]);
        }

        $mainImageId = (int) $request->input('main_image_id');
        $mainImage = null;

        if ($mainImageId && ! $removeIds->contains($mainImageId)) {
            $mainImage = $product->images()->whereKey($mainImageId)->first();
        }

        if (! $mainImage) {
            $mainImage = $product->images()
                ->orderByDesc('is_main')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
        }

        if ($mainImage) {
            $product->images()->update(['is_main' => false]);
            $mainImage->update(['is_main' => true]);
            $product->forceFill(['image' => $mainImage->image])->save();
            return;
        }

        $product->forceFill(['image' => null])->save();
    }

    private function ensureDefaultProductLabels(): void
    {
        collect([
            ['name' => 'New Arrivals', 'slug' => 'new-arrived', 'color' => '#315411'],
            ['name' => 'Bestsellers', 'slug' => 'best-product', 'color' => '#315411'],
        ])->each(function (array $label) {
            ProductLabel::firstOrCreate(
                ['slug' => $label['slug']],
                [
                    'name' => $label['name'],
                    'color' => $label['color'],
                    'is_active' => true,
                ]
            );
        });
    }

    private function storeImage(Request $request)
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('product-images');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
        $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $fileName);

        return 'products/images/' . $fileName;
    }

    private function storeImages(Request $request)
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        $directory = public_path('product-images');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $paths = [];

        foreach ($request->file('images', []) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $fileName);
            $paths[] = 'products/images/' . $fileName;
        }

        return $paths;
    }

    private function productImageStoragePath(?string $path): string
    {
        if ($path && str_starts_with($path, 'products/images/')) {
            return public_path('product-images/'.Str::after($path, 'products/images/'));
        }

        return public_path((string) $path);
    }

    private function uniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }
}
