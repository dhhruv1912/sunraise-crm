@php
    $menu = config('sidebar-'.session('active_company'));
@endphp
<aside class="sr-sidebar">

    {{-- Brand --}}
    
    @include('temp.company')

    {{-- Menu --}}
    <nav class="sr-sidebar__nav " id="srSidebarNav">

        @foreach($menu as $item)
            @can($item['permission'] ?? null)

                @php
                    $hasChildren = !empty($item['children']);
                    $isActive = $item['active'] ?? false;
                    $isOpen   = $item['open'] ?? false;
                @endphp

                <div class="sr-menu {{ $isActive ? 'is-active' : '' }} {{ $hasChildren && $isOpen ? 'is-open' : '' }}">

                    {{-- Parent --}}
                    <div class="sr-menu__item {{ $hasChildren ? 'has-children' : '' }}">
                        <a
                            href="{{ $hasChildren ? 'javascript:void(0)' : (isset($item['route']) ? route($item['route']) : '#') }}"
                            class="sr-menu__link"
                        >
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['title'] }}</span>
                            @if($hasChildren)
                                <i class="mdi mdi-chevron-down sr-menu__arrow"></i>
                            @endif
                        </a>
                    </div>

                    {{-- Children --}}
                    @if($hasChildren)
                        <div class="sr-menu__submenu">
                            @foreach($item['children'] as $child)
                                @can($child['permission'] ?? null)
                                    
                                    <a
                                        href="{{ is_array($child['route']) ? route($child['route'][0], $child['route'][1]) : route($child['route']) }}"
                                        class="sr-submenu__link rounded-3 my-1 {{ ($child['active'] ?? false) ? 'is-active' : '' }}"
                                    >
                                        <span class="mdi mdi-format-list-bulleted"></span>  
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
    document.querySelectorAll('.sr-menu .has-children .sr-menu__link')
        .forEach(link => {
            link.addEventListener('click', () => {
                link.closest('.sr-menu').classList.toggle('is-open');
            });
        });

    // Collapse sidebar
    const toggle = document.getElementById('srSidebarNav');
    toggle?.addEventListener('mouseenter', () => {
        document.body.classList.remove('sr-sidebar-collapsed');
        document.body.classList.add('sr-sidebar-expand');
        document.getElementById("logoMain").classList.remove('d-none');
        document.getElementById("logoCropped").classList.add('d-none');
    });
    // toggle?.addEventListener('mouseleave', () => {
    //     document.body.classList.add('sr-sidebar-collapsed');
    //     document.body.classList.remove('sr-sidebar-expand');
    //     document.getElementById("logoMain").classList.add('d-none');
    //     document.getElementById("logoCropped").classList.remove('d-none');
    // });

});
</script>

{{-- =========================
     Modern Sidebar Styles
     (theme-sr.css driven)
========================= --}}
<style>
</style>