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
        <button class="icon-btn" data-toggle-theme title="Toggle theme" aria-label="Toggle theme">
            <x-icon name="moon" class="size-4 dark:hidden" />
            <x-icon name="sun" class="hidden size-4 dark:block" />
        </button>
    </div>
</header>

@include('partials.menu-seach')