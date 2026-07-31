<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.pages.coupons.index', [
            'coupons' => Coupon::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.pages.coupons.form', $this->formData(new Coupon()));
    }

    public function store(Request $request)
    {
        Coupon::create($this->validated($request));

        return redirect()->route('coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.pages.coupons.form', $this->formData($coupon));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'Coupon deleted.');
    }

    private function formData(Coupon $coupon): array
    {
        return [
            'coupon' => $coupon,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
        ];
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:80', Rule::unique('coupons')->ignore($coupon)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:flat,percentage,free_shipping'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'applicable_category_ids' => ['nullable', 'array'],
            'applicable_category_ids.*' => ['integer', 'exists:categories,id'],
            'applicable_product_ids' => ['nullable', 'array'],
            'applicable_product_ids.*' => ['integer', 'exists:products,id'],
            'excluded_product_ids' => ['nullable', 'array'],
            'excluded_product_ids.*' => ['integer', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['minimum_order_amount'] = $data['minimum_order_amount'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['applicable_category_ids'] = array_values($data['applicable_category_ids'] ?? []);
        $data['applicable_product_ids'] = array_values($data['applicable_product_ids'] ?? []);
        $data['excluded_product_ids'] = array_values($data['excluded_product_ids'] ?? []);

        return $data;
    }
}
