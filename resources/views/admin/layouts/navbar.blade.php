@php
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
    $userName = auth()->check() ? auth()->user()->name : 'Admin';
    $userInitial = strtoupper(substr($userName, 0, 1));
@endphp

<nav class="topbar" aria-label="Admin top navigation">
    <div class="topbar-left">
        <button id="mobileSidebarToggle" class="icon-btn mobile-sidebar-btn" type="button" aria-label="Open sidebar">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 6h18M3 12h18M3 18h18" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"></path>
            </svg>
        </button>

        <div>
            <h2 id="dash" class="page-title">Admin Dashboard</h2>
            <p class="page-subtitle">Manage catalog, orders, and operations</p>
        </div>
    </div>

    <div class="topbar-right">
        <button id="themeToggle" class="icon-btn theme-btn" type="button" aria-label="Enable dark mode" aria-pressed="false">
            <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20.7 14.1A9 9 0 0 1 9.9 3.3a9 9 0 1 0 10.8 10.8z"></path>
            </svg>
            <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 17a5 5 0 1 1 5-5 5 5 0 0 1-5 5zm0-14v3m0 12v3M4.2 4.2l2.1 2.1m11.4 11.4l2.1 2.1M3 12h3m12 0h3M4.2 19.8l2.1-2.1m11.4-11.4l2.1-2.1" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"></path>
            </svg>
        </button>

        @auth
            <div class="notify-dropdown">
                <button id="notifyToggle" class="icon-btn notify-trigger" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="notifyMenu">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-6V11a7 7 0 1 0-14 0v5l-2 2v1h18v-1l-2-2z"></path>
                    </svg>
                    @if($unreadCount)
                        <span class="badge">{{ $unreadCount }}</span>
                    @endif
                </button>

                <ul id="notifyMenu" class="topbar-dropdown notify-menu" hidden>
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
                        <a href="{{ route('admin.notifications.index') }}" class="view-all">View all notifications</a>
                    </li>
                </ul>
            </div>
        @endauth

        <div class="user-chip" title="{{ $userName }}">
            <span class="avatar">{{ $userInitial }}</span>
            <span class="username">{{ $userName }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>

<script>
(() => {
    const wrapper = document.querySelector('.admin-wrapper');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const themeToggleBtn = document.getElementById('themeToggle');
    const notifyToggle = document.getElementById('notifyToggle');
    const notifyMenu = document.getElementById('notifyMenu');

    if (mobileSidebarToggle && wrapper) {
        mobileSidebarToggle.addEventListener('click', () => {
            wrapper.classList.toggle('sidebar-open');
        });
    }

    if (themeToggleBtn) {
        const setTheme = (theme) => {
            const isDark = theme === 'dark';
            document.body.classList.toggle('dark-mode', isDark);
            themeToggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            themeToggleBtn.setAttribute('aria-label', isDark ? 'Enable light mode' : 'Enable dark mode');
            themeToggleBtn.classList.toggle('is-dark', isDark);
        };

        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            const newTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            setTheme(newTheme);
        });
    }

    if (notifyToggle && notifyMenu) {
        const closeNotifications = () => {
            notifyMenu.hidden = true;
            notifyToggle.setAttribute('aria-expanded', 'false');
        };

        notifyToggle.addEventListener('click', (event) => {
            event.preventDefault();
            const isOpen = !notifyMenu.hidden;
            notifyMenu.hidden = isOpen;
            notifyToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });

        document.addEventListener('click', (event) => {
            if (notifyMenu.hidden) return;
            if (!event.target.closest('.notify-dropdown')) {
                closeNotifications();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeNotifications();
            }
        });
    }
})();
</script>
