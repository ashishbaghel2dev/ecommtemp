<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Contracts\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()
            ->where(function ($query) {
                foreach (['Gosowa'] as $term) {
                    $query->where('question', 'not like', "%{$term}%")
                        ->where('answer', 'not like', "%{$term}%");
                }
            })
            ->orderByDesc('id')
            ->get();

        return view('client.pages.faqs.index', compact('faqs'));
    }
}
