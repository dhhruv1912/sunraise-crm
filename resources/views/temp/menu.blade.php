@php
    $menu = config('sidebar');
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="menu-inner-shadow"></div>

    <div class="app-brand demo d-md-none">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo me-1">
                <span style="color: var(--bs-primary)">
                    {{-- @include('temp.logo') Move SVG to separate file --}}
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2">Materio</span>
        </a>
    </div>
    <ul class="menu-inner py-1">
        @foreach ($menu as $item)
            {{-- PARENT PERMISSION CHECK --}}
            @if (!empty($item['permission']) && !auth()->user()->can($item['permission']))
                @continue
            @endif

            <li class="menu-item {{ $item['open'] ? 'active' : '' }}">
                @if (isset($item['children']))
                    <a href="javascript:void(0)" class="d-flex menu-link menu-toggle w-100 waves-effect">
                        <i class="menu-icon tf-icons {{ $item['icon'] }}"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>

                    <ul class="menu-sub" style="{{ $item['open'] ? 'display:block;' : '' }}">
                        @foreach ($item['children'] as $child)
                            {{-- CHILD PERMISSION CHECK --}}
                            @if (!empty($child['permission']) && !auth()->user()->can($child['permission']))
                                @continue
                            @endif

                            <li class="menu-item {{ $child['active'] ? 'active' : '' }}">
                                <a class="menu-link"
                                    href="{{ isset($child['route']) ? (is_array($child['route']) ? route($child['route'][0], $child['route'][1]) : route($child['route'])) : '' }}">
                                    {{ $child['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ isset($item['route']) ? (is_array($item['route']) ? route($item['route'][0], $item['route'][1]) : route($item['route'])) : '' }}"
                        class="d-flex menu-link w-100 waves-effect {{ $item['active'] ? 'active' : '' }}">
                        <i class="menu-icon tf-icons {{ $item['icon'] }}"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>
                @endif

            </li>
        @endforeach
    </ul>
</aside>
<div id="menu-overlay">
</div>
