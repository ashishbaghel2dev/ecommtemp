<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController 
{
    // 📋 LIST
    public function index(Request $request)
    {
        $query = Category::with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('sort_by')) {

            if ($request->sort_by == 'name_asc') {
                $query->orderBy('name', 'asc');
            }

            if ($request->sort_by == 'name_desc') {
                $query->orderBy('name', 'desc');
            }

            if ($request->sort_by == 'oldest') {
                $query->orderBy('id', 'asc');
            }

            if ($request->sort_by == 'latest') {
                $query->orderBy('id', 'desc');
            }

            if ($request->sort_by == 'order') {
                $query->orderBy('sort_order', 'asc');
            }

        } else {
            $query->orderBy('sort_order', 'asc');
        }

        $categories = $query->get();

        return view('admin.pages.category.category', compact('categories'));
    }

    // ➕ CREATE FORM
    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.pages.category.create', compact('parents'));
    }

    // 💾 STORE (PUBLIC FOLDER IMAGE)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'nullable|unique:categories',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $data = $request->only([
            'name',
            'parent_id',
            'description',
            'show_on_home',
            'is_active',
            'sort_order',
            'meta_title',
            'meta_description'
        ]);

        $data['slug'] = $request->slug ?? Str::slug($request->name);

        // 🖼️ IMAGE UPLOAD (PUBLIC)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_img_' . $image->getClientOriginalName();

            $image->move(public_path('categories/images'), $imageName);

            $data['image'] = 'categories/images/' . $imageName;
        }

        // 🖼️ BANNER UPLOAD (PUBLIC)
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time() . '_banner_' . $banner->getClientOriginalName();

            $banner->move(public_path('categories/banners'), $bannerName);

            $data['banner'] = 'categories/banners/' . $bannerName;
        }

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Category Created Successfully');
    }

public function edit($id)
{
    $category = Category::findOrFail($id);
    $parents = Category::whereNull('parent_id')->get();

    return view('admin.pages.category.edit', compact('category', 'parents'));
}


  public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'slug' => "nullable|unique:categories,slug," . $category->id,
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
    ]);

    $data = $request->only([
        'name',
        'parent_id',
        'description',
        'show_on_home',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description'
    ]);

    $data['slug'] = $request->slug ?? Str::slug($request->name);

    /* ================= IMAGE ================= */
    if ($request->hasFile('image')) {

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $image = $request->file('image');
        $imageName = time() . '_img_' . $image->getClientOriginalName();

        $image->move(public_path('categories/images'), $imageName);

        $data['image'] = 'categories/images/' . $imageName;
    } else {
        $data['image'] = $category->image;
    }

    /* ================= BANNER ================= */
    if ($request->hasFile('banner')) {

        if ($category->banner && file_exists(public_path($category->banner))) {
            unlink(public_path($category->banner));
        }

        $banner = $request->file('banner');
        $bannerName = time() . '_banner_' . $banner->getClientOriginalName();

        $banner->move(public_path('categories/banners'), $bannerName);

        $data['banner'] = 'categories/banners/' . $bannerName;
    } else {
        $data['banner'] = $category->banner;
    }

    $category->update($data);

    return redirect()->route('categories.index')
        ->with('success', 'Category Updated Successfully');
}

    // ❌ DELETE (CATEGORY + IMAGE DELETE)
    public function destroy($id)
    {
        try {

            $category = Category::findOrFail($id);

            // prevent delete if children exist
            if ($category->hasChildren()) {
                return back()->with('error', 'Cannot delete category with children');
            }

            // delete image
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            // delete banner
            if ($category->banner && file_exists(public_path($category->banner))) {
                unlink(public_path($category->banner));
            }

            $category->delete();

            return back()->with('success', 'Category Deleted Successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}