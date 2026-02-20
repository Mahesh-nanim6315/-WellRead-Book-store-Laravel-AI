<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Http\Request;

class BookControllers extends Controller
{
   public function index(Request $request)
{
    $query = Book::with(['author','category','genre']);

    // 🔍 Search by name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // 🎯 Filter by author
    if ($request->filled('author')) {
        $query->where('author_id', $request->author);
    }

    // 🎯 Filter by category
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // 🎯 Filter by genre
    if ($request->filled('genre')) {
        $query->where('genre_id', $request->genre);
    }

    $books = $query->latest()->paginate(10)->withQueryString();

    $authors = Author::all();
    $categories = Category::all();
    $genres = Genre::all();

    return view('admin.books.index', compact(
        'books',
        'authors',
        'categories',
        'genres'
    ));
}


    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        $genres = Genre::all();

        return view('admin.books.create', compact('authors','categories','genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'language' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'genre_id' => 'required|exists:genres,id',
            'image' => 'required|url',
            'price' => 'required|numeric',
        ]);

        Book::create($request->all());

        return redirect()->route('admin.books.index')
            ->with('success','Book created successfully');
    }

    public function show(Book $book)
    {
        $book->load(['author', 'category', 'genre']);

        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $authors = Author::all();
        $categories = Category::all();
        $genres = Genre::all();

        return view('admin.books.edit', compact('book','authors','categories','genres'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'language' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'genre_id' => 'required|exists:genres,id',
            'image' => 'required|url',
            'price' => 'required|numeric',
        ]);

        $book->update($request->all());

        return redirect()->route('admin.books.index')
            ->with('success','Book updated successfully');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return back()->with('success','Book deleted successfully');
    }
}

