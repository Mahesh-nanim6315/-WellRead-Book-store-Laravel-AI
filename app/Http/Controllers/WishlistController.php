<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with('book')
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    
    public function store(Book $book)
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'book_id' => $book->id
        ]);

        return back()->with('success', 'Added to wishlist');
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('success', 'Removed from wishlist');
    }

    public function toggle(Request $request)
{
    $request->validate([
        'book_id' => 'required|exists:books,id'
    ]);

    $wishlist = Wishlist::where('user_id', Auth::id())
        ->where('book_id', $request->book_id)
        ->first();

    if ($wishlist) {
        $wishlist->delete();
        return response()->json(['status' => 'removed']);
    }

    Wishlist::create([
        'user_id' => Auth::id(),
        'book_id' => $request->book_id
    ]);

    return response()->json(['status' => 'added']);
}

}
