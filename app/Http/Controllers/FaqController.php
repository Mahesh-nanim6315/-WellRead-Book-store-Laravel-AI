<?php

namespace App\Http\Controllers;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'question' => 'How do I buy or rent a book?',
                'answer'   => 'You can add a book to cart, choose ebook/audio/paperback and complete payment.'
            ],
            [
                'question' => 'How long is ebook or audio access valid?',
                'answer'   => 'Ebooks and audiobooks are available for 30 days after purchase.'
            ],
            [
                'question' => 'Where can I see my purchased books?',
                'answer'   => 'Go to My Library after login to access ebooks and audiobooks.'
            ],
            [
                'question' => 'Can I download ebooks?',
                'answer'   => 'Yes, ebooks can be downloaded during the rental period.'
            ],
            [
                'question' => 'Is online payment safe?',
                'answer'   => 'Yes, we use secure payment gateways for all transactions.'
            ],
        ];

        return view('faq.index', compact('faqs'));
    }
}
