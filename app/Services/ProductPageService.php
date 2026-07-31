<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductPageService
{
    public function getProductPageData(Product $product): array
    {
        $loadedProduct = Product::query()
            ->active()
            ->with([
                'category.parent',
                'labels',
                'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('sort_order'),
                'variants' => fn ($query) => $query->where('is_active', true),
                'attributeValues.attribute',
                'attributeValues.attributeValue',
                'reviews' => fn ($query) => $query->where('status', 'approved')->latest()->take(6),
                'reviews.user',
                'reviews.images',
            ])
            ->findOrFail($product->id);

        return [
            'product' => $loadedProduct,
            'attributeGroups' => $this->getAttributeGroups($loadedProduct),
            'variantAttributes' => $this->getVariantAttributes($loadedProduct),
            'variantValues' => $this->getVariantValues($loadedProduct),
            'relatedProducts' => $this->getRelatedProducts($loadedProduct),
        ];
    }

    private function getAttributeGroups(Product $product): Collection
    {
        return $product->attributeValues
            ->groupBy('attribute_id')
            ->map(function (Collection $values) {
                $attribute = $values->first()->attribute;

                return [
                    'attribute' => $attribute,
                    'values' => $values->sortBy(function ($productAttributeValue) {
                        return $productAttributeValue->attributeValue->sort_order ?? $productAttributeValue->id;
                    })->values(),
                ];
            })
            ->values();
    }

    private function getVariantAttributes(Product $product): Collection
    {
        $ids = $product->variants
            ->flatMap(fn ($variant) => is_array($variant->attributes) ? array_keys($variant->attributes) : [])
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Attribute::query()
            ->whereIn('id', $ids)
            ->pluck('name', 'id');
    }

    private function getVariantValues(Product $product): Collection
    {
        $ids = $product->variants
            ->flatMap(fn ($variant) => is_array($variant->attributes) ? array_values($variant->attributes) : [])
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return AttributeValue::query()
            ->whereIn('id', $ids)
            ->pluck('value', 'id');
    }

    private function getRelatedProducts(Product $product): Collection
    {
        return Product::query()
            ->active()
            ->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)
            ->whereNotNull('slug')
            ->with([
                'category',
                'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('sort_order'),
            ])
            ->latest()
            ->take(4)
            ->get();
    }
}
