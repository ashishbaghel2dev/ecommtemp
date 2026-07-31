<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()
            ->latest()
            ->get();

        return view('admin.pages.faq.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.pages.faq.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'written_by' => ['nullable', 'string', 'max:120'],
        ]);

        Faq::query()->create($validated);

        return redirect()
            ->route('faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.pages.faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'written_by' => ['nullable', 'string', 'max:120'],
        ]);

        $faq->update($validated);

        return redirect()
            ->route('faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('faqs.index')
            ->with('success', 'FAQ moved to bin successfully.');
    }
}
