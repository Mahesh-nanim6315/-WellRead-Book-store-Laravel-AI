<aside id="sidebar" class="sidebar admin-sidebar" aria-label="Admin navigation">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" aria-label="Go to dashboard">
            <span class="logo-mark">WR</span>
            <span class="brand">WellRead Admin</span>
        </a>

        <button id="toggleBtn" class="sidebar-toggle" type="button" aria-label="Collapse sidebar" aria-expanded="true">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M15 18l-6-6 6-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
        </button>
    </div>

    <nav class="sidebar-nav">
        <p class="menu-title">Overview</p>
        <ul class="menu">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-18v6h8V3h-8z"></path></svg>
                    </span>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3 7h18v2H3V7zm2 4h14l-1.2 9H6.2L5 11zm4-8h6l1 2H8l1-2z"></path></svg>
                    </span>
                    <span class="text">Orders</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <a href="{{ route('admin.payments.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3 6h18a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm0 4h18M7 14h4"></path></svg>
                    </span>
                    <span class="text">Payments</span>
                </a>
            </li>
        </ul>

        <p class="menu-title">Catalog</p>
        <ul class="menu">
            <li class="{{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                <a href="{{ route('admin.books.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 5a2 2 0 0 1 2-2h11a3 3 0 0 1 3 3v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5zm2 0v13h12V6a1 1 0 0 0-1-1H6z"></path></svg>
                    </span>
                    <span class="text">Books</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.authors.*') ? 'active' : '' }}">
                <a href="{{ route('admin.authors.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M16 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4zM6 14a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm10 1c-3.3 0-6 1.8-6 4v2h12v-2c0-2.2-2.7-4-6-4zM6 16c-2.8 0-5 1.4-5 3.1V21h7v-1.9A4.8 4.8 0 0 1 10 16z"></path></svg>
                    </span>
                    <span class="text">Authors</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <a href="{{ route('admin.reviews.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M5 3h10l4 4v14H5V3zm9 1.5V8h3.5L14 4.5zM8 12h8v2H8v-2zm0 4h8v2H8v-2z"></path></svg>
                    </span>
                    <span class="text">Reviews</span>
                </a>
            </li>
        </ul>

        <p class="menu-title">Administration</p>
        <ul class="menu">
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M16 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4zM6 14a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm10 1c-3.3 0-6 1.8-6 4v2h12v-2c0-2.2-2.7-4-6-4zM6 16c-2.8 0-5 1.4-5 3.1V21h7v-1.9A4.8 4.8 0 0 1 10 16z"></path></svg>
                    </span>
                    <span class="text">Users</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                <a href="{{ route('admin.notifications.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-6V11a7 7 0 1 0-14 0v5l-2 2v1h18v-1l-2-2z"></path></svg>
                    </span>
                    <span class="text">Notifications</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.roles_permissions.*') ? 'active' : '' }}">
                <a href="{{ route('admin.roles_permissions.index') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 1l3 2 3.5-.5 1 3.4L23 8l-1.5 3 1.5 3-3.5 2.1-1 3.4L15 21l-3 2-3-2-3.5.5-1-3.4L1 14l1.5-3L1 8l3.5-2.1 1-3.4L9 3l3-2zm0 7a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"></path></svg>
                    </span>
                    <span class="text">Roles & Permissions</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings') }}">
                    <span class="icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M19.4 13a7.8 7.8 0 0 0 .1-1 7.8 7.8 0 0 0-.1-1l2.1-1.6-2-3.4-2.5 1a7.8 7.8 0 0 0-1.7-1l-.4-2.7h-4l-.4 2.7a7.8 7.8 0 0 0-1.7 1l-2.5-1-2 3.4L4.6 11a7.8 7.8 0 0 0-.1 1 7.8 7.8 0 0 0 .1 1l-2.1 1.6 2 3.4 2.5-1a7.8 7.8 0 0 0 1.7 1l.4 2.7h4l.4-2.7a7.8 7.8 0 0 0 1.7-1l2.5 1 2-3.4L19.4 13zM12 15.5A3.5 3.5 0 1 1 15.5 12 3.5 3.5 0 0 1 12 15.5z"></path></svg>
                    </span>
                    <span class="text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <span class="footer-user">{{ auth()->user()->name ?? 'Admin' }}</span>
        <span class="footer-role">Signed in</span>
    </div>
</aside>

<script>
(() => {
    const wrapper = document.querySelector('.admin-wrapper');
    const toggleBtn = document.getElementById('toggleBtn');
    if (!wrapper || !toggleBtn) return;

    const setState = (collapsed) => {
        wrapper.classList.toggle('collapsed', collapsed);
        toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        toggleBtn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        localStorage.setItem('sidebar', collapsed ? 'collapsed' : 'expanded');
    };

    const isDesktop = window.matchMedia('(min-width: 1025px)').matches;
    if (isDesktop && localStorage.getItem('sidebar') === 'collapsed') {
        setState(true);
    }

    toggleBtn.addEventListener('click', () => {
        if (!window.matchMedia('(min-width: 1025px)').matches) {
            wrapper.classList.toggle('sidebar-open');
            return;
        }
        setState(!wrapper.classList.contains('collapsed'));
    });

    document.addEventListener('click', (event) => {
        if (!wrapper.classList.contains('sidebar-open')) return;
        const insideSidebar = event.target.closest('.admin-sidebar');
        const clickedToggle = event.target.closest('#toggleBtn');
        if (!insideSidebar && !clickedToggle) {
            wrapper.classList.remove('sidebar-open');
        }
    });
})();
</script>
