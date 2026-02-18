<div class="crm-section">
    <div class="fw-semibold mb-2">Task Indicator</div>

    {{-- @foreach($rows as $r) --}}
    <span class="badge bg-success">Projects</span>
    <span class="badge bg-info">Leads</span>
    <span class="badge bg-warning">Quotes</span>
    {{-- @endforeach --}}
</div>
<div class="crm-section mt-2">
    <div class="fw-semibold mb-2">Team Workload</div>

    @foreach($rows as $r)
        <div class="mb-3">

            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">{{ $r->assignee_name }}</span>
                <span class="badge bg-primary">{{ $r->total }} tasks</span>
            </div>

            
            <div class="progress mt-1" style="height:15px">
                {{-- <div class="progress-bar bg-success"style="width: {{ min($r->projects * 10, 100) }}%"></div> --}}
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ min($r->projects * 10, 100) }}%" aria-valuenow="{{ $r->projects }}" aria-valuemin="0" aria-valuemax="100">{{ $r->projects }}</div>
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: {{ min($r->leads * 10, 100) }}%" aria-valuenow="{{ $r->leads }}" aria-valuemin="0" aria-valuemax="100">{{ $r->leads }}</div>
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: {{ min($r->quotes * 10, 100) }}%" aria-valuenow="{{ $r->quotes }}" aria-valuemin="0" aria-valuemax="100">{{ $r->quotes }}</div>
            </div>

            <div class="d-flex w-100 small text-muted mt-1">
                @if ($r->projects)
                    <span style="width: {{ $r->projects * 10 }}%">Projects: {{ $r->projects }}</span>
                @endif
                @if ($r->leads)
                    <span style="width: {{ $r->leads * 10 }}%">Leads: {{ $r->leads }}</span>
                @endif
                @if ($r->quotes)
                    <span style="width: {{ $r->quotes * 10 }}%">Quotes: {{ $r->quotes }}</span>
                @endif
            </div>

        </div>
    @endforeach

    @if($rows->isEmpty())
        <div class="text-muted small">
            No workload data
        </div>
    @endif
</div>
