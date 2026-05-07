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
            'image' => 'nullable|image|max:2048',
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
            $imagePath = $this->storeImage($request);

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
                'manage_stock' => $request->has('manage_stock'),
                'in_stock' => $request->has('in_stock'),
                'image' => $imagePath,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'type' => $request->type ?? 'simple',
            ]);

            if ($imagePath) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                    'is_main' => true,
                    'sort_order' => 0,
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
        $product = Product::with(['images', 'attributeValues.attributeValue', 'variants', 'labels'])
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
            'image' => 'nullable|image|max:2048',
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

            $imagePath = $this->storeImage($request);

            if ($imagePath && $product->image) {
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
                'manage_stock' => $request->has('manage_stock'),
                'in_stock' => $request->has('in_stock'),
                'image' => $imagePath ?: $product->image,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'type' => $request->type ?? 'simple',
            ]);

            if ($imagePath) {
                $product->images()->delete();
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                    'is_main' => true,
                    'sort_order' => 0,
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
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $product = Product::with(['images', 'variants', 'attributeValues', 'labels'])->findOrFail($id);

        DB::beginTransaction();

        try {
            if ($product->image) {
                File::delete(public_path($product->image));
            }

            foreach ($product->images as $image) {
                if ($image->image) {
                    File::delete(public_path($image->image));
                }
            }

            $product->labels()->detach();
            $product->attributeValues()->delete();
            $product->variants()->delete();
            $product->images()->delete();
            $product->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Product deleted successfully');
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

    private function storeImage(Request $request)
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('products/images');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
        $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $fileName);

        return 'products/images/' . $fileName;
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
