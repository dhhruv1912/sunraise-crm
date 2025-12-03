<nav id="fixed-navbar" class="navbar navbar-expand-md navbar-dark fixed-top">
    <div class="container-fluid">

        <!-- Brand -->
        <div class="app-brand demo">
            <a href="{{ url('/') }}" class="app-brand-link">
                <span class="app-brand-logo demo me-1">
                    <span style="color: var(--bs-primary)">
                        {{-- @include('temp.logo') Move SVG to separate file --}}
                    </span>
                </span>
                <span class="app-brand-text demo menu-text fw-semibold ms-2">Materio</span>
            </a>

            {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="mdi menu-toggle-icon d-xl-block align-middle mdi-20px"></i>
            </a> --}}
            {{-- <button id="mobile-menu-toggle" class="btn btn-sm btn-primary d-lg-none">
                <i class="tf-icons bx bx-menu"></i>
            </button> --}}
        </div>

        <!-- Mobile toggle -->
        <button id="mobile-menu-toggle" class="btn btn-sm btn-primary d-lg-none navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse" aria-controls="navbarCollapse"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="color: var(--bs-primary)"></span>
        </button>

        <!-- Right Side -->
        <ul class="navbar-nav flex-row align-items-center ms-auto w-100 row d-md-flex d-none">

            {{-- Extra buttons from child page --}}
            <div class="col-2">
                <div class="position-relative">
                    {{-- <input type="text" id="globalSearch" class="form-control" placeholder="Search customer / project / lead">
                    <div id="globalSearchResults" class="search-results"></div> --}}
                    <div class="global-search-box">
                        <input id="globalSearchInput" class="form-control" placeholder="Search customer, lead, project...">
                        <div id="globalSearchResults" class="search-dropdown"></div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mx-2 col justify-content-end">
                @yield('headbar')
                <li class="nav-item navbar-dropdown dropdown-user dropdown ms-3 justify-content-end d-flex">
                    <a class="nav-link dropdown-toggle hide-arrow p-0"
                       href="#" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ asset('assets/img/avatars/1.png') }}"
                                 alt="Avatar"
                                 class="w-px-40 h-auto rounded-circle">
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end w-20 mt-3 py-2">

                        <li>
                            <a class="dropdown-item pb-2 mb-1" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-online me-2">
                                        <img src="{{ asset('assets/img/avatars/1.png') }}"
                                             class="w-px-40 h-auto rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">
                                            {{ session('staff.fname') }} {{ session('staff.lname') }}
                                        </h6>
                                        <small class="text-muted">{{ session('staff.role') }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <li><div class="dropdown-divider my-1"></div></li>

                        <li>
                            <a class="dropdown-item" href="">{{-- {{ route('Profile') }} --}}
                                <i class="mdi mdi-account-outline me-1 mdi-20px"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="mdi mdi-cog-outline me-1 mdi-20px"></i>
                                <span>Settings</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex" href="#">
                                <i class="mdi mdi-credit-card-outline me-1 mdi-20px"></i>
                                <span class="d-flex align-items-center">
                                    Billing
                                    <span class="badge badge-sm bg-danger ms-auto">4</span>
                                </span>
                            </a>
                        </li>

                        <li><div class="dropdown-divider my-1"></div></li>

                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="mdi mdi-power me-1 mdi-20px"></i>
                                <span>Log Out</span>
                            </a>
                        </li>

                    </ul>
                </li>
            </div>

            <!-- User Dropdown -->

        </ul>
    </div>
</nav>
