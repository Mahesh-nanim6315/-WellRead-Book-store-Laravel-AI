<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorControllers extends Controller
{
    
    public function index(Request $request)
{
    $query = Author::withCount('books');

    // 🔍 Search by name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // 🎯 Filter by minimum books
    if ($request->filled('min_books')) {
        $query->having('books_count', '>=', $request->min_books);
    }

    $authors = $query->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.authors.index', compact('authors'));
}

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|url',
            'bio' => 'nullable|string',
        ]);

        Author::create($request->all());

        return redirect()->route('admin.authors.index')
            ->with('success','Author created successfully');
    }

    public function show(Author $author)
    {
        return view('admin.authors.show', compact('author'));
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|url',
            'bio' => 'nullable|string',
        ]);

        $author->update($request->all());

        return redirect()->route('admin.authors.index')
            ->with('success','Author updated successfully');
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return back()->with('success','Author deleted successfully');
    }
}

