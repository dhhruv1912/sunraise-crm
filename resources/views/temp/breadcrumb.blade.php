@php
    use App\Helpers\Breadcrumb;
    $breadcrumb = Breadcrumb::load();
@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style1">

        @foreach ($breadcrumb as $name => $url)
            @if ($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $name }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $url }}">{{ $name }}</a>
                </li>
            @endif
        @endforeach

    </ol>
</nav>
