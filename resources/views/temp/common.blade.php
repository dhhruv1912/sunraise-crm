<!DOCTYPE html>
<html lang="en"
      class="light-style layout-menu-fixed layout-compact"
      dir="ltr"
      data-theme="theme-default"
      data-assets-path="{{ asset('assets/') }}"
      data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no" />

    <title>@yield('title') | Sahwa</title>

    <meta name="description" content="">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base_url" content="{{ url('/') }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet"
      href="{{ session('active_company') == 'arham' ? '/assets/css/theme-ar.css' : '/assets/css/theme-sr.css' }}"
      id="theme-css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">

    @yield('head')
    <!-- Scrollbar Style -->
    <style>
        ::-webkit-scrollbar { width: 7.5px; height: 7.5px; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #9055fd 0%, #c4a5fe 100%);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #9055fd 0%, #c4a5fe 100%);
        }
        .floating-btn { position: fixed; bottom: 3rem; right: 1.5rem; z-index: 1080; }
        .shadow-danger { box-shadow: 0 1px 20px 1px #ea5455 !important; }
        .loader-img { z-index: 9999; }
    </style>

    <!-- Helpers & Config -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>

    <!-- Loader -->
    <div class="loader-img position-absolute w-100 h-100">
        <img src="{{ asset('assets/img/logo/page.gif') }}"
             class="w-100 h-100"
             style="object-fit: cover; opacity: 0.7;">
    </div>

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            @include('temp/menu')

            <!-- Layout page -->
            <div class="layout-page">

                @include('temp/header')

                <div class="content-wrapper">

                    <div class="container-fluid flex-grow-1">
                        @yield('content')
                    </div>
                    @include('temp/footer')
                </div>

            </div>
        </div>

        <!-- Menu overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

    <!-- Permissions JS -->
    {{-- <script src="{{ asset('assets/js/permission/location.js') }}"></script> --}}

    <!-- Layout JS: moved all inline JS here -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    @yield('scripts')

</body>
</html>
