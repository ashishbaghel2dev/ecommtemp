<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminReviewController extends Controller
{
    /**
     * Review Listing Page
     */
    public function index(Request $request)
    {
        $reviews = Review::query()
            ->with([
                'user:id,name',
                'product:id,name,slug',
                'images'
            ])

            // Filter by status
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })

            // Filter by rating
            ->when($request->rating, function ($query) use ($request) {
                $query->where('rating', $request->rating);
            })

            // Search
            ->when($request->search, function ($query) use ($request) {

                $query->where(function ($q) use ($request) {

                    $q->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('comment', 'like', '%' . $request->search . '%');
                });
            })

            ->latest()
            ->paginate(20);

        return view('admin.pages.reviews.index', compact('reviews'));
    }

    /**
     * Show Single Review
     */
    public function show(Review $review)
    {
        $review->load([
            'user',
            'product',
            'images',
            'reports',
            'votes.user',
        ]);

        return view('admin.pages.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $review->load([
            'user',
            'product',
            'images',
        ]);

        return view('admin.pages.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'admin_reply' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'is_verified_purchase' => ['nullable', 'boolean'],
        ]);

        $review->update([
            'title' => $validated['title'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'admin_reply' => $validated['admin_reply'] ?? null,
            'status' => $validated['status'],
            'is_verified_purchase' => (bool) ($validated['is_verified_purchase'] ?? false),
        ]);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review request updated successfully.');
    }

    /**
     * Approve Review
     */
    public function approve(Review $review)
    {
        if ($review->status !== 'approved') {

            $review->update([
                'status' => 'approved',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Review approved successfully.');
    }

    /**
     * Reject Review
     */
    public function reject(Review $review)
    {
        if ($review->status !== 'rejected') {

            $review->update([
                'status' => 'rejected',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Review rejected successfully.');
    }

    /**
     * Admin Reply
     */
    public function reply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Reply added successfully.');
    }

    /**
     * Delete Review
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()
            ->back()
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Restore Soft Deleted Review
     */
    public function restore($id)
    {
        $review = Review::withTrashed()->findOrFail($id);

        $review->restore();

        return redirect()
            ->back()
            ->with('success', 'Review restored successfully.');
    }
}
