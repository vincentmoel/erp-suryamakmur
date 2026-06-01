<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-center" style="padding: 0px 18px;">
            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                <img src="{{ asset('src/images/logos/logo.webp') }}" class="dark-logo"
                    style="width: 180px; margin-top: 20px;" />
                <img src="{{ asset('src/images/logos/light-logo.svg') }}" class="light-logo" width="180" />
            </a>
            <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8 text-muted"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar simplebar-scrollable-y" data-simplebar>
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>

                @if (Session::get('permissions')['Dashboard']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['StationMonitoring']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('station-monitoring.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-device-gamepad"></i>
                            </span>
                            <span class="hide-menu">Station Monitoring</span>
                        </a>
                    </li>
                @endif



                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Administration</span>
                </li>


                @if (Session::get('permissions')['Invoice']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('invoices.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-file-invoice"></i>
                            </span>
                            <span class="hide-menu">Invoice</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['Item']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('items.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-shopping-bag"></i>
                            </span>
                            <span class="hide-menu">Item</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['Customer']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('customers.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="hide-menu">Customer</span>
                        </a>
                    </li>
                @endif

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Inventory</span>
                </li>


                @if (Session::get('permissions')['StockOpname']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('stock-opnames.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-building-warehouse"></i>
                            </span>
                            <span class="hide-menu">Stock Opname</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['InventoryLog']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('inventory-logs.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-stack-2"></i>
                            </span>
                            <span class="hide-menu">Inventory Log</span>
                        </a>
                    </li>
                @endif

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Master Data</span>
                </li>

                @if (Session::get('permissions')['StationCategory']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('station-categories.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-brand-apple-arcade"></i>
                            </span>
                            <span class="hide-menu">Station Category</span>
                        </a>
                    </li>
                @endif


                @if (Session::get('permissions')['ItemCategory']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('item-categories.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-box"></i>
                            </span>
                            <span class="hide-menu">Item Category</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['IpAddress']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('ip-addresses.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-device-desktop"></i>
                            </span>
                            <span class="hide-menu">IP Address</span>
                        </a>
                    </li>
                @endif


                @if (Session::get('permissions')['Duration']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('durations.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-clock-2"></i>
                            </span>
                            <span class="hide-menu">Duration</span>
                        </a>
                    </li>
                @endif


                @if (Session::get('permissions')['RentalStation']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('rental-stations.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-device-gamepad-2"></i>
                            </span>
                            <span class="hide-menu">Rental Station</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['Discount']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('discounts.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-discount"></i>
                            </span>
                            <span class="hide-menu">Discount</span>
                        </a>
                    </li>
                @endif


                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">User Management</span>
                </li>

                @if (Session::get('permissions')['Role']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('roles.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-lock-access"></i>
                            </span>
                            <span class="hide-menu">Role</span>
                        </a>
                    </li>
                @endif

                @if (Session::get('permissions')['User']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('users.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-users"></i>
                            </span>
                            <span class="hide-menu">User</span>
                        </a>
                    </li>
                @endif


                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Setting</span>
                </li>

                @if (Session::get('permissions')['Config']['menu'] ?? false)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('configs.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-settings"></i>
                            </span>
                            <span class="hide-menu">Config</span>
                        </a>
                    </li>
                @endif

            </ul>

        </nav>

        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <div class="john-img">
                    @if (auth()->user()->profile == null)
                        <img src="{{ asset('src/images/profile/default-profile.jpg') }}" class="rounded-circle"
                            width="40" height="40">
                    @else
                        <img src="{{ asset(auth()->user()->profile) }}" class="rounded-circle" width="40"
                            height="40">
                    @endif
                </div>
                <div class="john-title">
                    <span class="mb-0 fw-semibold">{{ Str::limit(auth()->user()->name, 18, '') }}</span>
                    <br>
                    <span class="fs-2 text-dark role-container"></span>
                </div>

            </div>
        </div>


        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
