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


    @include('admin.includes.sidebar')

    <div class="dashboard-main">


        <header class="dashboard-header">
            @include('admin.includes.header')
        </header>


        <main class="dashboard-content">
            @yield('content')

        </main>


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