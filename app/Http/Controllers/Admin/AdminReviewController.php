<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController 
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
    // public function show(Review $review)
    // {
    //     $review->load([
    //         'user',
    //         'product',
    //         'images',
    //         'reports',
    //         'votes'
    //     ]);

    //     return view('admin.reviews.show', compact('review'));
    // }

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