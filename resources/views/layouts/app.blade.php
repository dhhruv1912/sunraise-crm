<!DOCTYPE html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ $title ?? 'Authentication' }} - Sunraise CRM</title>

    <meta name="description" content="" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script src="{{ asset('/assets/js/permission/location.js') }}"></script>
</head>

<body class="min-h-screen flex items-center justify-center">
    <div class="loader-img position-absolute w-100 h-100" style="z-index: 9999;">
        <img src="{{ asset('assets/img/loader/page.gif') }}" alt="" class="w-100 h-100"
            style="object-fit: cover;opacity: 0.7;">
    </div>
    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
        <div class="position-relative">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-4">
                    <!-- Login -->
                    <div class="card p-2">
                        @if (session('success'))
                            <div class="mb-4 p-3 bg-green-100 text-green-800 text-sm rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Error Alert (first error only) --}}
                        @if ($errors->any())
                            <div class="mb-4 p-3 bg-red-100 text-red-800 text-sm rounded">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{-- MAIN CONTENT --}}
                        <div>
                            @yield('content')
                        </div>
                    </div>
                    <!-- /Login -->
                    <img src="{{ asset('assets/img/illustrations/tree-3.png') }}" alt="auth-tree"
                        class="authentication-image-object-left d-none d-lg-block" />
                    <img src="{{ asset('assets/img/illustrations/auth-basic-mask-light.png') }}"
                        class="authentication-image d-none d-lg-block" alt="triangle-bg"
                        data-app-light-img="illustrations/auth-basic-mask-light.png') }}"
                        data-app-dark-img="illustrations/auth-basic-mask-dark.png') }}" />
                    <img src="{{ asset('assets/img/illustrations/tree.png') }}" alt="auth-tree"
                        class="authentication-image-object-right d-none d-lg-block" />
                </div>
            </div>
            <div class="mt-8 text-center text-sm text-gray-500 position-absolute bottom-0 m-auto w-100">
                © {{ date('Y') }} Sunraise CRM. All Rights Reserved.
            </div>
        </div>

        {{-- Footer --}}

    </div>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/page/login.js') }}"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
