<div class="crm-section">
    <div class="fw-semibold mb-2">Delay Analysis</div>

    @foreach($rows as $status => $delayed)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span>{{ $status }}</span>
            <span class="badge bg-danger">
                {{ $delayed }} delayed
            </span>
        </div>
    @endforeach

    @if(count($rows)== 0)
        <div class="text-muted small">
            No delayed projects 🎉
        </div>
    @endif
</div>
