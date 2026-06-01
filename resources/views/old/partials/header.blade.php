<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse"
                    href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>
   
        <div class="d-block d-lg-none">
            <img src="{{ asset('src/images/logos/logo-small.png') }}" width="120" alt="" />
        </div>

        
        <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="p-2">
                <i class="ti ti-dots fs-7"></i>
            </span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <div class="d-flex align-items-center justify-content-between">
                <button type="button" id="btn-open-cash-drawer-header"
                    class="btn btn-open-cash-drawer-header w-100 d-flex align-items-center justify-content-center" style="background-color:#168118; color:white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash me-1">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                        <path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    </svg>
                    Open Cash Drawer
                </button>
                <a href="javascript:void(0)"
                    class="nav-link d-flex d-lg-none align-items-center justify-content-center"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                    aria-controls="offcanvasWithBothOptions">
                    <i class="ti ti-align-justified fs-7"></i>
                </a>
                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link pe-0" href="javascript:void(0)" id="drop1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="user-profile-img">
                                    <img src="{{ asset('src/images/profile/default-profile.jpg') }}"
                                        class="rounded-circle" width="35" height="35"
                                        alt="" />
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                            aria-labelledby="drop1">
                            <div class="profile-dropdown position-relative" data-simplebar>
                                {{-- <div class="py-3 px-7 pb-0">
                                    <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                </div> --}}
                                <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                    <img src="{{ asset('src/images/profile/default-profile.jpg') }}" class="rounded-circle"
                                        width="80" height="80" alt="" />
                                    <div class="ms-3">
                                        <h5 class="mb-1 fs-3">{{ auth()->user()->name }}</h5>
                                        <span class="mb-1 d-block text-dark role-container">Designer</span>
                                        <p class="mb-0 d-flex text-dark align-items-center gap-2"><i
                                                class="ti ti-user fs-4"></i> {{ auth()->user()->username }}</p>
                                    </div>
                                </div>
                                {{-- <div class="message-body">
                                    <a href="./page-user-profile.html"
                                        class="py-8 px-7 mt-8 d-flex align-items-center">
                                        <span
                                            class="d-flex align-items-center justify-content-center bg-light rounded-1 p-6">
                                            <img src="{{ asset('src/images/svgs/icon-account.svg') }}"
                                                alt="" width="24" height="24" />
                                        </span>
                                        <div class="w-75 d-inline-block v-middle ps-3">
                                            <h6 class="mb-1 bg-hover-primary fw-semibold">
                                                My Profile
                                            </h6>
                                            <span class="d-block text-dark">Account Settings</span>
                                        </div>
                                    </a>
                                
                                </div> --}}
                                <div class="d-grid py-4 px-7 pt-8" >
                                    <form action="{{ route('logout') }}" method="POST" >
                                        @csrf
                                        <button class="btn btn-outline-primary w-100" >Log Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>