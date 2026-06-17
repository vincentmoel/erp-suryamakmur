<header class="sticky top-0 z-20 flex h-14 items-center justify-between border-b bg-card px-4 py-2 sm:px-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-3">
        <button class="icon-btn lg:hidden" data-toggle-sidebar aria-label="Open sidebar"><x-icon name="panel-left" class="size-5" /></button>
        <button class="icon-btn hidden lg:inline-flex" data-collapse-sidebar aria-label="Collapse sidebar"><x-icon name="panel-left" class="size-5" /></button>
        <div class="hidden h-4 w-px bg-border sm:block"></div>
        @include('partials.breadcrumb')
    </div>
    <div class="flex items-center gap-1.5">
        <button class="btn btn-outline hidden h-8 w-56 justify-start text-muted-foreground md:inline-flex"
            data-toggle-palette><x-icon name="search" class="size-4" /> Search... <kbd
                class="ml-auto rounded border px-1 text-[10px]">⌘K</kbd></button>

        {{-- Quick Actions --}}
        <div class="relative" id="quick-actions">
            <button type="button" id="quick-actions-btn"
                class="btn btn-outline h-8 gap-1.5 px-3 text-sm"
                aria-haspopup="true" aria-expanded="false">
                <x-icon name="plus" class="size-4" />
                <span class="hidden sm:inline">Quick Create</span>
            </button>
            <div id="quick-actions-dropdown"
                class="absolute right-0 top-full mt-1 hidden w-48 rounded-lg border bg-card shadow-lg z-50 overflow-hidden py-1">
                <a href="{{ route('invoices.create') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-sm hover:bg-accent transition-colors">
                    <x-icon name="invoice" class="size-4 text-muted-foreground" />
                    <span>{{ __('general.nav_invoices') }}</span>
                </a>
                <a href="{{ route('bills.create') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-sm hover:bg-accent transition-colors">
                    <x-icon name="receipt" class="size-4 text-muted-foreground" />
                    <span>{{ __('general.nav_bills') }}</span>
                </a>
                <div class="my-1 border-t"></div>
                <a href="{{ route('customers.create') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-sm hover:bg-accent transition-colors">
                    <x-icon name="contact" class="size-4 text-muted-foreground" />
                    <span>{{ __('general.nav_customers') }}</span>
                </a>
                <a href="{{ route('products.create') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-sm hover:bg-accent transition-colors">
                    <x-icon name="box" class="size-4 text-muted-foreground" />
                    <span>{{ __('general.nav_products') }}</span>
                </a>
            </div>
        </div>
        @auth
        <div class="relative" id="lang-switcher">
            <button type="button" id="lang-btn"
                class="icon-btn text-xl"
                title="{{ app()->getLocale() === 'id' ? 'Bahasa Indonesia' : 'English' }}"
                aria-haspopup="true" aria-expanded="false">
                {{ app()->getLocale() === 'id' ? '🇮🇩' : '🇺🇸' }}
            </button>
            <div id="lang-dropdown"
                class="absolute right-0 top-full mt-1 hidden w-52 rounded-lg border bg-card shadow-lg z-50 overflow-hidden">
                <button type="button" onclick="changeLanguage('id')"
                    class="flex w-full items-center gap-3 px-3 py-2.5 text-sm hover:bg-accent transition-colors {{ app()->getLocale() === 'id' ? 'font-semibold text-primary' : '' }}">
                    <span class="text-xl leading-none">🇮🇩</span>
                    <span>Bahasa Indonesia</span>
                    @if(app()->getLocale() === 'id')
                        <x-icon name="check" class="ml-auto size-4 text-primary" />
                    @endif
                </button>
                <button type="button" onclick="changeLanguage('en')"
                    class="flex w-full items-center gap-3 px-3 py-2.5 text-sm hover:bg-accent transition-colors {{ app()->getLocale() === 'en' ? 'font-semibold text-primary' : '' }}">
                    <span class="text-xl leading-none">🇺🇸</span>
                    <span>English</span>
                    @if(app()->getLocale() === 'en')
                        <x-icon name="check" class="ml-auto size-4 text-primary" />
                    @endif
                </button>
            </div>
        </div>
        @endauth
        <button class="icon-btn" data-toggle-theme title="Toggle theme" aria-label="Toggle theme">
            <x-icon name="moon" class="size-4 dark:hidden" />
            <x-icon name="sun" class="hidden size-4 dark:block" />
        </button>
    </div>

<script>
(function () {
    const btn = document.getElementById('quick-actions-btn');
    const dropdown = document.getElementById('quick-actions-dropdown');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const open = !dropdown.classList.contains('hidden');
        dropdown.classList.toggle('hidden', open);
        btn.setAttribute('aria-expanded', String(!open));
    });

    document.addEventListener('click', function () {
        dropdown.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
    });

    dropdown.addEventListener('click', function (e) { e.stopPropagation(); });
})();

function changeLanguage(lang) {
    fetch('/language/' + lang, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(function() { window.location.reload(); });
}

(function () {
    const btn = document.getElementById('lang-btn');
    const dropdown = document.getElementById('lang-dropdown');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const open = !dropdown.classList.contains('hidden');
        dropdown.classList.toggle('hidden', open);
        btn.setAttribute('aria-expanded', String(!open));
    });

    document.addEventListener('click', function () {
        dropdown.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
    });

    dropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });
})();
</script>
</header>

@include('partials.menu-seach')