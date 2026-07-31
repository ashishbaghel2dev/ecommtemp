<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Inquiry;
use App\Services\StoreMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function create(): View
    {
        $faqs = Faq::query()
            ->where(function ($query) {
                foreach (['Divyakriti', 'Hospital', 'Clinic', 'Orthopedic', 'Orthopedics'] as $term) {
                    $query->where('question', 'not like', "%{$term}%")
                        ->where('answer', 'not like', "%{$term}%");
                }
            })
            ->orderByDesc('id')
            ->take(4)
            ->get();

        return view('client.pages.contact', compact('faqs'));
    }

    public function store(Request $request, StoreMailService $storeMailService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $inquiry = Inquiry::query()->create($validated);
        $storeMailService->inquiryCreated($inquiry);

        return back()->with('success', 'Thank you. Your inquiry has been submitted successfully.');
    }
}
