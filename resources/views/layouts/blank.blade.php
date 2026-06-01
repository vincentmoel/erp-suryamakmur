<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('src/css/styles.css') }}">
    @stack('styles')
    <script>
        try {
            if (localStorage.getItem("mocci-theme") === "dark") document.documentElement.classList.add("dark")
        } catch (e) {}
    </script>
</head>

<body class="min-h-screen bg-background text-foreground antialiased">

    @yield('content')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    @stack('scripts')

</body>

</html>
