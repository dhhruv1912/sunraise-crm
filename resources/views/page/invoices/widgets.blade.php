<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Total Invoices</div>
        <div class="fs-4 fw-bold text-primary">
            {{ $total }}
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Paid</div>
        <div class="fs-4 fw-bold text-success">
            {{ $paid }}
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Partial</div>
        <div class="fs-4 fw-bold text-warning">
            {{ $partial }}
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Overdue</div>
        <div class="fs-4 fw-bold text-danger">
            {{ $overdue }}
        </div>
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
