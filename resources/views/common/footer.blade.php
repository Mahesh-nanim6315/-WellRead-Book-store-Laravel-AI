@vite(['resources/css/footer.css', 'resources/js/app.js'])

<footer class="footer">
    <div class="footer-container">

        <!-- Brand Section -->
        <div class="footer-brand">
            <h2>BookAI</h2>
            <p>
                Discover books smarter with AI-powered recommendations,
                semantic search, and personalized reading experiences.
            </p>

            <div class="social-icons">
                <a href="#"><i class="fab fa-github"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/books">Browse Books</a></li>
                <li><a href="/categories">Categories</a></li>
                <li><a href="/recommendations">AI Recommendations</a></li>
            </ul>
        </div>

        <!-- Categories -->
        <div class="footer-links">
            <h3>Top Categories</h3>
            <ul>
                <li><a href="#">Programming</a></li>
                <li><a href="#">AI & ML</a></li>
                <li><a href="#">Self Development</a></li>
                <li><a href="#">Business</a></li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-newsletter">
            <h3>Subscribe</h3>
            <p>Get AI-curated book updates directly to your inbox.</p>
            
                @if(session('success'))
                    <div class="success-msg">{{ session('success') }}</div>
                @endif
                
            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} BookAI. All rights reserved.</p>
        <div class="legal-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms</a>
            <a href="#">Contact</a>
        </div>
    </div>
</footer>