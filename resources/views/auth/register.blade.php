<x-guest-layout>
<div class="auth-shell">
    <section class="auth-media">
        <video class="auth-video" autoplay muted loop playsinline poster="{{ asset('images/newslide2.jpg') }}">
            <source src="{{ asset('videos/auth-bg.mp4') }}" type="video/mp4">
        </video>

        <img class="auth-slide is-active" src="{{ asset('images/newslide2.jpg') }}" alt="Library books">
        <img class="auth-slide" src="{{ asset('images/bookslide1.jpg') }}" alt="Books row">
        <img class="auth-slide" src="{{ asset('images/newslide1.jpg') }}" alt="Reading mood">

        <div class="auth-media-overlay"></div>

        <div class="auth-quote-wrap">
            <p class="auth-quote" id="authQuoteText">Open a book, and you open your mind.</p>
            <span class="auth-quote-author" id="authQuoteAuthor">BookStore</span>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-card auth-card--split">
            <h2 class="auth-title">Create Account</h2>

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                    >
                    @error('name')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                    >
                    @error('email')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    >
                    @error('password')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    >
                    @error('password_confirmation')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="auth-switch">
                    <span>Already have an account?</span>
                    <a href="{{ route('login') }}">Login</a>
                </div>

                <div class="form-actions form-actions--single">
                    <button type="submit" class="btn-primary">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.auth-slide');
    let slideIndex = 0;

    setInterval(() => {
        if (!slides.length) {
            return;
        }

        slides[slideIndex].classList.remove('is-active');
        slideIndex = (slideIndex + 1) % slides.length;
        slides[slideIndex].classList.add('is-active');
    }, 4500);

    const quotes = [
        { text: 'Open a book, and you open your mind.', author: 'BookStore' },
        { text: 'Read more, imagine more, become more.', author: 'BookStore' },
        { text: 'Every page is a new beginning.', author: 'BookStore' }
    ];

    const quoteText = document.getElementById('authQuoteText');
    const quoteAuthor = document.getElementById('authQuoteAuthor');
    let quoteIndex = 0;

    setInterval(() => {
        if (!quoteText || !quoteAuthor) {
            return;
        }

        quoteIndex = (quoteIndex + 1) % quotes.length;
        quoteText.textContent = quotes[quoteIndex].text;
        quoteAuthor.textContent = quotes[quoteIndex].author;
    }, 5500);
});
</script>
