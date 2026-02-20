<aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <span class="logo">📚</span>
        <span class="brand">BookStore</span>
        <button id="toggleBtn">⟨</button>
    </div>

    <ul class="menu">
        <li>
            <a href="{{ url('/dashboard') }}">
                <span class="icon">🏠</span>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.orders.index') }}">
                <span class="icon">📦</span>
                <span class="text">Orders</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.payments.index') }}">
                <span class="icon">💳</span>
                <span class="text">Payments</span>
            </a>
        </li>
        
        <li>
            <a href="{{ route('admin.books.index') }}">
                <span class="icon">📚</span>
                <span class="text">Add Books</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.authors.index') }}">
                <span class="icon">👥</span>
                <span class="text">Add Authors</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.users.index')}}">
                <span class="icon">🙍🏻‍♂️</span>
                <span class="text">Users</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.reviews.index') }}">
                <span class="icon">✍</span>
                <span class="text">Reviews</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.notifications.index') }}">
                <span class="icon">🔔</span>
                <span class="text">Notifications</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.roles_permissions.index') }}">
                <span class="icon">🔐</span>
                <span class="text">Roles & Permissions</span>
            </a>
        </li>

        <div style="margin-top: 200px;">
             <li>
                 <a href="#"> 
                 <span class="icon">⚙️</span>
                 <span class="text">Settings</span>
                 </a>
            </li>

             <li>
                 <a href="{{route('logout')}}">
                 <span class="icon">🔓</span>
                  <span class="text">Logout</span>
                 </a>
             </li> 

             
        </div>

    </ul>
</aside>


<script>
const wrapper = document.querySelector('.admin-wrapper');
const toggleBtn = document.getElementById('toggleBtn');

toggleBtn.addEventListener('click', () => {
    wrapper.classList.toggle('collapsed');

    const isCollapsed = wrapper.classList.contains('collapsed');
    toggleBtn.textContent = isCollapsed ? '⟩' : '⟨';

    localStorage.setItem('sidebar', isCollapsed ? 'collapsed' : 'expanded');
});

// Load saved state
if (localStorage.getItem('sidebar') === 'collapsed') {
    wrapper.classList.add('collapsed');
    toggleBtn.textContent = '⟩';
}

</script>

