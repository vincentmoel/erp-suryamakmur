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
        <nav data-sidebar="content" class="flex min-h-0 flex-1 flex-col gap-2 overflow-auto">
            @foreach (config('sidebar') as $item)
                <div data-sidebar="group" class="relative flex w-full min-w-0 flex-col p-2">
                    @if (!empty($item['group']))
                        <div data-sidebar="group-label"
                            class="sidebar-label flex h-8 shrink-0 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70">
                            {{ $item['group'] }}
                        </div>
                    @endif

                    <div data-sidebar="group-content" class="w-full text-sm">
                        <ul data-sidebar="menu" class="flex w-full min-w-0 flex-col gap-1">

                            @if (!empty($item['children']) && empty($item['title']) && array_is_list($item['children']))
                                @foreach ($item['children'] as $flatItem)
                                    <li data-sidebar="menu-item" class="group/menu-item relative">
                                        <a class="nav-link {{ request()->routeIs($flatItem['route'] ?? '') ? 'active' : '' }}"
                                            href="{{ !empty($flatItem['route']) ? route($flatItem['route']) : ($flatItem['url'] ?? '#') }}">
                                            <x-icon :name="$flatItem['icon']" class="size-4" />
                                            <span class="sidebar-text">{{ $flatItem['title'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            @elseif (!empty($item['children']) && !array_is_list($item['children']))
                                @php
                                    $dropdownTitle = $item['title'] ?? $item['children']['title'] ?? '';
                                    $dropdownIcon  = $item['icon']  ?? $item['children']['icon']  ?? 'circle';
                                    $dropdownSubs  = $item['children']['children'] ?? [];
                                @endphp
                                <li data-nav-parent data-sidebar="menu-item" class="group/menu-item relative">
                                    <button data-nav-toggle aria-expanded="false" class="nav-link" type="button">
                                        <x-icon :name="$dropdownIcon" class="size-4" />
                                        <span class="sidebar-text">{{ $dropdownTitle }}</span>
                                        <x-icon name="chevron-right" class="nav-chevron" />
                                    </button>
                                    <ul data-nav-sub data-sidebar="menu-sub" class="nav-sub hidden">
                                        @foreach ($dropdownSubs as $child)
                                            <li data-sidebar="menu-sub-item" class="group/menu-sub-item relative">
                                                <a class="nav-sub-link"
                                                    href="{{ !empty($child['route']) ? route($child['route']) : ($child['url'] ?? '#') }}">
                                                    <span>{{ $child['title'] }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li data-sidebar="menu-item" class="group/menu-item relative">
                                    <a class="nav-link {{ request()->routeIs($item['route'] ?? '') ? 'active' : '' }}"
                                        href="{{ !empty($item['route']) ? route($item['route']) : ($item['url'] ?? '#') }}">
                                        <x-icon :name="$item['icon']" class="size-4" />
                                        <span class="sidebar-text">{{ $item['title'] }}</span>
                                    </a>
                                </li>
                            @endif

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
                        <span>Profile</span>
                    </a>
                    <div class="my-1 h-px bg-border"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-sm px-2 py-2 text-sm text-destructive hover:bg-accent transition-colors cursor-pointer">
                            <x-icon name="logout" class="size-4" />
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
