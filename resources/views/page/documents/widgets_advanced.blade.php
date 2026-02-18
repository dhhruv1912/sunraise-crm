{{-- MISSING KYC --}}
<div class="col-md-4">
    <div class="crm-section border-start border-4 border-danger">
        <div class="d-flex justify-content-between">
            <div>
                <div class="text-muted small">Customers with Missing KYC</div>
                <div class="fs-4 fw-bold text-danger">
                    {{ $missingKyc }}
                </div>
            </div>
            <i class="fa-solid fa-id-card text-danger fs-4"></i>
        </div>
    </div>
</div>

{{-- LARGE FILES --}}
<div class="col-md-4">
    <div class="crm-section border-start border-4 border-warning">
        <div class="d-flex justify-content-between">
            <div>
                <div class="text-muted small">Large Files (&gt; 5MB)</div>
                <div class="fs-4 fw-bold text-warning">
                    {{ $largeFiles }}
                </div>
            </div>
            <i class="fa-solid fa-database text-warning fs-4"></i>
        </div>
    </div>
</div>

{{-- RECENT UPLOADS --}}
<div class="col-md-4">
    <div class="crm-section">
        <div class="text-muted small mb-2">
            Recent Uploads
        </div>

        @forelse($recent as $d)
            <div class="d-flex justify-content-between small mb-1">
                <div class="text-truncate">
                    {{ $d->file_name }}
                </div>
                <div class="text-muted">
                    {{ $d->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="text-muted small">
                No recent uploads
            </div>
        @endforelse
    </div>
</div>
