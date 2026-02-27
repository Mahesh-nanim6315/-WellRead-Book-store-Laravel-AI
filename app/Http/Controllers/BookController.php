<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Review;
use App\Models\Setting;

class BookController extends Controller
{

public function categoriesIndex()
{
    $categories = Category::withCount([
            'books',
            'books as ebooks_count' => function ($query) {
                $query->where('has_ebook', true);
            },
            'books as audiobooks_count' => function ($query) {
                $query->where('has_audio', true);
            },
            'books as paperbacks_count' => function ($query) {
                $query->where('has_paperback', true);
            },
        ])
        ->orderBy('name')
        ->get();

    return view('categories.index', compact('categories'));
}


public function home()
{
    // Load first 6 categories only for homepage
    $categories = Category::with(['books' => function ($query) {
        $query->latest()->take(15);
    }])->take(6)->get();

    // Recently Added
    $recentBooks = Book::latest()->take(12)->get();

    // Top Trending (example logic)
    $trendingBooks = Book::withCount('reviews')
        ->orderByDesc('reviews_count')
        ->take(12)
        ->get();

        $recentlyViewedIds = session()->get('recently_viewed', []);

        $recentlyViewedBooks = Book::whereIn('id', $recentlyViewedIds)
            ->get()
            ->sortBy(function ($book) use ($recentlyViewedIds) {
                return array_search($book->id, $recentlyViewedIds);
            });

    return view('welcome', compact(
          'recentBooks',
          'trendingBooks',
          'categories',
          'recentlyViewedBooks'
      
    ));
}

public function show($id)
{
    $book = Book::with('reviews.user')->findOrFail($id);

    if ($book->is_premium && (!Auth::check() || !Auth::user()->canAccessBook($book))) {
        return redirect()->route('plans.index')
            ->with('error', 'This book requires a premium subscription.');
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
        

        $user = Auth::user();

        $recommendedBooks = collect();

        if ($user) {

            // Get category IDs of books user purchased
            $purchasedCategoryIds = \App\Models\OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('book')
            ->get()
            ->pluck('book.category_id')
            ->unique();

            // Recommend books from same categories
            $recommendedBooks = \App\Models\Book::whereIn('category_id', $purchasedCategoryIds)
                ->where('id', '!=', $book->id)
                ->take(5)
                ->get();
        }

        // Track recently viewed books in session

        $recentlyViewed = session()->get('recently_viewed', []);

        if (!in_array($id, $recentlyViewed)) {
            array_unshift($recentlyViewed, $id);
        }

        $recentlyViewed = array_slice($recentlyViewed, 0, 10);

        session()->put('recently_viewed', $recentlyViewed);

    return view('product-details', compact('book', 'reviews', 'recommendedBooks', 'recentlyViewed'));
}

 
public function categoryBooks(Category $category)
{
    $books = Book::where('category_id', $category->id)
                 ->paginate(Setting::get('books_per_page', 12));

   return view('category-books', [
    'category' => $category->name,
    'books' => $books
]);

}


}

