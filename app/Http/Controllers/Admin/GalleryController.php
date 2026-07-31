<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class GalleryController extends Controller
{
    private const UPLOAD_DIR = 'uploads/gallery';

    public function index(): View
    {
        $galleries = Gallery::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.gallery.index', compact('galleries'));
    }

    public function create(): View
    {
        return view('admin.pages.gallery.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['image'] = $this->storeImage($request);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['status'] = $request->boolean('status');

        Gallery::query()->create($validated);

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery image created successfully.');
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.pages.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($gallery->image);
            $validated['image'] = $this->storeImage($request);
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['status'] = $request->boolean('status');

        $gallery->update($validated);

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery image updated successfully.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery image moved to bin successfully.');
    }

    private function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $fileName = time() . '-' . uniqid('gallery-', true) . '.' . $file->getClientOriginalExtension();
        $destination = public_path(self::UPLOAD_DIR);

        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $file->move($destination, $fileName);

        return self::UPLOAD_DIR . '/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $absolutePath = public_path($path);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
