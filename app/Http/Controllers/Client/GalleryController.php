<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Contracts\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('client.pages.gallery.index', compact('galleries'));
    }
}
