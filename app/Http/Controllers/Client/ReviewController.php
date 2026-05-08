<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display Product Reviews
     */
    public function index()
    {
        $reviews = Review::with([
                'user',
                'product',
                'images'
            ])
            ->where('status', 'approved')
            ->latest()
            ->paginate(10);

        return view('client.reviews.index', compact('reviews'));
    }

    /**
     * Store Review
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $review = Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => Auth::id(),
            'title' => $validated['title'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload Images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                $path = $image->store('reviews', 'public');

                $review->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Review submitted successfully.');
    }

    /**
     * Show Single Review
     */
    public function show(Review $review)
    {
        $review->load([
            'user',
            'product',
            'images'
        ]);

        return view('client.reviews.show', compact('review'));
    }

    /**
     * Edit Review Page
     */
    public function edit(Review $review)
    {
        // Only Owner
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.reviews.edit', compact('review'));
    }

    /**
     * Update Review
     */
    public function update(Request $request, Review $review)
    {
        // Only Owner
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update([
            'title' => $validated['title'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,

            // Recheck review after update
            'status' => 'pending',
        ]);

        return redirect()
            ->route('reviews.show', $review->id)
            ->with('success', 'Review updated successfully.');
    }

    /**
     * Delete Review
     */
    public function destroy(Review $review)
    {
        // Only Owner
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return redirect()
            ->route('reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}