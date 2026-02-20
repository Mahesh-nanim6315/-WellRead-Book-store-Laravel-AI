<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
       @vite(['resources/css/app.css',])
</head>
<body>
     
<div class="newsletter-page">
  
    <h1>Subscribe to our Newsletter 📬</h1>
    <p>Get updates on new books, offers, and author releases.</p>

    @if(session('success'))
        <div class="success-msg">{{ session('success') }}</div>
    @endif

    <form action="{{ route('newsletter.subscribe') }}" method="POST">
        @csrf
        <input 
            type="email" 
            name="email" 
            placeholder="Enter your email" 
            required
        >
        <button type="submit">Subscribe</button>
    </form>
</div>
</body>
</html>




