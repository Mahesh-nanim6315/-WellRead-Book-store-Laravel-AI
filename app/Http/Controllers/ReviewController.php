<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;


class ReviewController extends Controller
{

public function store(Request $request, Book $book)
{
    $userId = Auth::id();

    $request->validate([
        'rating'  => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:1000',
    ]);

    $alreadyReviewed = Review::where('book_id', $book->id)
        ->where('user_id', $userId)
        ->exists();

    if ($alreadyReviewed) {
        return back()->with('error', 'You have already reviewed this book.');
    }

    Review::create([
        'book_id' => $book->id,
        'user_id' => Auth::id(),
        'rating'  => $request->rating,
        'comment' => $request->comment,
        'is_approved'=> false,
    ]);

    return back()->with('success', 'Review added successfully!');
}



public function edit(Review $review)
{
    // Security check
    if ($review->user_id !== Auth::id()) {
        abort(403);
    }

    return view('reviews.edit', compact('review'));
}

public function update(Request $request, Review $review)
{
    if ($review->user_id !== Auth::id()) {
        abort(403);
    }

    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string'
    ]);

    $review->update([
        'rating' => $request->rating,
        'comment' => $request->comment
    ]);

    return redirect()
        ->route('books.show', $review->book_id)
        ->with('success', 'Review updated successfully');
}

public function destroy(Review $review)
{
    if ($review->user_id !== Auth::id()) {
        abort(403);
    }

    $review->delete();

    return back()->with('success', 'Review deleted');
}




}
