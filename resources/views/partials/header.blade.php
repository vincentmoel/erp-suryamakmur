<header class="sticky top-0 z-20 flex h-14 items-center justify-between border-b bg-card px-4 py-2 sm:px-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-3">
        <button class="icon-btn lg:hidden" data-toggle-sidebar aria-label="Open sidebar"><i data-lucide="panel-left"
                class="size-5"></i></button>
        <button class="icon-btn hidden lg:inline-flex" data-collapse-sidebar aria-label="Collapse sidebar"><i
                data-lucide="panel-left" class="size-5"></i></button>
        <div class="hidden h-4 w-px bg-border sm:block"></div>
        <nav aria-label="breadcrumb" data-slot="breadcrumb" class="hidden sm:block">
            <ol data-slot="breadcrumb-list"
                class="flex flex-wrap items-center gap-1.5 break-words text-sm text-muted-foreground sm:gap-2.5">
                <li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5">
                    <a data-slot="breadcrumb-link" class="transition-colors hover:text-foreground"
                        href="#">Home</a>
                </li>
                <li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" class="[&>svg]:size-3.5"><i
                        data-lucide="chevron-right" class="size-3.5"></i></li>
                <li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5">
                    <a data-slot="breadcrumb-link" class="transition-colors hover:text-foreground"
                        href="#">Dashboard</a>
                </li>
                <li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" class="[&>svg]:size-3.5"><i
                        data-lucide="chevron-right" class="size-3.5"></i></li>
                <li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5">
                    <span data-slot="breadcrumb-page" role="link" aria-disabled="true" aria-current="page"
                        class="font-normal text-foreground">Free</span>
                </li>
            </ol>
        </nav>
    </div>
    <div class="flex items-center gap-1.5">
        <button class="btn btn-outline hidden h-8 w-56 justify-start text-muted-foreground md:inline-flex"
            data-toggle-palette><i data-lucide="search" class="size-4"></i> Search... <kbd
                class="ml-auto rounded border px-1 text-[10px]">⌘K</kbd></button>
        <button class="icon-btn" data-toggle-theme title="Toggle theme" aria-label="Toggle theme">
            <i data-lucide="moon" class="size-4 dark:hidden"></i>
            <i data-lucide="sun" class="hidden size-4 dark:block"></i>
        </button>
    </div>
</header>

@include('partials.menu-seach')