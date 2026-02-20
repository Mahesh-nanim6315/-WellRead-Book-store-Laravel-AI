<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        form {
    max-width: 500px;
    margin: 40px auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
}

select, textarea, button {
    width: 100%;
    padding: 10px;
    margin-top: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

select {
    background: #fff;
    cursor: pointer;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

button {
    background: #4CAF50;
    color: #fff;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #45a049;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

    </style>
</head>
<body>
    
<h2>Edit Review</h2>

<form method="POST" action="{{ route('reviews.update', $review->id) }}">
    @csrf
    @method('PUT')

    <select name="rating" required>
        @for($i = 5; $i >= 1; $i--)
            <option value="{{ $i }}" {{ $review->rating == $i ? 'selected' : '' }}>
                {{ str_repeat('⭐', $i) }}
            </option>
        @endfor
    </select>

    <textarea name="comment" required>{{ $review->comment }}</textarea>

    <button type="submit">Update Review</button>
</form>
</body>
</html>




