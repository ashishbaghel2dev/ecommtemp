<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">


</head>
<body>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <aside class="dashboard-sidebar-box">
        @include('admin.includes.sidebar')
    </aside>

    <!-- Main -->
    <div class="dashboard-main">

        <!-- Header -->
        <header class="dashboard-header">
            @include('admin.includes.header')
        </header>

        <!-- Content -->
        <main class="dashboard-content">
            @yield('content')

         
        </main>

        <!-- Footer -->
        <footer class="dashboard-footer">
            @include('admin.includes.footer')
        </footer>

    </div>

</div>



<script src="{{ asset('js/admin/layout/sidebar.js') }}"></script>
<script src="{{ asset('js/admin/layout/header.js') }}"></script>
<script src="{{ asset('js/admin/layout/bell.js') }}"></script>

<script src="{{ asset('js/admin/app.js') }}"></script>


</body>
</html>