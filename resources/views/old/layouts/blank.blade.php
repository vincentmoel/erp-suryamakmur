<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ isset($title) ? "$title | " : '' }} {{ env('APP_NAME') }}</title>

    @if (App::environment('production'))
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="title" content="Sekolah Bhakti Tunas Harapan">
    <meta name="description" content="Trilingual National School" />
    <meta name="author" content="Vincent Nathaniel Moeljopranoto" />
    <meta name="keywords" content="School, Trilingual National School, Chinese, Magelang" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="language" content="English">

    <link rel="shortcut icon" type="image/png" href="{{ asset('src/images/logos/traffic-light.png') }}" />
    <link id="themeColors" rel="stylesheet" href="{{ asset('src/css/style.css?' . time()) }}" />

    <link rel="stylesheet" href="{{ asset('src/libs/sweetalert2/dist/sweetalert2.min.css') }}">

</head>

<body>
    <div class="preloader">
        <img src="{{ asset('src/images/logos/traffic-light.png') }}" alt="loader" class="lds-ripple img-fluid" style="min-width: 100px"/>
    </div>

    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        @yield('content')
    </div>

    <!--  Import Js Files -->
    <script src="{{ asset('src/libs/jquery/dist/jquery.min.js?' . time()) }}"></script>
    <script src="{{ asset('src/libs/simplebar/dist/simplebar.min.js?' . time()) }}"></script>
    <script src="{{ asset('src/libs/bootstrap/dist/js/bootstrap.bundle.min.js?' . time()) }}"></script>

    <!--  core files -->
    <script src="{{ asset('src/js/app.min.js?' . time()) }}"></script>
    <script src="{{ asset('src/js/app.init.js?' . time()) }}"></script>
    <script src="{{ asset('src/js/app-style-switcher.js?' . time()) }}"></script>
    <script src="{{ asset('src/js/sidebarmenu.js?' . time()) }}"></script>

    <script src="{{ asset('src/js/custom.js?' . time()) }}"></script>

    <script src="{{ asset('src/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {


            @if (Session::has('error'))
                Swal.fire({
                    icon: 'error',
                    title: "Error Code: {{ Session::get('error.code') }}",
                    text: "{{ Session::get('error.message') }}",
                    confirmButtonColor: '#5d87ff',
                })
            @endif

            $('form').submit(function() {
                $(this).find(':button[type=submit]').prop('disabled', true);
                var buttonText = $(this).find(':button[type=submit]').html();
                $(this).find(':button[type=submit]').html(
                    '<span class="me-1 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>' +
                    buttonText);
            });
        });

        function logout() {
            document.logoutForm.submit();
        }
    </script>
</body>

</html>
