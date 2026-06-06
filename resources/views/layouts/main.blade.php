<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}</title>
    <link rel="icon" href="{{ asset('src/img/logo-mini-dark.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" href="{{ asset('src/img/logo-mini-light.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="stylesheet" href="{{ asset('src/css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    @include('partials.confirm-delete-dialog')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('src/js/app.js') }}"></script>
    <script src="{{ asset('src/js/custom-script.js') }}"></script>
    @stack('scripts')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(
                    {{ Js::from(session('success.title')) }},
                    {{ Js::from(session('success.message')) }},
                    'success'
                );
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(
                    {{ Js::from(session('error.message') ?? 'Error') }},
                    '',
                    'error'
                );
            });
        </script>
    @endif
    @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(
                    {{ Js::from(session('warning.title') ?? 'Warning') }},
                    {{ Js::from(session('warning.message') ?? '') }},
                    'warning'
                );
            });
        </script>
    @endif
</body>

</html>
