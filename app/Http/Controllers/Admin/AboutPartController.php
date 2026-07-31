<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AboutPartController extends Controller
{
    private const UPLOAD_DIR = 'uploads/about-parts';

    public function index(): View
    {
        $aboutParts = AboutPart::query()
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.about-parts.index', compact('aboutParts'));
    }

    public function create(): View
    {
        return view('admin.pages.about-parts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title']);

        if (AboutPart::query()->where('slug', $validated['slug'])->exists()) {
            return back()
                ->withErrors(['slug' => 'This slug is already in use.'])
                ->withInput();
        }

        foreach ($this->imageFields() as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $this->storeImage($request, $field);
            }
        }

        $validated['status'] = $request->boolean('status');

        AboutPart::query()->create($validated);

        return redirect()
            ->route('about-parts.index')
            ->with('success', 'About part created successfully.');
    }

    public function edit(AboutPart $aboutPart): View
    {
        return view('admin.pages.about-parts.edit', compact('aboutPart'));
    }

    public function update(Request $request, AboutPart $aboutPart): RedirectResponse
    {
        $validated = $request->validate($this->rules($aboutPart));
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title']);

        if (AboutPart::query()->where('slug', $validated['slug'])->whereKeyNot($aboutPart->id)->exists()) {
            return back()
                ->withErrors(['slug' => 'This slug is already in use.'])
                ->withInput();
        }

        foreach ($this->imageFields() as $field) {
            if ($request->hasFile($field)) {
                $this->deleteImage($aboutPart->{$field});
                $validated[$field] = $this->storeImage($request, $field);
            }
        }

        $validated['status'] = $request->boolean('status');

        $aboutPart->update($validated);

        return redirect()
            ->route('about-parts.index')
            ->with('success', 'About part updated successfully.');
    }

    public function destroy(AboutPart $aboutPart): RedirectResponse
    {
        $aboutPart->delete();

        return redirect()
            ->route('about-parts.index')
            ->with('success', 'About part moved to bin successfully.');
    }

    private function rules(?AboutPart $aboutPart = null): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                Rule::unique('about_parts', 'slug')->ignore($aboutPart),
            ],
            'short_description' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'image_1' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'image_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'image_3' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    private function imageFields(): array
    {
        return ['image_1', 'image_2', 'image_3'];
    }

    private function makeSlug(string $value): string
    {
        return Str::slug($value);
    }

    private function storeImage(Request $request, string $field): string
    {
        $file = $request->file($field);
        $fileName = time() . '-' . uniqid($field . '-', true) . '.' . $file->getClientOriginalExtension();
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
