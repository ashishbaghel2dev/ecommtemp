<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('client.pages.tags.index', compact('tags'));
    }

    public function show(Tag $tag): View
    {
        abort_unless($tag->status, 404);

        $blogs = Blog::query()
            ->where('status', true)
            ->where('publish_status', 'posted')
            ->whereNotNull('meta_tags')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (Blog $blog) => $this->blogHasTag($blog, $tag))
            ->values();

        return view('client.pages.tags.show', compact('tag', 'blogs'));
    }

    private function blogHasTag(Blog $blog, Tag $tag): bool
    {
        return collect(preg_split('/[\n,]+/', (string) $blog->meta_tags))
            ->map(fn (string $tagText) => Str::slug(trim($tagText)))
            ->contains($tag->slug);
    }
}
