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

        <div class="content">
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>
