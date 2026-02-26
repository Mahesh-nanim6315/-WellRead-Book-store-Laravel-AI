<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;

class ProductController extends Controller
{
   
public function home(Request $request)
{
    $query = Book::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('language')) {
        $query->where('language', $request->language);
    }

    if ($request->filled('author_id')) {
        $query->where('author_id', $request->author_id);
    }

    if ($request->filled('genre_id')) {
        $query->where('genre_id', $request->genre_id);
    }

    if ($request->sort === 'price_asc') {
        $query->orderBy('price', 'asc');
    } elseif ($request->sort === 'price_desc') {
        $query->orderBy('price', 'desc');
    } else {
        $query->latest();
    }

    $books = $query->paginate(15)->withQueryString();
    $categories = Category::orderBy('name')->get();
    $authors = Author::orderBy('name')->get();
    $genres = Genre::orderBy('name')->get();
    $languages = Book::distinct()->orderBy('language')->pluck('language');

    return view('products', compact('books', 'categories', 'authors', 'genres', 'languages'));
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

        return view('product-details', compact('book', 'reviews'));
    }

    // Show Add Book Form
        public function create()
    {
         $categories = Category::all();
         $authors = Author::all();
         $genres     = Genre::all();
        return view('add-book', compact('categories', 'authors', 'genres'));
    }


public function audiobooks()
{
    $categories = Category::whereIn('name', [
        'Drama',
        'Thriller',
        'Social',
        'Fantasy',
        'Family',
        'Romance',
        'Humor',
        'Horror',
        'Historical'

    ])->get()->keyBy('name');

    $drama = Book::where('category_id', $categories['Drama']->id)
                 ->where('has_audio', true)
                 ->latest()->take(10)->get();

    $thriller = Book::where('category_id', $categories['Thriller']->id)
                    ->where('has_audio', true)
                    ->latest()->take(10)->get();
    

      $fantasy = Book::where('category_id', $categories['Fantasy']->id)
                    ->where('has_audio', true)
                    ->latest()->take(10)->get();

    $social = Book::where('category_id', $categories['Social']->id)
                  ->where('has_audio', true)
                  ->latest()->take(10)->get();

  

    $family = Book::where('category_id', $categories['Family']->id)
                  ->where('has_audio', true)
                  ->latest()->take(10)->get();

    $romance = Book::where('category_id', $categories['Romance']->id)
                   ->where('has_audio', true)
                   ->latest()->take(10)->get();

    $humor = Book::where('category_id', $categories['Humor']->id)
                   ->where('has_audio', true)
                   ->latest()->take(10)->get();

    $horror = Book::where('category_id', $categories['Horror']->id)
                   ->where('has_audio', true)
                   ->latest()->take(10)->get();
    $historical = Book::where('category_id', $categories['Historical']->id)
                   ->where('has_audio', true)
                   ->latest()->take(10)->get();

    return view('ebkcursol', compact(
        'drama',
        'thriller',
        'social',
        'family',
        'romance',
        'categories',
        'humor',
        'horror',
        'historical',   
    ));
}


public function ebooks()
{
    $categories = Category::whereIn('name', [
        'Drama',
        'Thriller',
        'Social',
        'Family',
        'Romance',
         'Humor',
         'Horror',
         'Historical'
    ])->get()->keyBy('name');

    $drama = Book::where('category_id', $categories['Drama']->id)
                 ->where('has_ebook', true)
                 ->latest()->take(10)->get();

    $thriller = Book::where('category_id', $categories['Thriller']->id)
                    ->where('has_ebook', true)
                    ->latest()->take(10)->get();

    $social = Book::where('category_id', $categories['Social']->id)
                  ->where('has_ebook', true)
                  ->latest()->take(10)->get();

    $family = Book::where('category_id', $categories['Family']->id)
                  ->where('has_ebook', true)
                  ->latest()->take(10)->get();

    $romance = Book::where('category_id', $categories['Romance']->id)
                   ->where('has_ebook', true)
                   ->latest()->take(10)->get();
    $humor = Book::where('category_id', $categories['Humor']->id)
                   ->where('has_ebook', true)
                   ->latest()->take(10)->get();
    $horror = Book::where('category_id', $categories['Horror']->id)
                   ->where('has_ebook', true)
                     ->latest()->take(10)->get();
    $historical = Book::where('category_id', $categories['Historical']->id)
                   ->where('has_ebook', true)
                     ->latest()->take(10)->get();


    return view('ebkcursol', compact(
        'drama',
        'thriller',
        'social',
        'family',
        'romance',
        'categories',
        'humor',
        'horror',
        'historical'
    ));
}


public function paperbacks()
{
    $categories = Category::whereIn('name', [
        'Drama',
        'Thriller',
        'Social',
        'Family',
        'Romance',
         'Humor',
        'Horror',
        'Historical'
    ])->get()->keyBy('name');

    $drama = Book::where('category_id', $categories['Drama']->id)
                 ->where('has_paperback', true)
                 ->latest()->take(10)->get();

    $thriller = Book::where('category_id', $categories['Thriller']->id)
                    ->where('has_paperback', true)
                    ->latest()->take(10)->get();

    $social = Book::where('category_id', $categories['Social']->id)
                  ->where('has_paperback', true)
                  ->latest()->take(10)->get();

    $family = Book::where('category_id', $categories['Family']->id)
                  ->where('has_paperback', true)
                  ->latest()->take(10)->get();

    $romance = Book::where('category_id', $categories['Romance']->id)
                   ->where('has_paperback', true)
                   ->latest()->take(10)->get();

    $humor = Book::where('category_id', $categories['Humor']->id)
                   ->where('has_paperback', true)
                     ->latest()->take(10)->get();
    $horror = Book::where('category_id', $categories['Horror']->id)
                   ->where('has_paperback', true)
                     ->latest()->take(10)->get();
    $historical = Book::where('category_id', $categories['Historical']->id)
                   ->where('has_paperback', true)
                     ->latest()->take(10)->get();

    return view('ebkcursol', compact(
        'drama',
        'thriller',
        'social',
        'family',
        'romance',
        'categories',
        'humor',
        'horror',
        'historical'
    ));
}

    /* carousel View all button */
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



