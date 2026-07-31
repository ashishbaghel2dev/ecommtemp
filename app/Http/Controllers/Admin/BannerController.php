<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    // 📋 LIST
    public function index()
    {
        $banners = Banner::orderBy('priority')->latest()->get();
        return view('admin.pages.banners.banners', compact('banners'));
    }

    // ➕ CREATE FORM
    public function create()
    {
        return view('admin.pages.banners.create');
    }

    // 💾 STORE (PUBLIC FOLDER)
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'eyebrow' => 'nullable|string|max:80',
            'title' => 'nullable|string|max:140',
            'subtitle' => 'nullable|string|max:500',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:60',
        ]);

        $data = $request->only([
            'eyebrow',
            'title',
            'subtitle',
            'link',
            'button_text',
            'priority',
            'position'
        ]);

        $data['is_active'] = $request->has('is_active');

        // 🖼️ IMAGE UPLOAD (PUBLIC)
        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $imageName = time() . '_banner_' . $image->getClientOriginalName();

            $image->move(public_path('banners'), $imageName);

            $data['image'] = 'banners/' . $imageName;
        }

        Banner::create($data);

        return redirect()->route('banners.index')
            ->with('success', 'Banner Created Successfully');
    }

    // ✏️ EDIT
    public function edit(Banner $banner)
    {
        return view('admin.pages.banners.edit', compact('banner'));
    }

    // 🔄 UPDATE (DELETE OLD + UPLOAD NEW)
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'eyebrow' => 'nullable|string|max:80',
            'title' => 'nullable|string|max:140',
            'subtitle' => 'nullable|string|max:500',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:60',
        ]);

        $data = $request->only([
            'eyebrow',
            'title',
            'subtitle',
            'link',
            'button_text',
            'priority',
            'position'
        ]);

        $data['is_active'] = $request->has('is_active');

        // 🖼️ IMAGE UPDATE
        if ($request->hasFile('image')) {

            // delete old image
            if ($banner->image && file_exists(public_path($banner->image))) {
                unlink(public_path($banner->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_banner_' . $image->getClientOriginalName();

            $image->move(public_path('banners'), $imageName);

            $data['image'] = 'banners/' . $imageName;
        }

        $banner->update($data);

        return redirect()->route('banners.index')
            ->with('success', 'Banner Updated Successfully');
    }

    // Move to trash. Files are removed only on permanent delete.
    public function destroy(Banner $banner)
    {
        try {
            $banner->delete();

            return back()->with('success', 'Banner moved to trash successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
