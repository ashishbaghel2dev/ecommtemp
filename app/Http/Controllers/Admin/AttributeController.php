<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\SubCategory;
use Illuminate\Support\Str;

class AttributeController 
{
    // 📋 LIST
    public function index(Request $request)
    {
        $query = Attribute::with('subcategory');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        $attributes = $query->latest()->get();

        return view('admin.pages.attributes.index', compact('attributes'));
    }

    // ➕ CREATE
    public function create()
    {
        $subcategories = SubCategory::all();
        return view('admin.pages.attributes.create', compact('subcategories'));
    }

    // 💾 STORE
    public function store(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required',
            'code' => 'required|unique:attributes',
            'type' => 'required|in:text,select,number,boolean',
        ]);

        Attribute::create([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'code' => Str::slug($request->code),
            'type' => $request->type,
            'is_required' => $request->is_required ?? 0,
            'is_filterable' => $request->is_filterable ?? 0,
            // 'is_active' => $request->is_active ?? 1,
        ]);

        return redirect()->route('attributes.index')
            ->with('success', 'Attribute Created Successfully');
    }

    // ✏️ EDIT
    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        $subcategories = SubCategory::all();

        return view('admin.pages.attributes.edit', compact('attribute', 'subcategories'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $request->validate([
            'subcategory_id' => 'required|exists:sub_categories,id',
            'name' => 'required',
            'code' => 'required|unique:attributes,code,' . $attribute->id,
            'type' => 'required|in:text,select,number,boolean',
        ]);

        $attribute->update([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'code' => Str::slug($request->code),
            'type' => $request->type,
            'is_required' => $request->is_required ?? 0,
            'is_filterable' => $request->is_filterable ?? 0,
            'is_active' => $request->is_active ?? 1,
        ]);

        return redirect()->route('attributes.index')
            ->with('success', 'Attribute Updated Successfully');
    }

    // ❌ DELETE
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        return back()->with('success', 'Attribute Deleted Successfully');
    }
}