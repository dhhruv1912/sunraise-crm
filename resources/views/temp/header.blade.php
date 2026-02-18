<header class="sr-header shadow-sm">
    <div class="sr-header__inner">

        {{-- LEFT --}}
        <div class="sr-header__left">
            <button id="menuToggle" class="menu-toggle">
                ☰
            </button>
            {{-- Breadcrumb / Page title --}}
            <div class="sr-header__title">
                
                <h6 class="mb-0">@yield('title', 'Dashboard')</h6>
            </div>
            @yield('headbar')
        </div>

        {{-- RIGHT --}}
        <div class="sr-header__right">
            {{-- Theme toggle (future ready) --}}
            {{-- <button class="sr-header__icon-btn" id="srThemeToggle">
                <i class="mdi mdi-weather-night"></i>
            </button> --}}

            {{-- Notifications --}}
            {{-- <div class="dropdown">
                <button class="sr-header__icon-btn" data-bs-toggle="dropdown">
                    <i class="mdi mdi-bell-outline"></i>
                    <span class="sr-header__dot"></span>
                </button>

                <div class="dropdown-menu dropdown-menu-end sr-dropdown">
                    <h6 class="dropdown-header">Notifications</h6>
                    <div class="sr-dropdown__empty">
                        No new notifications
                    </div>
                </div>
            </div> --}}

            {{-- User --}}
            <div class="dropdown">
                <button class="sr-header__user" data-bs-toggle="dropdown">
                    <div class="sr-header__avatar">
                            <img src="https://api.dicebear.com/7.x/adventurer-neutral/svg?seed={{ auth()->user()->fname }}+{{ auth()->user()->lname }}"
                                 alt="Avatar"
                                 class="w-px-40 h-auto rounded-circle" style="max-width: 40px">
                    </div>
                    <div class="d-flex flex-column sr-header__user-info d-none">
                        <span class="sr-header__user-name">
                            {{ auth()->user()->fname ?? 'User' }} {{ auth()->user()->lname ?? '' }}
                        </span>
                        <small class="text-muted">
                            {{ auth()->user()->email ?? '' }}
                        </small>
                    </div>
                    <i class="mdi mdi-chevron-down"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end sr-dropdown">
                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                        <i class="mdi mdi-account-outline me-2"></i> Profile
                    </a>
                    <a class="dropdown-item" href="{{ route('profile.settings') }}">
                        <i class="mdi mdi-cog-outline me-2"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger">
                            <i class="mdi mdi-logout me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>

{{-- =========================
     Header Interactions
========================= --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // Sidebar toggle (sync with sidebar)
        document.getElementById('srSidebarToggleBtn')
            ?.addEventListener('click', () => {
                document.body.classList.toggle('sr-sidebar-collapsed');
            });

        // Theme toggle (safe placeholder)
        document.getElementById('srThemeToggle')
            ?.addEventListener('click', () => {
                const theme = document.documentElement.getAttribute('data-theme');
                document.documentElement.setAttribute(
                    'data-theme',
                    theme === 'dark' ? 'light' : 'dark'
                );
            });

    });
</script>

{{-- =========================
     Header Styles
========================= --}}
<style>
    /* Header shell */
    .sr-header {
        position: sticky;
        top: 0;
        z-index: 99;
        background: var(--bs-body-bg);
        border-bottom: 1px solid var(--bs-border-color);
    }

    .sr-header__inner {
        height: 64px;
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Left */
    .sr-header__left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sr-header__title h6 {
        font-weight: 600;
        color: var(--bs-heading-color);
    }

    /* Right */
    .sr-header__right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Icon buttons */
    .sr-header__icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bs-body-color);
        transition: background .2s ease, color .2s ease;
    }

    .sr-header__icon-btn:hover {
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
    }

    /* Notification dot */
    .sr-header__dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--bs-danger);
    }

    /* User */
    .sr-header__user {
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
        background: transparent;
    }

    .sr-header__avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .sr-header__user-info {
        text-align: left;
        line-height: 1.2;
    }

    .sr-header__user-name {
        font-weight: 600;
        color: var(--bs-heading-color);
    }

    /* Dropdown */
    .sr-dropdown {
        min-width: 220px;
        border-radius: 12px;
        box-shadow: var(--bs-box-shadow-lg);
    }

    .sr-dropdown__empty {
        padding: 16px;
        text-align: center;
        color: var(--bs-secondary-color);
    }
</style>
