<div class="modal-header" style="background: var(--arham-gradient-blue); color: white;">
    <h5 class="modal-title">Panel Details</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <div class="mb-3">
        <h6 class="text-muted">Serial Number</h6>
        <p style="color: var(--arham-text-body); font-weight: bold;">{{ $panel->serial_number }}</p>
    </div>

    <div class="mb-3">
        <h6 class="text-muted">Status</h6>
        <p class="badge bg-info">{{ $panel->status }}</p>
    </div>

    <div class="mb-3">
        <h6 class="text-muted">Batch</h6>
        <p>{{ $panel->batch_no_copy }}</p>
    </div>

    <div class="mb-3">
        <h6 class="text-muted">Warehouse</h6>
        <p>{{ optional($panel->warehouse)->name ?? '-' }}</p>
    </div>

    <div class="mb-3">
        <h6 class="text-muted">Customer</h6>
        <p>{{ optional($panel->customer)->name ?? '-' }}</p>
    </div>

    <hr>

    <h6 class="text-muted">Movement History</h6>

    <ul class="list-group">
        @foreach($panel->movements as $m)
            <li class="list-group-item">
                <strong>{{ ucfirst($m->action) }}</strong>  
                <span class="text-muted">{{ $m->happened_at->format('d M Y, H:i') }}</span>
                <br>
                <small>{{ $m->note }}</small>
            </li>
        @endforeach
    </ul>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
