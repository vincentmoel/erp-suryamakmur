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
    <script>
    function initSingleSelect(root, placeholder) {
        var trigger     = root.querySelector('button[aria-haspopup="listbox"]');
        var content     = root.querySelector('[role="listbox"]');
        var searchInput = root.querySelector('[data-ss-search]');
        var labelEl     = root.querySelector('[data-ss-label]');
        var hiddenInput = root.querySelector('[data-ss-input]');
        var emptyEl     = root.querySelector('[data-ss-empty]');
        var clearBtn    = root.querySelector('[data-ss-clear]');
        var chevron     = root.querySelector('[data-ss-chevron]');

        if (!trigger || !content) return;

        function setChevron(open) {
            if (chevron) chevron.style.transform = open ? 'rotate(180deg)' : '';
        }

        function openDropdown() {
            document.dispatchEvent(new CustomEvent('ss:close-all', { detail: { except: root } }));
            var r = trigger.getBoundingClientRect();
            content.style.top   = (r.bottom + 4) + 'px';
            content.style.left  = r.left + 'px';
            content.style.width = r.width + 'px';
            content.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            setChevron(true);
            if (searchInput) { searchInput.value = ''; filterItems(''); searchInput.focus(); }
        }

        function closeDropdown() {
            content.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
            setChevron(false);
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            content.classList.contains('hidden') ? openDropdown() : closeDropdown();
        });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target) && !content.contains(e.target)) closeDropdown();
        });

        window.addEventListener('scroll', function () { if (!content.classList.contains('hidden')) openDropdown(); }, true);
        window.addEventListener('resize', function () { if (!content.classList.contains('hidden')) closeDropdown(); });

        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                clearSelection();
                hiddenInput.dispatchEvent(new Event('change'));
            });
            clearBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); clearBtn.click(); }
            });
        }

        function clearSelection() {
            hiddenInput.value = '';
            labelEl.textContent = placeholder;
            labelEl.classList.add('text-muted-foreground');
            if (clearBtn) clearBtn.classList.add('hidden');
            root.querySelectorAll('[data-ss-item]').forEach(function (i) { i.setAttribute('aria-selected', 'false'); });
            closeDropdown();
        }

        content.addEventListener('click', function (e) {
            var item = e.target.closest('[data-ss-item]');
            if (!item) return;
            e.stopPropagation();

            root.querySelectorAll('[data-ss-item]').forEach(function (i) { i.setAttribute('aria-selected', 'false'); });

            var value = item.dataset.value;
            var label = item.dataset.label;

            item.setAttribute('aria-selected', 'true');
            hiddenInput.value = value;
            labelEl.textContent = label;
            labelEl.classList.remove('text-muted-foreground');
            if (clearBtn) clearBtn.classList.remove('hidden');

            hiddenInput.dispatchEvent(new Event('change'));
            closeDropdown();
        });

        function filterItems(query) {
            var q = query.toLowerCase();
            var visible = 0;
            root.querySelectorAll('[data-ss-item]').forEach(function (item) {
                var match = item.dataset.label.toLowerCase().includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (emptyEl) visible === 0 ? emptyEl.classList.remove('hidden') : emptyEl.classList.add('hidden');
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () { filterItems(searchInput.value); });
            searchInput.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        document.addEventListener('ss:close-all', function (e) {
            if (e.detail.except !== root) closeDropdown();
        });
    }
    </script>
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
    <script src="{{ asset('src/js/app.js') }}?v={{ filemtime(public_path('src/js/app.js')) }}"></script>
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
