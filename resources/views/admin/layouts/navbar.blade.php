<nav class="topbar">
    <h2 id="dash">Admin Dashboard</h2>

    <div class="topbar-right">
        <button id="themeToggle" class="theme-btn" type="button" aria-label="Toggle theme">
            🌙
        </button>
        
        @auth
        <!-- Notifications Dropdown -->
        <div class="dropdown">
            <a href="#" class="bell" onclick="toggleDropdown(event)">
                @if(auth()->user()->unreadNotifications->count())
                    <span class="badge">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
                <img src="{{ asset('images/icon-bell.png') }}" alt="Notifications"/>
            </a>

            <ul class="dropdown-menu">
                @forelse(auth()->user()->notifications->take(5) as $notification)
                    <li>
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                           class="{{ $notification->read_at ? '' : 'unread' }}">
                            {{ $notification->data['message'] ?? 'New Notification' }}
                        </a>
                    </li>
                @empty
                    <li class="empty">No notifications</li>
                @endforelse

                <li class="divider"></li>
                <li>
                    <a href="{{ route('admin.notifications.index') }}" class="view-all">
                        View All
                    </a>
                </li>
            </ul>
        </div>
        @endauth

        <!-- User Name -->
        <div class="username">
            {{ auth()->user()->name }}
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>

<script>
function toggleDropdown(event) {
    event.preventDefault();
    const menu = event.currentTarget.nextElementSibling;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

(function () {
    const themeToggleBtn = document.getElementById('themeToggle');
    if (!themeToggleBtn) return;

    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            themeToggleBtn.textContent = '☀️';
        } else {
            document.body.classList.remove('dark-mode');
            themeToggleBtn.textContent = '🌙';
        }
    }

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    themeToggleBtn.addEventListener('click', () => {
        const newTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        setTheme(newTheme);
    });
})();
</script>
