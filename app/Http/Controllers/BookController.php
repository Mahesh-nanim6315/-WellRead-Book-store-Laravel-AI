<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Review;


class BookController extends Controller
{

    public function home()
    {
        
        $animeCategory   = Category::where('slug', 'anime-manga')->first();
        $fantasyCategory = Category::where('slug', 'Fantasy')->first();
        $comicCategory   = Category::where('slug', 'Comic')->first();
        $novelCategory   = Category::where('slug', 'Novel')->first();
        $scifiCategory   = Category::where('slug', 'Sci-Fi')->first();

        
        $anime   = $animeCategory?->books()->latest()->take(15)->get() ?? collect();
        $fantasy = $fantasyCategory?->books()->latest()->take(15)->get() ?? collect();
        $comic   = $comicCategory?->books()->latest()->take(15)->get() ?? collect();
        $novel   = $novelCategory?->books()->latest()->take(15)->get() ?? collect();
        $scifi   = $scifiCategory?->books()->latest()->take(15)->get() ?? collect();

        return view('welcome', compact(
            'anime',
            'fantasy',
            'comic',
            'novel',
            'scifi',
            'animeCategory',
            'fantasyCategory',
            'comicCategory',
            'novelCategory',
            'scifiCategory'
        ));
    }

public function show($id)
{
    $book = Book::with('reviews.user')->findOrFail($id);

    if ($book->is_premium) {
    if (!Auth::check() || !Auth::user()->hasActiveSubscription()) {
        return redirect()->route('plans.index')
            ->with('error', 'This book requires a premium subscription.');
    }
}
    $reviews = Review::where('book_id', $book->id)
        ->when(Auth::check() && Auth::user()->role !== 'admin', function ($query) {
            $query->where(function ($q) {
                $q->where('is_approved', true)
                  ->orWhere('user_id', Auth::id());
            });
        })
        ->when(!Auth::check(), function ($query) {
            $query->where('is_approved', true);
        })
        ->latest()
        ->get();

    return view('product-details', compact('book', 'reviews'));
}

 
public function categoryBooks(Category $category)
{
    $books = Book::where('category_id', $category->id)
                 ->paginate(12);

   return view('category-books', [
    'category' => $category->name,
    'books' => $books
]);

}


}

