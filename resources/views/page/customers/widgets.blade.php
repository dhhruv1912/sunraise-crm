<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Total Cutomers</div>
        <div class="fs-4 fw-bold text-primary">
            {{ $total }}
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Active Projects</div>
        <div class="fs-4 fw-bold text-warning">
            {{ $active }} / {{ $projects }}
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Total Invoiced</div>
        <div class="fs-4 fw-bold text-info">
            ₹ {{ number_format($totalInvoiced) }}
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="crm-section">
        <div class="text-muted small">Outstanding</div>
        <div class="fs-4 fw-bold text-danger">
            ₹ {{ number_format($outstanding) }}
        </div>
    </div>
</div>
