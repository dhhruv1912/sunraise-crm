<div class="col-md-4">
    <div class="crm-section h-100">

        <div class="d-flex align-items-center mb-2">
            <img src="{{ $u->avatar
                ? asset('storage/'.$u->avatar)
                : asset('assets/img/avatar-placeholder.png') }}"
                 class="rounded-circle me-2"
                 style="width:48px;height:48px;object-fit:cover">

            <div>
                <div class="fw-semibold">
                    {{ $u->fname }} {{ $u->lname }}
                </div>
                <div class="text-muted small">
                    {{ $u->email ?? '—' }}
                </div>
            </div>
        </div>

        <div class="row g-2 small">
            <div class="col-6">
                <div class="text-muted">Mobile</div>
                <div class="fw-semibold">{{ $u->mobile ?? '—' }}</div>
            </div>

            <div class="col-6">
                <div class="text-muted">Role</div>
                <div class="fw-semibold">
                    {{ ucfirst($u->role ?? 'user') }}
                </div>
            </div>

            <div class="col-12 mt-2">
                <a href="{{ route('profile.settings') }}"
                   class="btn btn-sm btn-outline-primary w-100">
                    <i class="fa-solid fa-gear me-1"></i>
                    Personal Settings
                </a>
            </div>
        </div>

    </div>
</div>
