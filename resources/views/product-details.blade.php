<!DOCTYPE html>
<html>
<head>
    <title>{{ $book->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@include('common.header')
<div class="product-page">
    <!-- Left Column -->
    <div class="product-left">
        <img src="{{ $book->image }}" alt="{{ $book->name }}" class="product-image">
        
        <div class="action-box">
            @if($book->has_ebook)
                <form action="{{ route('cart.add', $book->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="format" value="ebook">
                    <input type="hidden" name="price" value="{{ $book->ebook_price }}">
                    <button type="submit" class="btnss btn-primary">
                        <span>📘</span>
                        <span>Rent Ebook - ${{ number_format($book->ebook_price, 2) }}</span>
                    </button>
                </form>
            @endif
            
            @if($book->has_audio)
                <form action="{{ route('cart.add', $book->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="format" value="audio">
                    <input type="hidden" name="price" value="{{ $book->audio_price }}">
                    <button type="submit" class="btnss btn-primary">
                        <span>🎧</span>
                        <span>Rent Audio - ${{ number_format($book->audio_price, 2) }}</span>
                    </button>
                </form>
            @endif
            
            @if($book->has_paperback)
                <form action="{{ route('cart.add', $book->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="format" value="paperback">
                    <input type="hidden" name="price" value="{{ $book->paperback_price }}">
                    <button type="submit" class="btnss btn-primary">
                        <span>📕</span>
                        <span>Buy Paperback - ${{ number_format($book->paperback_price, 2) }}</span>
                    </button>
                </form>
            @endif
            
            <button class="btnss btn-primary" data-id="{{ $book->id }}">
                <span>❤️</span>
                <span>Add to Wishlist</span>
            </button>
            
            <a href="{{ route('home') }}" class="btn-back">
                <span>←</span>
                <span>Back to Products</span>
            </a>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="product-right">
        <div class="product-header">
            <h1 class="product-title">{{ $book->name }}</h1>
            @if($book->is_premium)
                <p style="display:inline-flex; align-items:center; gap:8px; background:#fef3c7; color:#b45309; font-weight:600; padding:6px 12px; border-radius:999px;">
                    Premium book - Active subscription required
                </p>
            @endif
            
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">Author</span>
                    <span class="meta-value">
                        @if($book->author)
                            <a href="{{ route('authors.show', $book->author->id) }}" class="author-link">
                                {{ $book->author->name }}
                            </a>
                        @else
                            Unknown
                        @endif
                    </span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-label">Language</span>
                    <span class="meta-value">{{ $book->language }}</span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-label">Category</span>
                    <span class="meta-value">{{ $book->category->name ?? $book->category }}</span>
                </div>
                
                <div class="meta-item">
                    <span class="meta-label">Genre</span>
                    <span class="meta-value">{{ $book->genre->name ?? '-' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Format Details -->
        <div class="format-section">
            <h3 class="format-title">Available Formats</h3>
            <div class="format-grid">
                @if($book->has_ebook)
                <div class="format-card">
                    <span class="format-icon">📘</span>
                    <h4>E-Book</h4>
                    <p><strong>Pages:</strong> {{ $book->ebook_pages }}</p>
                    @if($book->ebook_pdf)
                        <a href="{{ asset($book->ebook_pdf) }}" download class="btnss btn-tertiary">
                            ⬇ Download Sample
                        </a>
                    @endif
                </div>
                @endif
                
                @if($book->has_audio)
                <div class="format-card">
                    <span class="format-icon">🎧</span>
                    <h4>Audiobook</h4>
                    <p><strong>Duration:</strong> {{ $book->audio_minutes }} minutes</p>
                    @if($book->audio_file)
                        <audio controls class="audio-player">
                            <source src="{{ asset($book->audio_file) }}" type="audio/mpeg">
                        </audio>
                    @endif
                </div>
                @endif
                
                @if($book->has_paperback)
                <div class="format-card">
                    <span class="format-icon">📕</span>
                    <h4>Paperback</h4>
                    <p><strong>Pages:</strong> {{ $book->paperback_pages }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Description -->
        <div class="description-section">
            <h2 class="description-title">Book Description</h2>
            <div class="description-content">
                {!! nl2br(e($book->description)) !!}
            </div>
        </div>
        
        <!-- Reviews -->
        <div class="reviews-section">
            <div class="reviews-header">
                <h2>Customer Reviews</h2>
                @if($book->reviews->count())
                    <div class="rating-badge">
                        <span>⭐</span>
                        <span>{{ number_format($book->reviews->avg('rating'), 1) }}/5</span>
                        <span>({{ $book->reviews->count() }} reviews)</span>
                    </div>
                @endif
            </div>
            
            @forelse($reviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <span class="review-author">
                            {{ $review->user->name}}
                            
                        </span>

                         @if(!$review->is_approved)
                            <span class="badge bg-warning">
                                Pending Approval
                            </span>
                        @endif

                        <span class="review-rating">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < $review->rating)
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </span>
                    </div>
                    
                    <p class="review-comment">{{ $review->comment }}</p>

                    <small>{{ $review->created_at->diffForHumans() }}</small>
                    
                    @auth
                        @if($review->user_id === auth()->id())
                            <div class="review-actions">
                                <a href="{{ route('reviews.edit', $review->id) }}" 
                                   class="btnss btn-tertiary" style="width: auto; padding: 8px 16px;">
                                    Edit Review
                                </a>
                                
                                <form method="POST" action="{{ route('reviews.destroy', $review->id) }}" 
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this review?')"
                                            class="btnss btn-tertiary" 
                                            style="width: auto; padding: 8px 16px; background: #fee2e2; color: #dc2626;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            @empty
                <div class="review-card">
                    <p style="color: #6b7280; text-align: center; padding: 20px;">
                        No reviews yet. Be the first to review this book!
                    </p>
                </div>
            @endforelse
        </div>
        
        <!-- Add Review Form -->
        @auth
            @if(!$book->reviews->where('user_id', auth()->id())->count())
                <div class="review-form-section">
                    <h3>Add Your Review</h3>
                    <form action="{{ route('reviews.store', $book->id) }}" method="POST" class="review-form">
                        @csrf
                        
                        <div>
                            <label for="rating">Your Rating</label>
                            <select name="rating" id="rating" class="rating-select" required>
                                <option value="">Select Rating</option>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Very Good</option>
                                <option value="3">⭐⭐⭐ Good</option>
                                <option value="2">⭐⭐ Fair</option>
                                <option value="1">⭐ Poor</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="comment">Your Review</label>
                            <textarea name="comment" id="comment" 
                                      placeholder="Share your thoughts about this book..."
                                      class="comment-textarea" required></textarea>
                        </div>
                        
                        <button type="submit" class="btnss btn-primary" style="width: auto; align-self: flex-start;">
                            Submit Review
                        </button>
                    </form>
                </div>
            @else
                <div class="review-card" style="background: #f0fdf4; border-color: #86efac;">
                    <p style="color: #059669; display: flex; align-items: center; gap: 10px;">
                        <span>✅</span>
                        <span>You've already reviewed this book. Thank you for your feedback!</span>
                    </p>
                </div>
            @endif
        @else
            <div class="review-card" style="background: #fef3c7; border-color: #fcd34d;">
                <p style="color: #d97706; text-align: center;">
                    <a href="{{ route('login') }}" style="color: #2563eb; font-weight: 600;">
                        Log in
                    </a> 
                    to add your review and share your thoughts about this book.
                </p>
            </div>
        @endauth
    </div>
</div>

@if($recommendedBooks->isNotEmpty())
    <section class="recommended-section">
        <h2 style="text-align: center;">Recommended For You</h2>

        <div class="book-grid">
            @foreach($recommendedBooks as $recBook)
                <a href="{{ route('books.show', $recBook->id) }}" class="book-card">
                    <img src="{{ $recBook->image }}" alt="">
                    <p>{{ $recBook->title }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif

@include('common.footer')


</body>

</html>
