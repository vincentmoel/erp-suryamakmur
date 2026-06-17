@php
    use Illuminate\Support\Facades\Route;

    $separator = '<li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" class="[&>svg]:size-3.5"><svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></li>';

    $linkItem = fn(string $label, string $href) =>
        '<li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5">
            <a data-slot="breadcrumb-link" class="transition-colors hover:text-foreground" href="' . e($href) . '">' . e($label) . '</a>
        </li>';

    $activeItem = fn(string $label) =>
        '<li data-slot="breadcrumb-item" class="inline-flex items-center gap-1.5">
            <span data-slot="breadcrumb-page" role="link" aria-disabled="true" aria-current="page" class="font-normal text-foreground">' . e($label) . '</span>
        </li>';

    $homeLink = $linkItem('Home', url('/'));

    // --- Custom breadcrumb array ---
    if (!empty($breadcrumb) && is_array($breadcrumb)) {
        $items = [$homeLink];
        foreach ($breadcrumb as $index => $crumb) {
            $isLast = $index === array_key_last($breadcrumb);
            $items[] = $separator;
            $items[] = $isLast || empty($crumb['route'])
                ? $activeItem($crumb['name'])
                : $linkItem($crumb['name'], $crumb['route']);
        }
        $breadcrumbHtml = implode('', $items);
    } else {
        // --- Auto-detect from route name ---
        $routeName   = Route::currentRouteName() ?? '';
        $segments    = explode('.', $routeName);
        $resource    = $segments[0] ?? '';
        $action      = $segments[1] ?? 'index';

        // Find menu title from Module enum
        $menuTitle  = null;
        $indexRoute = null;
        foreach (\App\Enums\Module::cases() as $mod) {
            if ($mod->route() === null) continue;
            $r = explode('.', $mod->route())[0] ?? '';
            if ($r === $resource) {
                $menuTitle  = $mod->label();
                $indexRoute = Route::has($resource . '.index') ? route($resource . '.index') : null;
                break;
            }
        }

        $fallbackTitle = $menuTitle ?? ucfirst($resource);

        $items = [$homeLink];

        if ($action === 'index' || $menuTitle === null) {
            $items[] = $separator;
            $items[] = $activeItem($fallbackTitle);
        } elseif ($action === 'create') {
            $items[] = $separator;
            $items[] = $linkItem($fallbackTitle, $indexRoute);
            $items[] = $separator;
            $items[] = $activeItem(__('general.breadcrumb_add') . ' ' . $fallbackTitle);
        } elseif ($action === 'edit') {
            $items[] = $separator;
            $items[] = $linkItem($fallbackTitle, $indexRoute);
            $items[] = $separator;
            $items[] = $activeItem(__('general.breadcrumb_edit') . ' ' . $fallbackTitle);
        } else {
            // show / view / or any other action
            $pageTitle = $title ?? (__('general.breadcrumb_view') . ' ' . $fallbackTitle);
            $items[] = $separator;
            $items[] = $linkItem($fallbackTitle, $indexRoute);
            $items[] = $separator;
            $items[] = $activeItem($pageTitle);
        }

        $breadcrumbHtml = implode('', $items);
    }
@endphp

<nav aria-label="breadcrumb" data-slot="breadcrumb" class="hidden sm:block">
    <ol data-slot="breadcrumb-list"
        class="flex flex-wrap items-center gap-1.5 break-words text-sm text-muted-foreground sm:gap-2.5">
        {!! $breadcrumbHtml !!}
    </ol>
</nav>
