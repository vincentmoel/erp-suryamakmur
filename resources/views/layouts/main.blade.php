<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Mocci Admin HTML</title>
    <link rel="icon" href="./public/favicon.svg">
    <link rel="stylesheet" href="{{ asset('src/css/styles.css') }}">
    <script>
        try {
            if (localStorage.getItem("mocci-theme") === "dark") document.documentElement.classList.add("dark")
        } catch (e) {}
    </script>
    @stack('styles')
</head>

<body class="min-h-screen bg-background text-foreground antialiased">

    @include('partials.sidebar')

    <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/40"></div>
    <div class="app-shell min-h-screen transition-[padding] duration-200 ease-linear lg:pl-64">
        @include('partials.header')
        <main class="p-4 lg:p-6">
            <div class="space-y-4">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="{{ asset('src/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
