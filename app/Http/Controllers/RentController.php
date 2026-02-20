<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class RentController extends Controller
{
    public function rentEbook(Book $book)
    {
        return redirect()->back()->with('success', 'E-Book rented successfully!');
    }

    public function rentAudio(Book $book)
    {
        return redirect()->back()->with('success', 'Audio book rented successfully!');
    }
}
