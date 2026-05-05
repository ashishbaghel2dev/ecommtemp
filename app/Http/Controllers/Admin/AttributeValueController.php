<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;

class AttributeValueController 
{
    // 📋 LIST
    public function index(Request $request)
    {
        $query = AttributeValue::with('attribute');

        if ($request->filled('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('attribute_id')) {
            $query->where('attribute_id', $request->attribute_id);
        }

        $values = $query->latest()->get();

        return view('admin.pages.attribute_values.index', compact('values'));
    }

    // ➕ CREATE
    public function create()
    {
        $attributes = Attribute::where('is_active', 1)->get();
        return view('admin.pages.attribute_values.create', compact('attributes'));
    }

    // 💾 STORE
    public function store(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required',
        ]);

        AttributeValue::create([
            'attribute_id' => $request->attribute_id,
            'value' => $request->value,
            'slug' => Str::slug($request->value),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? 1,
            'color_code' => $request->color_code,
        ]);

        return redirect()->route('attribute-values.index')
            ->with('success', 'Attribute Value Created Successfully');
    }

    // ✏️ EDIT
    public function edit($id)
    {
        $value = AttributeValue::findOrFail($id);
        $attributes = Attribute::all();

        return view('admin.pages.attribute_values.edit', compact('value', 'attributes'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $value = AttributeValue::findOrFail($id);

        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required',
        ]);

        $value->update([
            'attribute_id' => $request->attribute_id,
            'value' => $request->value,
            'slug' => Str::slug($request->value),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? 1,
            'color_code' => $request->color_code,
        ]);

        return redirect()->route('attribute-values.index')
            ->with('success', 'Attribute Value Updated Successfully');
    }

    // ❌ DELETE
    public function destroy($id)
    {
        $value = AttributeValue::findOrFail($id);
        $value->delete();

        return back()->with('success', 'Attribute Value Deleted Successfully');
    }
}