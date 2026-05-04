<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SocialMediaLinkController
{
    public function index()
    {
        $links = SocialMediaLink::orderBy('priority')->get();
        return view('admin.pages.socialLinks.social-links', compact('links'));
    }

    public function create()
    {
        return view('admin.pages.socialLinks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required|url',
            'icon' => 'nullable|string',
        ]);

        SocialMediaLink::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'url' => $request->url,
            'icon' => $request->icon,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('social-links.index')->with('success', 'Link created');
    }

    public function edit(SocialMediaLink $social_link)
    {
        return view('admin.pages.socialLinks.edit', compact('social_link'));
    }

    public function update(Request $request, SocialMediaLink $social_link)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required|url',
            'icon' => 'nullable|string',
        ]);

        $social_link->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'url' => $request->url,
            'icon' => $request->icon,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('social-links.index')->with('success', 'Link updated');
    }

    public function destroy(SocialMediaLink $social_link)
    {
        $social_link->delete();

        return back()->with('success', 'Link deleted');
    }
}

