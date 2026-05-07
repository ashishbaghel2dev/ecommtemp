<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductLabelController 
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labels = ProductLabel::latest()->paginate(10);

        return view('admin.pages.product_labels.index', compact('labels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.product_labels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'color'     => 'nullable|string|max:255',
            'is_active' => 'nullable',
        ]);

        ProductLabel::create([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'color'     => $request->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('productlabels.index')
            ->with('success', 'Label created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product_label = ProductLabel::findOrFail($id);

        return view('admin.pages.product_labels.edit', compact('product_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product_label = ProductLabel::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'color'     => 'nullable|string|max:255',
            'is_active' => 'nullable',
        ]);

        $product_label->update([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'color'     => $request->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('productlabels.index')
            ->with('success', 'Label updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product_label = ProductLabel::findOrFail($id);

        $product_label->delete();

        return redirect()
            ->route('productlabels.index')
            ->with('success', 'Label deleted successfully.');
    }
}