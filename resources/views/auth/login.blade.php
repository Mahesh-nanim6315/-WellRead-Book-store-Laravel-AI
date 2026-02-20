  @vite(['resources/css/app.css', 'resources/js/app.js'])
<x-guest-layout>
<div class="auth-shell">
    <section class="auth-media">
        <!-- <video class="auth-video" autoplay muted loop playsinline poster="{{ asset('images/newslide1.jpg') }}">
            <source src="{{ asset('videos/cloudv.mp4') }}" type="video/mp4">
        </video> -->

        <img class="auth-slide is-active" src="{{ asset('images/newslide1.jpg') }}" alt="Books shelf">
        <img class="auth-slide" src="{{ asset('images/newslide2.jpg') }}" alt="Books and coffee">
        <img class="auth-slide" src="{{ asset('images/bookslide1.jpg') }}" alt="Reading corner">

        <div class="auth-media-overlay"></div>

        <div class="auth-quote-wrap">
            <p class="auth-quote" id="authQuoteText">A reader lives a thousand lives before he dies.</p>
            <span class="auth-quote-author" id="authQuoteAuthor">George R.R. Martin</span>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-card auth-card--split">
            <h2 class="auth-title">Login</h2>

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
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
                    >
                    @error('password')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="auth-switch">
                    <span>Don't have an account?</span>
                    <a href="{{ route('register') }}">Register</a>
                </div>

                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Forgot password?
                        </a>
                    @endif

                    <button type="submit" class="btn-primary">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function () {
            this.parentElement.classList.remove('focused');
        });
    });

    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');

    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
    }

    inputs.forEach(input => {
        input.addEventListener('input', function () {
            this.classList.remove('invalid', 'valid');

            if (this.value.trim() === '') {
                return;
            }

            if (this.checkValidity()) {
                this.classList.add('valid');
            } else {
                this.classList.add('invalid');
            }
        });
    });

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
        { text: 'A reader lives a thousand lives before he dies.', author: 'George R.R. Martin' },
        { text: 'Books are a uniquely portable magic.', author: 'Stephen King' },
        { text: 'Today a reader, tomorrow a leader.', author: 'Margaret Fuller' }
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

