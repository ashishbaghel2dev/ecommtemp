<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $adminSettings['dashboard_label'] ?? 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">


</head>
<body data-dashboard-theme="{{ $adminSettings['dashboard_theme'] ?? 'green' }}">

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

@php
    $toasts = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('status') ? ['type' => 'success', 'message' => session('status')] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
        session('info') ? ['type' => 'info', 'message' => session('info')] : null,
    ])->filter()->values();

    if (isset($errors) && $errors->any()) {
        $toasts = $toasts->merge(
            collect($errors->all())->map(fn ($message) => ['type' => 'error', 'message' => $message])
        );
    }
@endphp
<script>
    window.__APP_TOASTS__ = @json($toasts->values());
</script>
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/admin/app.js') }}"></script>


</body>
</html>
