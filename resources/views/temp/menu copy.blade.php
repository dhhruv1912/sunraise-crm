@php
    use App\Helpers\MenuArray;
    $menu = MenuArray::items();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="menu-inner-shadow"></div>

    @include('temp.company')
    <ul class="menu-inner py-1">
        @foreach ($menu as $item)
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