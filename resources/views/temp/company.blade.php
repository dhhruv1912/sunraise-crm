<div class="app-brand d-flex demo justify-content-between px-3">
    <a href="{{ route('company.select') }}" class="app-brand-link">
        <span class="app-brand-logo demo me-1">
            <span style="color: var(--bs-primary)">
                <img src="{{ asset('assets/img/logo/'.session('active_company').'-logo.png') }}" onerror="{{ asset('assets/img/logo/sunraise-logo.png') }}" alt="" class="h-px-40">
            </span>
        </span>
    </a>
</div>