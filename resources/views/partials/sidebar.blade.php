    <aside
        class="app-sidebar fixed inset-y-0 left-0 z-40 flex h-svh w-64 -translate-x-full flex-col border-r bg-sidebar text-sidebar-foreground transition-[transform,width] duration-200 ease-linear lg:translate-x-0">
        <div data-sidebar="header" class="flex items-center p-4">
            {{-- Full logo: light mode --}}
            <img src="{{ asset('src/img/logo-dark.png') }}" alt="Logo" class="h-10 w-auto dark:hidden sidebar-full-logo">
            {{-- Full logo: dark mode --}}
            <img src="{{ asset('src/img/logo-light.png') }}" alt="Logo" class="h-10 w-auto hidden dark:block sidebar-full-logo">
            {{-- Mini logo: collapsed sidebar, light mode --}}
            <img src="{{ asset('src/img/logo-mini-dark.png') }}" alt="Logo" class="size-10 object-contain hidden dark:hidden sidebar-mini-logo">
            {{-- Mini logo: collapsed sidebar, dark mode --}}
            <img src="{{ asset('src/img/logo-mini-light.png') }}" alt="Logo" class="size-10 object-contain hidden dark:hidden sidebar-mini-logo-dark">
        </div>
        @php
            use App\Enums\Module;

            $isSuperAdmin   = auth()->user()->roles->contains('id', 1);
            $grantedModules = $isSuperAdmin ? null : \App\Models\Permission::whereIn('role_id', auth()->user()->roles->pluck('id'))
                ->where('action', 'read')
                ->pluck('module')
                ->unique()
                ->flip()
                ->toArray();

            // Build sidebar groups from Module enum — single source of truth.
            $sidebarGroups = [];
            $currentGroup  = '__none__';
            foreach (Module::cases() as $mod) {
                if ($mod->route() === null) continue;
                if (!$isSuperAdmin && !isset($grantedModules[$mod->value])) continue;

                $g = $mod->group() ?? '';
                if ($g !== $currentGroup) {
                    $sidebarGroups[] = ['group' => $mod->group(), 'items' => []];
                    $currentGroup    = $g;
                }
                $sidebarGroups[count($sidebarGroups) - 1]['items'][] = $mod;
            }
        @endphp

        <nav data-sidebar="content" class="flex min-h-0 flex-1 flex-col gap-2 overflow-auto">
            @foreach ($sidebarGroups as $grp)
                <div data-sidebar="group" class="relative flex w-full min-w-0 flex-col p-2">
                    @if ($grp['group'])
                        <div data-sidebar="group-label"
                            class="sidebar-label flex h-8 shrink-0 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70">
                            {{ $grp['group'] }}
                        </div>
                    @endif

                    <div data-sidebar="group-content" class="w-full text-sm">
                        <ul data-sidebar="menu" class="flex w-full min-w-0 flex-col gap-1">
                            @foreach ($grp['items'] as $mod)
                                @php
                                    $routeName   = $mod->route();
                                    $routePrefix = str_contains($routeName, '.')
                                        ? substr($routeName, 0, strrpos($routeName, '.'))
                                        : $routeName;
                                    $isActive = request()->routeIs($routeName)
                                        || ($routePrefix !== $routeName && request()->routeIs($routePrefix . '.*'));
                                @endphp
                                <li data-sidebar="menu-item" class="group/menu-item relative">
                                    <a class="nav-link {{ $isActive ? 'active' : '' }}"
                                        href="{{ route($routeName) }}">
                                        <x-icon :name="$mod->icon()" class="size-4" />
                                        <span class="sidebar-text">{{ $mod->label() }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </nav>
        <div data-sidebar="footer" class="flex flex-col gap-2 border-t p-2">
            <div data-dropdown class="relative">
                <button data-dropdown-trigger class="nav-link sidebar-user-btn h-12 w-full">
                    @php $authUser = auth()->user(); @endphp
                    <img src="{{ $authUser->photo ? asset('storage/' . $authUser->photo) : asset('src/img/default-profile.jpg') }}" class="size-8 shrink-0 rounded-full object-cover" alt="Profile">
                    <x-sidebar-user-info />
                    <x-icon name="more-horizontal" class="ml-auto size-4 opacity-60 nav-chevron" />
                </button>
                <div data-dropdown-menu class="hidden w-56 rounded-md border bg-popover p-1 text-popover-foreground shadow-md" style="position:absolute; bottom:calc(100% + 4px); left:0; z-index:50;">
                    <div class="px-2 py-1.5">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-muted-foreground truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="my-1 h-px bg-border"></div>
                    <a href="#" class="flex items-center gap-2 rounded-sm px-2 py-2 text-sm hover:bg-accent transition-colors">
                        <x-icon name="profile" class="size-4 opacity-70" />
                        <span>@lang('general.profile')</span>
                    </a>
                    <div class="my-1 h-px bg-border"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-sm px-2 py-2 text-sm text-destructive hover:bg-accent transition-colors cursor-pointer">
                            <x-icon name="logout" class="size-4" />
                            <span>@lang('general.logout')</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
