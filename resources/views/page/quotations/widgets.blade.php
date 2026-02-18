<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Total Quotations</div>
        <div class="fs-4 fw-bold text-primary">{{ $total }}</div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Sent</div>
        <div class="fs-4 fw-bold text-success">{{ $sent }}</div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Draft</div>
        <div class="fs-4 fw-bold text-warning">{{ $draft }}</div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Total Value</div>
        <div class="fs-4 fw-bold text-info">
            ₹ {{ number_format($value) }}
        </div>
    </div>
</div>
