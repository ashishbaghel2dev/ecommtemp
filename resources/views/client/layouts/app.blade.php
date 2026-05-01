<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My Website')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    @include('client.includes.navbar')


    <main>
        @yield('content')
    </main>

    @include('client.includes.footer')

</body>
</html>
