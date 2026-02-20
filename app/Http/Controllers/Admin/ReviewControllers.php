<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewControllers extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user','book']);

        // Filter by approval
        if ($request->filled('status')) {
            $query->where('is_approved', $request->status);
        }

        // Search by book or user
        if ($request->filled('search')) {
            $query->whereHas('book', function($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%');
            })->orWhereHas('user', function($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%');
            });
        }

        $reviews = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved
        ]);

        return back()->with('success','Review status updated');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success','Review deleted');
    }
}

