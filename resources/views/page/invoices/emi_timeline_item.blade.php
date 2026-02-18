@php
    $date = \Carbon\Carbon::parse($r['date']);
    $days = $r['days'];
@endphp

<div class="crm-timeline-item">
    <div class="crm-timeline-dot
        {{ $days > 0 ? 'bg-danger' : ($days === 0 ? 'bg-warning' : 'bg-primary') }}">
    </div>

    <div class="crm-timeline-content ps-2">

        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold">
                {{ $date->format('d M Y') }}

                @if($days > 0)
                    <span class="badge bg-danger-subtle text-danger ms-1">
                        Overdue by {{ $days }} day{{ $days > 1 ? 's' : '' }}
                    </span>
                @elseif($days === 0)
                    <span class="badge bg-warning-subtle text-warning ms-1">
                        Due Today
                    </span>
                @else
                    <span class="badge bg-primary-subtle text-primary ms-1">
                        {{ abs($days) }} day{{ abs($days) > 1 ? 's' : '' }} remaining
                    </span>
                @endif
            </div>

            <div class="fw-bold">
                ₹ {{ number_format(abs($r['amount'])) }}
            </div>
        </div>

        <div class="small text-muted mt-1">
            @if(!empty($r['project']))
                <span class="fw-semibold">{{ $r['project'] }}</span> ·
            @endif
            {{ $r['customer'] ?? '—' }}
        </div>

    </div>
</div>
