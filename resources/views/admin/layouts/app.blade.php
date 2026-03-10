<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
        @vite(['resources/css/admin.css'])
</head>
<body>

<div class="admin-wrapper">
    @include('admin.layouts.sidebar')

    <div class="main-content">
        @include('admin.layouts.navbar')

        <div class="toast-container" id="admin-toast-container" aria-live="polite" aria-atomic="true">
            @if (session('success'))
                <div class="toast toast-success">
                    <span class="toast-text">{{ session('success') }}</span>
                    <button type="button" class="toast-close" aria-label="Dismiss">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="toast toast-error">
                    <span class="toast-text">{{ session('error') }}</span>
                    <button type="button" class="toast-close" aria-label="Dismiss">&times;</button>
                </div>
            @endif
            @if (session('status'))
                <div class="toast toast-info">
                    <span class="toast-text">{{ session('status') }}</span>
                    <button type="button" class="toast-close" aria-label="Dismiss">&times;</button>
                </div>
            @endif
            @if ($errors->any())
                <div class="toast toast-error">
                    <span class="toast-text">{{ $errors->first() }}</span>
                    <button type="button" class="toast-close" aria-label="Dismiss">&times;</button>
                </div>
            @endif
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.toast').forEach((toast) => {
    const closeBtn = toast.querySelector('.toast-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => toast.remove());
    }
    setTimeout(() => {
        toast.classList.add('is-hiding');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
});
</script>

</body>
</html>
