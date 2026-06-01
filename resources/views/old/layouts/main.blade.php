<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ isset($title) ? "$title | " : '' }} {{ env('APP_NAME') }}</title>

    @if (App::environment('production'))
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="title" content="Street Game Station">
    <meta name="description" content="Street Game Station" />
    <meta name="author" content="Vincent Nathaniel Moeljopranoto" />
    <meta name="keywords" content="Game, Game Station, Play Station, Semarang, Games" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="language" content="English">

    <!-- --------------------------------------------------- -->
    <!-- Select2 -->
    <!-- --------------------------------------------------- -->
    <link rel="stylesheet" href="{{ asset('src/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/css/custom-select2-style.css') }}">


    <link rel="stylesheet"
        href="{{ asset('src/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css?' . date('H:i:s')) }}">
    @yield('css')
    @stack('style')

    <!-- --------------------------------------------------- -->
    <!-- Favicon -->
    <!-- --------------------------------------------------- -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('src/images/logos/traffic-light.png') }}" />
    <link id="themeColors" rel="stylesheet" href="{{ asset('src/css/style.css?' . date('H:i:s')) }}" />
    {{-- <link  id="themeColors"  rel="stylesheet" href="{{ asset('src/css/style.min.css') }}" /> --}}


    <link rel="stylesheet" href="{{ asset('src/libs/prismjs/themes/prism-okaidia.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/libs/sweetalert2/dist/sweetalert2.min.css') }}">

    <link rel="stylesheet" href="{{ asset('src/css/custom-style.css') }}">

</head>

<body>
    <div class="preloader">
        <img src="{{ asset('src/images/logos/traffic-light.png') }}" alt="loader" class="lds-ripple img-fluid"
            style="min-width: 100px" />
    </div>

    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        @include('partials.sidebar')
        <div class="body-wrapper">

            @include('partials.header')

            <div class="container-fluid mw-100">
                @if ($useBreadcrumb ?? true)
                    @include('partials.breadcrumb')
                @endif

                @yield('content')
            </div>
        </div>

        <div class="dark-transparent sidebartoggler"></div>
        <div class="dark-transparent sidebartoggler"></div>
    </div>

    <script src="{{ asset('src/libs/jquery/dist/jquery.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/libs/jquery-ui/dist/jquery-ui.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/libs/simplebar/dist/simplebar.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/libs/bootstrap/dist/js/bootstrap.bundle.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <!-- ---------------------------------------------- -->
    <!-- core files -->
    <!-- ---------------------------------------------- -->
    <script src="{{ asset('src/js/app.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/js/app.init.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/js/app-style-switcher.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/js/sidebarmenu.js?' . date('Y-m-d H:i:s')) }}"></script>

    <script src="{{ asset('src/js/custom.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/libs/prismjs/prism.js?' . date('Y-m-d H:i:s')) }}"></script>

    <script src="{{ asset('src/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <!-- ---------------------------------------------- -->
    <!-- current page js files -->
    <!-- ---------------------------------------------- -->

    <script src="{{ asset('src/js/plugins/toastr-init.js') }}"></script>

    <script>
        $('form').submit(function() {
            $(this).find(':button[type=submit]').prop('disabled', true);
            var buttonText = $(this).find(':button[type=submit]').html();
            $(this).find(':button[type=submit]').html(
                '<span class="me-1 spinner-border spinner-border-sm loading-spinner" role="status" aria-hidden="true"></span>' +
                buttonText);
        });

        $(document).ready(function() {
            @if (Session::has('error_import'))
                toastr.error(
                    "{{ Session::get('error_import.message') }}",
                    "{{ Session::get('error_import.title') }}", {
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                        timeOut: 15000,
                        positionClass: "toastr toast-top-center mt-3",
                        containerId: "toast-top-center"
                    }
                );
            @endif

            @if (Session::has('success'))
                toastr.success(
                    "{{ Session::get('success.message') }}",
                    "{{ Session::get('success.title') }}", {
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                        timeOut: 4000,
                        positionClass: "toastr toast-top-center mt-3",
                        containerId: "toast-top-center"
                    }
                );
            @endif

            @if (Session::has('error'))
                Swal.fire({
                    icon: 'error',
                    title: "Error Code: {{ Session::get('error.code') }}",
                    text: "{{ Session::get('error.message') }}",
                    confirmButtonColor: '#5d87ff',
                })
            @endif

            @if (Session::has('errorDataExists'))
                Swal.fire({
                    title: 'Data Already Exists',
                    text: "Do you want to edit data instead of adding data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Sure, take me to the edit page",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ Session::get('errorDataExists')['routeEdit'] }}";
                    }
                });
            @endif

        })
    </script>

    <!-- ---------------------------------------------- -->
    <!-- Select2 -->
    <!-- ---------------------------------------------- -->
    <script src="{{ asset('src/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('src/libs/select2/dist/js/select2.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('.nav-small-cap').each(function() {
                var nextElem = $(this).nextUntil('.nav-small-cap');
                var visibleLi = nextElem.filter('li:visible');

                if (!visibleLi.length) {
                    $(this).hide();
                }
            });
        });

        $(document).ready(function() {
            const roleContainers = $('.role-container');
            const roles = @json(auth()->user()->roles()->pluck('name')->toArray());
            let index = 0;

            function showNextRole() {
                roleContainers.each(function() {
                    const $this = $(this);
                    if (roles.length === 1) {
                        $this.text(roles[index]);
                    } else {
                        if (index < roles.length) {
                            $this.fadeOut(function() {
                                $(this).text(roles[index]).fadeIn();
                            });
                        } else {
                            index = 0;
                            $this.fadeOut(function() {
                                $(this).text(roles[index]).fadeIn();
                            });
                        }
                    }
                });
                index++;
                if (index >= roles.length) {
                    index = 0;
                }
            }

            // Mulai transisi
            showNextRole();
            setInterval(showNextRole, 2000);

            $('.btn-open-cash-drawer-header').on('click', function() {
                toggleLoadingToButton("btn-open-cash-drawer-header", $('#btn-open-cash-drawer-header').html(), 'add');

                $.ajax({
                    url: "{{ route('station-monitoring.ajax.openCashDrawer') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showToast('success', 'Success',
                                response.message);
                            toggleLoadingToButton("btn-open-cash-drawer-header", $(
                                    '#btn-open-cash-drawer-header')
                                .html(), 'remove');

                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to open cash drawer',
                                'error'
                            );
                            toggleLoadingToButton("btn-open-cash-drawer-header", $(
                                    '#btn-open-cash-drawer-header')
                                .html(), 'remove');

                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Error: ' + xhr.statusText,
                            'error'
                        );
                    }
                });
            });
        });
    </script>

    <script src="{{ asset('src/libs/datatables.net/js/jquery.dataTables.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/js/custom-script.js?' . now()) }}"></script>
    @yield('script')
    @stack('stack-script')
</body>

</html>
