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
            <div data-sidebar="group" class="relative flex w-full min-w-0 flex-col p-2">

                <div data-sidebar="group-content" class="w-full text-sm">
                    <ul data-sidebar="menu" class="flex w-full min-w-0 flex-col gap-1">
                        <li data-sidebar="menu-item" class="group/menu-item relative">
                            <a class="nav-link active" href="/" data-active="true">
                                <i data-lucide="chart-no-axes-combined" class="size-4"></i>
                                <span class="sidebar-text">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
          
            <div data-sidebar="group" class="relative flex w-full min-w-0 flex-col p-2">

                <div data-sidebar="group-content" class="w-full text-sm">
                    <ul data-sidebar="menu" class="flex w-full min-w-0 flex-col gap-1">
                        <li data-sidebar="menu-item" class="group/menu-item relative">
                            <a class="nav-link" href="{{ route('users.index') }}" data-active="true">
                                <i data-lucide="users" class="size-4"></i>
                                <span class="sidebar-text">Users</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
          
            <div data-sidebar="group" class="relative flex w-full min-w-0 flex-col p-2">
                <div data-sidebar="group-label"
                    class="sidebar-label flex h-8 shrink-0 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70">
                    E-Commerce</div>
                <div data-sidebar="group-content" class="w-full text-sm">
                    <ul data-sidebar="menu" class="flex w-full min-w-0 flex-col gap-1">
                        <li data-nav-parent data-sidebar="menu-item" class="group/menu-item relative">
                            <button data-nav-toggle aria-expanded="false" class="nav-link" type="button">
                                <i data-lucide="shopping-cart" class="size-4"></i>
                                <span class="sidebar-text">E-Commerce</span>
                                <i data-lucide="chevron-right" class="nav-chevron"></i>
                            </button>
                            <ul data-nav-sub data-sidebar="menu-sub" class="nav-sub hidden">
                                <li data-sidebar="menu-sub-item" class="group/menu-sub-item relative"><a
                                        class="nav-sub-link "
                                        href="./pages/ecommerce/dashboard.html"><span>Dashboard</span></a></li>
                                <li data-sidebar="menu-sub-item" class="group/menu-sub-item relative"><a
                                        class="nav-sub-link "
                                        href="./pages/ecommerce/products.html"><span>Products</span></a></li>
                                <li data-sidebar="menu-sub-item" class="group/menu-sub-item relative"><a
                                        class="nav-sub-link "
                                        href="./pages/ecommerce/orders.html"><span>Orders</span></a></li>
                                <li data-sidebar="menu-sub-item" class="group/menu-sub-item relative"><a
                                        class="nav-sub-link "
                                        href="./pages/ecommerce/customers.html"><span>Customers</span></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div data-sidebar="footer" class="flex flex-col gap-2 border-t p-2">
            <div data-dropdown class="relative">
                <button data-dropdown-trigger class="nav-link sidebar-user-btn h-12 w-full">
                    <img src="{{ asset('src/img/default-profile.jpg') }}" class="size-8 shrink-0 rounded-full object-cover" alt="Profile">
                    <div class="sidebar-profile-meta grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                        <span class="text-muted-foreground truncate text-xs">fatmuh@moccilabs.com</span>
                    </div>
                    <x-icon name="more-horizontal" class="ml-auto size-4 opacity-60 nav-chevron" />
                </button>
                <div data-dropdown-menu class="hidden w-56 rounded-md border bg-popover p-1 text-popover-foreground shadow-md" style="position:absolute; bottom:calc(100% + 4px); left:0; z-index:50;">
                    <div class="px-2 py-1.5">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-muted-foreground truncate">fatmuh@moccilabs.com</p>
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
