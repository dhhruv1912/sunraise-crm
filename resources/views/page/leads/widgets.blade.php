<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Total Leads</div>
        <div class="fs-4 fw-bold text-primary">{{ $total }}</div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Active Leads</div>
        <div class="fs-4 fw-bold text-info">{{ $active }}</div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Follow-ups Today</div>
        <div class="fs-4 fw-bold text-success">{{ $today }}</div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section {{ $overdue > 0 ? 'border-danger' : '' }}"
         id="leadOverdueWidget"
         style="cursor:pointer">
        <div class="text-muted small">Overdue Follow-ups</div>
        <div class="fs-4 fw-bold {{ $overdue > 0 ? 'text-danger' : 'text-muted' }}">
            {{ $overdue }}
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="crm-section">
        <div class="text-muted small">Avg Response Time</div>
        <div class="fs-4 fw-bold text-warning">
            {{ $avgResponse }} hrs
        </div>
    </div>
</div>
