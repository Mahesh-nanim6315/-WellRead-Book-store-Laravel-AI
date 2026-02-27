<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Setting;
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

    $books = $query->latest()->paginate(Setting::get('books_per_page', 12))->withQueryString();

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
            'is_premium' => 'nullable|boolean',
            'has_ebook' => 'nullable|boolean',
            'has_audio' => 'nullable|boolean',
            'has_paperback' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_premium'] = $request->boolean('is_premium');
        $data['has_ebook'] = $request->boolean('has_ebook');
        $data['has_audio'] = $request->boolean('has_audio');
        $data['has_paperback'] = $request->boolean('has_paperback');

        Book::create($data);

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
            'is_premium' => 'nullable|boolean',
            'has_ebook' => 'nullable|boolean',
            'has_audio' => 'nullable|boolean',
            'has_paperback' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_premium'] = $request->boolean('is_premium');
        $data['has_ebook'] = $request->boolean('has_ebook');
        $data['has_audio'] = $request->boolean('has_audio');
        $data['has_paperback'] = $request->boolean('has_paperback');

        $book->update($data);

        return redirect()->route('admin.books.index')
            ->with('success','Book updated successfully');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return back()->with('success','Book deleted successfully');
    }
}

