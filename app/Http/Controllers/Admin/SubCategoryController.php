<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Str;

class SubCategoryController 
{
    // 📋 LIST
    public function index(Request $request)
    {
        $query = SubCategory::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
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

        $subcategories = $query->get();

        return view('admin.pages.subcategory.subcategory', compact('subcategories'));
    }

    // ➕ CREATE
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.pages.subcategory.create', compact('categories'));
    }

        // 💾 STORE
        public function store(Request $request)
        {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'sort_order' => 'nullable|integer',
                    'is_active'=> 'nullable|boolean',
                    'description'=> 'nullable|string',
                        'show_on_home' => 'nullable|boolean',
                        'meta_title' => 'nullable|string',
                        'meta_description' => 'nullable|string',
            ]);
                
    
            $data = $request->only([
                'category_id',
                'name',
                'description',
                'is_active',
                'show_on_home',
                'sort_order',
                'meta_title',
                'meta_description'
            ]);
    
            $data['slug'] = $request->slug ?? Str::slug($request->name);
    
            /* ================= IMAGE ================= */
            if ($request->hasFile('image')) {
    
                $image = $request->file('image');
                $imageName = time() . '_img_' . $image->getClientOriginalName();
    
                $image->move(public_path('subcategories/images'), $imageName);
    
                $data['image'] = 'subcategories/images/' . $imageName;
            }
    
            /* ================= BANNER ================= */
            if ($request->hasFile('banner')) {
    
                $banner = $request->file('banner');
                $bannerName = time() . '_banner_' . $banner->getClientOriginalName();
    
                $banner->move(public_path('subcategories/banners'), $bannerName);
    
                $data['banner'] = 'subcategories/banners/' . $bannerName;
            }
    
            SubCategory::create($data);
    
            return redirect()->route('subcategories.index')
                ->with('success', 'SubCategory Created Successfully');
        }   


   
    // ✏️ EDIT
    public function edit($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $categories = Category::whereNull('parent_id')->get();

        return view('admin.pages.subcategory.edit', compact('subcategory', 'categories'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $subcategory = SubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'slug' => "nullable|unique:sub_categories,slug," . $subcategory->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $data = $request->only([
            'category_id',
            'name',
            'description',
            'is_active',
            'show_on_home',
            'sort_order',
            'meta_title',
            'meta_description'
        ]);

        $data['slug'] = $request->slug ?? Str::slug($request->name);

        /* ================= IMAGE ================= */
        if ($request->hasFile('image')) {

            if ($subcategory->image && file_exists(public_path($subcategory->image))) {
                unlink(public_path($subcategory->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_img_' . $image->getClientOriginalName();

            $image->move(public_path('subcategories/images'), $imageName);

            $data['image'] = 'subcategories/images/' . $imageName;
        } else {
            $data['image'] = $subcategory->image;
        }

        /* ================= BANNER ================= */
        if ($request->hasFile('banner')) {

            if ($subcategory->banner && file_exists(public_path($subcategory->banner))) {
                unlink(public_path($subcategory->banner));
            }

            $banner = $request->file('banner');
            $bannerName = time() . '_banner_' . $banner->getClientOriginalName();

            $banner->move(public_path('subcategories/banners'), $bannerName);

            $data['banner'] = 'subcategories/banners/' . $bannerName;
        } else {
            $data['banner'] = $subcategory->banner;
        }

        $subcategory->update($data);

        return redirect()->route('subcategories.index')
            ->with('success', 'SubCategory Updated Successfully');
    }

    // ❌ DELETE
    public function destroy($id)
    {
        try {

            $subcategory = SubCategory::findOrFail($id);

            if ($subcategory->image && file_exists(public_path($subcategory->image))) {
                unlink(public_path($subcategory->image));
            }

            if ($subcategory->banner && file_exists(public_path($subcategory->banner))) {
                unlink(public_path($subcategory->banner));
            }

            $subcategory->delete();

            return back()->with('success', 'SubCategory Deleted Successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}