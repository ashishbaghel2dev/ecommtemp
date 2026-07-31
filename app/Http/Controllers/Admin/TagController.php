<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.tags.index', compact('tags'));
    }

    public function edit(Tag $tag): View
    {
        return view('admin.pages.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate($this->rules($tag));
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title']);

        if (Tag::query()->where('slug', $validated['slug'])->whereKeyNot($tag->id)->exists()) {
            return back()
                ->withErrors(['slug' => 'This slug is already in use.'])
                ->withInput();
        }

        $validated['status'] = $request->boolean('status');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $tag->update($validated);

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag moved to bin successfully.');
    }

    private function rules(?Tag $tag = null): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                Rule::unique('tags', 'slug')->ignore($tag),
            ],
            'description' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function makeSlug(string $value): string
    {
        return Str::slug($value);
    }
}
