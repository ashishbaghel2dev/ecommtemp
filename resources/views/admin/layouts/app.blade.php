<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My Website')</title>
</head>
<body>

    @include('admin.includes.header')


    <main>
        @yield('content')
    </main>

    @include('admin.includes.footer')

</body>
</html>
