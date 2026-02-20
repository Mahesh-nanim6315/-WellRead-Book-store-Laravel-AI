<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('newsletter.index');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email'
        ]);

        Newsletter::create([
            'email' => $request->email
        ]);

        return back()->with('success', 'Thanks for subscribing!');
    }
}
