<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;



class ProductController 
{
    /*
    |--------------------------------------------------------------------------
    | LIST PRODUCTS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'images', 'variants', 'labels'])
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
    $categories = Category::active()
        ->with('children')
        ->parent()
        ->sorted()
        ->get();

    return view(
        'admin.pages.products.create',
        compact('categories')
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
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
        ]);

        DB::beginTransaction();

        try {

            // 1. CREATE PRODUCT
            $product = Product::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . rand(1000, 9999),
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'sale_price' => $request->sale_price,
                'sale_start' => $request->sale_start,
                'sale_end' => $request->sale_end,
                'stock' => $request->stock ?? 0,
                'manage_stock' => $request->manage_stock ?? true,
                'in_stock' => $request->in_stock ?? true,
                'is_featured' => $request->is_featured ?? false,
                'is_active' => $request->is_active ?? true,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'type' => $request->type ?? 'simple',
            ]);

            // 2. PRODUCT IMAGES
            if ($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $image,
                        'is_main' => $key === 0,
                        'sort_order' => $key,
                    ]);
                }
            }

            // 3. ATTRIBUTES
            if ($request->has('attributes')) {
                foreach ($request->attributes as $attribute_id => $data) {
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute_id,
                        'attribute_value_id' => $data['value_id'] ?? null,
                        'value' => $data['value'] ?? null,
                    ]);
                }
            }

            // 4. VARIANTS (CONFIGURABLE PRODUCTS)
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variant['sku'],
                        'price' => $variant['price'],
                        'sale_price' => $variant['sale_price'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                        'in_stock' => $variant['in_stock'] ?? true,
                        'attributes' => json_encode($variant['attributes']),
                        'image' => $variant['image'] ?? null,
                    ]);
                }
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
        $product = Product::with(['images', 'attributes', 'variants', 'labels'])
            ->findOrFail($id);

        return view('admin.pages.products.edit', compact('product'));
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
        ]);

        DB::beginTransaction();

        try {

            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'sku' => $request->sku,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'stock' => $request->stock,
                'is_active' => $request->is_active,
            ]);

            // update logic can be extended (images/variants/attributes)

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
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Product deleted successfully');
    }
}