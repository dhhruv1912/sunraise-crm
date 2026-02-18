<div class="crm-section">
    <div class="fw-semibold mb-2">EMI Cashflow</div>

    @foreach($rows as $date => $amount)
        <div class="crm-chip-action upcoming mb-1">
            <span>{{ \Carbon\Carbon::parse($date)->format('M Y') }}</span>
            <b>₹ {{ number_format($amount) }}</b>
        </div>
    @endforeach

    @if(empty($rows))
        <div class="text-muted small">
            No upcoming EMI cashflow
        </div>
    @endif
</div>
