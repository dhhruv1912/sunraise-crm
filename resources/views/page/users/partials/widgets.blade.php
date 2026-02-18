<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-muted small">Total Users</div>
            <div class="fs-4 fw-bold text-primary">{{ $total }}</div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-muted small">Active</div>
            <div class="fs-4 fw-bold text-success">{{ $active }}</div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-muted small">Inactive</div>
            <div class="fs-4 fw-bold text-danger">{{ $inactive }}</div>
        </div>
    </div>
</div>
