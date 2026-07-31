<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $statuses = Inquiry::statuses();
        $activeStatus = $request->query('status');
        $search = $request->query('search');

        $inquiries = Inquiry::query()
            ->when($activeStatus && isset($statuses[$activeStatus]), function ($query) use ($activeStatus) {
                $query->where('status', $activeStatus);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.inquiries.index', compact('inquiries', 'statuses', 'activeStatus', 'search'));
    }

    public function updateStatus(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Inquiry::statuses()))],
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry status updated successfully.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()
            ->route('inquiries.index')
            ->with('success', 'Inquiry moved to bin successfully.');
    }
}
