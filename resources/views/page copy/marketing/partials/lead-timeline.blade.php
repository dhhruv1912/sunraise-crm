@php
    $iconMap = [
        'created' => 'bi-plus-circle',
        'assigned' => 'bi-person-fill',
        'status' => 'bi-arrow-repeat',
        'followup' => 'bi-clock',
        'call' => 'bi-telephone',
        'email' => 'bi-envelope-fill',
        'note' => 'bi-sticky',
        'converted' => 'bi-flag-fill',
    ];
@endphp

<div class="timeline">
    @foreach($history as $h)
        @php
            $type = $h->action ?? 'note';
            $icon = $iconMap[$type] ?? 'bi-dot';
        @endphp
        <div class="d-flex mb-3">
            <div class="me-3">
                <span class="badge bg-primary rounded-circle p-2">
                    <i class="bi {{ $icon }}"></i>
                </span>
            </div>
            <div>
                <div><strong>{{ ucfirst(str_replace('_',' ',$h->action ?? '')) }}</strong></div>
                <div>{{ $h->message }}</div>
                <div class="small text-muted">{{ $h->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>
    @endforeach
</div>
