<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $blogs = Blog::query()
            ->where('status', true)
            ->where('publish_status', 'posted')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10);

        $categories = Blog::query()
            ->where('status', true)
            ->where('publish_status', 'posted')
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        return view('client.pages.blogs.index', compact('blogs', 'categories'));
    }

    public function show(Blog $blog): View
    {
        abort_unless($blog->status && $blog->publish_status === 'posted', 404);

        $relatedBlogs = Blog::query()
            ->where('status', true)
            ->where('publish_status', 'posted')
            ->whereKeyNot($blog->id)
            ->when($blog->category, fn ($query) => $query->where('category', $blog->category))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        if ($relatedBlogs->isEmpty()) {
            $relatedBlogs = Blog::query()
                ->where('status', true)
                ->where('publish_status', 'posted')
                ->whereKeyNot($blog->id)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->take(5)
                ->get();
        }

        $tags = Tag::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(16)
            ->get();

        return view('client.pages.blogs.show', compact('blog', 'relatedBlogs', 'tags'));
    }
}
