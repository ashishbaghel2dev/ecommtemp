<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to submit a review.');
        }

        $validated = $request->validate(
            [
                'product_id' => [
                    'required',
                    'exists:products,id',
                    Rule::unique('reviews', 'product_id')
                        ->where(fn ($query) => $query->where('user_id', Auth::id())),
                ],
                'title' => 'nullable|string|max:255',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string',
                'images.*' => 'nullable|image|max:2048',
            ],
            [
                'product_id.unique' => 'You have already reviewed this product.',
            ]
        );

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
        abort_unless($review->status === 'approved', 404);

        $review->load([
            'user',
            'product',
            'images'
        ]);

        return view('client.reviews.show', compact('review'));
    }

    public function helpful(Review $review)
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to mark reviews as helpful.',
            ], 401);
        }

        DB::transaction(function () use ($review) {
            $vote = ReviewVote::query()
                ->where('review_id', $review->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($vote && $vote->is_helpful) {
                $vote->delete();
                $review->decrement('helpful_votes');

                return;
            }

            if (! $vote) {
                ReviewVote::create([
                    'review_id' => $review->id,
                    'user_id' => Auth::id(),
                    'is_helpful' => true,
                ]);

                $review->increment('helpful_votes');

                return;
            }

            $vote->update(['is_helpful' => true]);
            $review->increment('helpful_votes');

            if ($review->unhelpful_votes > 0) {
                $review->decrement('unhelpful_votes');
            }
        });

        $review->refresh();

        $marked = ReviewVote::query()
            ->where('review_id', $review->id)
            ->where('user_id', Auth::id())
            ->where('is_helpful', true)
            ->exists();

        return response()->json([
            'status' => true,
            'marked' => $marked,
            'helpful_votes' => (int) $review->helpful_votes,
        ]);
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
