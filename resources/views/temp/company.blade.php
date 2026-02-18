<div class="sidebar__brand w-100">
    <a href="{{ route('company.select') }}" class="sidebar__brand-link">
        <img src="{{ asset('assets/img/logo/'.session('active_company').'-logo-f.png') }}" onerror="{{ asset('assets/img/logo/sunraise-logo-f.png') }}" alt="" class="h-px-40 w-100 d-none" id="logoMain">
        <img src="{{ asset('assets/img/logo/'.session('active_company').'-logo-s.png') }}" onerror="{{ asset('assets/img/logo/sunraise-logo-s.png') }}" alt="" class="h-px-40 w-100" id="logoCropped">
    </a>
</div>