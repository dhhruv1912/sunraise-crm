@php
    $menu = config('sidebar-'.session('active_company'));
    $company = session('active_company', 'sunraise');
    $sidebar = config("sidebar-{$company}");
@endphp
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    
    @include('temp.company')

    {{-- Menu --}}
    <nav class="sidebar__nav " id="srSidebarNav">
        
        @foreach($menu as $item)
            @can($item['permission'] ?? null)

                @php
                    $hasChildren = !empty($item['children']);
                    $isActive = $item['active'] ?? false;
                    $isOpen   = $item['open'] ?? false;
                @endphp

                <div class="menu {{ $isActive ? 'is-active' : '' }} {{ $hasChildren && $isOpen ? 'is-open' : '' }}">

                    {{-- Parent --}}
                    <div class="menu__item {{ $hasChildren ? 'has-children' : '' }}">
                        <a
                            href="{{ $hasChildren ? 'javascript:void(0)' : (isset($item['route']) ? route($item['route']) : '#') }}"
                            class="menu__link"
                        >
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['title'] }}</span>
                            @if($hasChildren)
                                <i class="mdi mdi-chevron-down menu__arrow"></i>
                            @endif
                        </a>
                    </div>

                    {{-- Children --}}
                    @if($hasChildren)
                        <div class="menu__submenu">
                            @foreach($item['children'] as $child)
                                @can($child['permission'] ?? null)
                                    
                                    <a
                                        href="{{ is_array($child['route']) ? route($child['route'][0], $child['route'][1]) : route($child['route']) }}"
                                        class="submenu__link my-1 {{ ($child['active'] ?? false) ? 'is-active' : '' }}"
                                    >
                                        @if ($child['icon'])
                                            <span class="{{ $child['icon'] }} menu-sub-icon"></span>  
                                        @endif
                                        {{ $child['title'] }}
                                    </a>
                                @endcan
                            @endforeach
                        </div>
                    @endif

                </div>

            @endcan
        @endforeach
        
    </nav>
</aside>

{{-- =========================
     Sidebar Interaction JS
========================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    // Submenu toggle
    document.querySelectorAll('.menu .has-children .menu__link')
    .forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();

            const currentMenu = link.closest('.menu');
            const isOpen = currentMenu.classList.contains('is-open');

            // Close all menus
            document.querySelectorAll('.menu.is-open')
                .forEach(menu => menu.classList.remove('is-open'));

            // Toggle only the clicked one
            if (!isOpen) {
                currentMenu.classList.add('is-open');
            }
        });
    });

    // Collapse sidebar
    const toggle = document.getElementById('srSidebarNav');
    toggle?.addEventListener('mouseenter', () => {
        document.body.classList.remove('sidebar-collapsed');
        document.body.classList.add('sidebar-expand');
        document.getElementById("logoMain").classList.remove('d-none');
        document.getElementById("logoCropped").classList.add('d-none');
    });
    toggle?.addEventListener('mouseleave', () => {
        document.body.classList.add('sidebar-collapsed');
        document.body.classList.remove('sidebar-expand');
        document.getElementById("logoMain").classList.add('d-none');
        document.getElementById("logoCropped").classList.remove('d-none');
    });

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("menuToggle");
    const sidebar = document.getElementById("sidebar");

    toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        document.body.classList.add("sidebar-expand");
        document.body.classList.remove("sidebar-collapsed");
        document.getElementById("logoMain").classList.remove('d-none');
        document.getElementById("logoCropped").classList.add('d-none');
    });

    // Close when clicking outside (optional but professional)
    document.addEventListener("click", function (e) {
        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove("active");
            document.body.classList.remove("sidebar-expand");
            document.body.classList.add("sidebar-collapsed");
            document.getElementById("logoMain").classList.add('d-none');
            document.getElementById("logoCropped").classList.remove('d-none');
        }
    });
});
</script>