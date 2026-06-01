@php
    use Illuminate\Support\Facades\Route;

    $separator = '<li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" class="[&>svg]:size-3.5"><i data-lucide="chevron-right" class="size-3.5"></i></li>';

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

        // Find menu title from sidebar config
        $menuTitle   = null;
        $indexRoute  = null;
        foreach (config('sidebar', []) as $item) {
            if (!empty($item['route'])) {
                $r = explode('.', $item['route'])[0] ?? '';
                if ($r === $resource) {
                    $menuTitle  = $item['title'];
                    $indexRoute = Route::has($resource . '.index') ? route($resource . '.index') : null;
                    break;
                }
            }
            foreach ($item['children'] ?? [] as $child) {
                if (!empty($child['route'])) {
                    $r = explode('.', $child['route'])[0] ?? '';
                    if ($r === $resource) {
                        $menuTitle  = $child['title'];
                        $indexRoute = Route::has($resource . '.index') ? route($resource . '.index') : null;
                        break 2;
                    }
                }
            }
        }

        $fallbackTitle = $menuTitle ?? ucfirst($resource);

        $items = [$homeLink];

        if ($action === 'index' || !$menuTitle) {
            $items[] = $separator;
            $items[] = $activeItem($fallbackTitle);
        } elseif ($action === 'create') {
            $items[] = $separator;
            $items[] = $linkItem($fallbackTitle, $indexRoute);
            $items[] = $separator;
            $items[] = $activeItem('Add ' . $fallbackTitle);
        } elseif ($action === 'edit') {
            $items[] = $separator;
            $items[] = $linkItem($fallbackTitle, $indexRoute);
            $items[] = $separator;
            $items[] = $activeItem('Edit ' . $fallbackTitle);
        } else {
            // show / view / or any other action
            $pageTitle = $title ?? ('View ' . $fallbackTitle);
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
