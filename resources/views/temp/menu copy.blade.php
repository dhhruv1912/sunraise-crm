@php
    use App\Helpers\MenuArray;
    $menu = MenuArray::items();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        @foreach ($menu as $item)
            {{-- @can($item['permission']) --}}
            <li class="menu-item {{ $item['open'] }}">
                @if (isset($item['children']))
                    <a href="javascript:void(0);" class="d-flex menu-link menu-toggle w-100 waves-effect">
                        <i class="menu-icon tf-icons {{ $item['icon'] }}"></i>
                        <div>{{ $item['title'] }}</div>
                    </a>

                    <ul class="menu-sub">
                        @foreach ($item['children'] as $child)
                            {{-- @can($child['permission']) --}}
                            <li class="menu-item {{ $child['active'] ? 'active' : '' }}">
                                <a href="{{ isset($child['route']) ? (is_array($child['route']) ? route($child['route'][0], $child['route'][1]) : route($child['route'])) : '' }}"
                                    {{-- {{ is_array($child['route']) ? route($child['route'][0], $child['route'][1]) : route($child['route']) }} --}} class="menu-link">
                                    <div>{{ $child['title'] }}</div>
                                </a>
                            </li>
                            {{-- @endcan --}}
                        @endforeach

                    </ul>
                @else
                    <a href="{{ is_array($item['route']) ? route($item['route'][0], $item['route'][1]) : route($item['route']) }}"
                        class="d-flex menu-link w-100 waves-effect">
                        <i class="menu-icon tf-icons {{ $item['icon'] }}"></i>
                        <div>{{ $item['title'] }}</div>
                    </a>
                @endif
            </li>
            {{-- @endcan --}}
        @endforeach

    </ul>

</aside>


@can('view users')
    <!-- menu item -->
@endcan
{{-- @if (empty($item['permission']) || auth()->user()->can($item['permission']))
   <a href="...">{{ $item['title'] }}</a>
@endif --}}
@role('Admin')
    <!-- admin only -->
@endrole
