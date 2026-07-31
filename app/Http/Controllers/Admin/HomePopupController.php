<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomePopupController extends Controller
{
    public function edit()
    {
        return view('admin.pages.home-popup.edit', [
            'settings' => AdminSetting::dashboardConfig(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'home_popup_enabled' => ['nullable', 'boolean'],
            'home_popup_show_once' => ['nullable', 'boolean'],
            'home_popup_eyebrow' => ['nullable', 'string', 'max:80'],
            'home_popup_title' => ['required', 'string', 'max:120'],
            'home_popup_body' => ['required', 'string', 'max:500'],
            'home_popup_button_text' => ['nullable', 'string', 'max:60'],
            'home_popup_button_url' => ['nullable', 'string', 'max:255'],
            'home_popup_delay_seconds' => ['required', 'integer', 'min:1', 'max:120'],
            'home_popup_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['home_popup_enabled'] = $request->boolean('home_popup_enabled') ? '1' : '0';
        $validated['home_popup_show_once'] = $request->boolean('home_popup_show_once') ? '1' : '0';
        $validated['home_popup_eyebrow'] = $validated['home_popup_eyebrow'] ?? '';
        $validated['home_popup_button_text'] = $validated['home_popup_button_text'] ?? '';
        $validated['home_popup_button_url'] = $validated['home_popup_button_url'] ?? '';
        unset($validated['home_popup_image']);

        if ($request->hasFile('home_popup_image')) {
            $validated['home_popup_image_path'] = $this->storeImage($request);
        }

        AdminSetting::putMany($validated);

        return redirect()
            ->route('home-popup.edit')
            ->with('success', 'Home popup updated successfully.');
    }

    private function storeImage(Request $request): string
    {
        $file = $request->file('home_popup_image');
        $directory = public_path('uploads/home-popup');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $name = time().'_home_popup.'.$file->getClientOriginalExtension();
        $file->move($directory, $name);

        return 'uploads/home-popup/'.$name;
    }
}
