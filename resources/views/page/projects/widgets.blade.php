{{-- ROW 1 --}}
<div class="row g-3">

    {{-- EXECUTION RADIAL --}}
    <div class="col-md-4">
        <div class="crm-section text-center">
            <div class="crm-radial" style="--value: {{ $executionPercent }};">
                <span>{{ $executionPercent }}%</span>
            </div>
            <div class="mt-2 fw-semibold">Execution Progress</div>
            <div class="small text-muted">Across active projects</div>
        </div>
    </div>

    {{-- ALERT STACK --}}
    <div class="col-md-4">
        <div class="crm-section">
            <div class="fw-semibold mb-2">Attention Required</div>

            <div class="crm-alert danger">
                <span>On Hold</span><b>{{ $onHold }}</b>
            </div>

            <div class="crm-alert warning">
                <span>Delayed (SLA)</span><b>{{ $delayed }}</b>
            </div>

            <div class="crm-alert danger">
                <span>Overdue EMI</span><b>{{ $overdueEmi }}</b>
            </div>

            <div class="crm-alert secondary">
                <span>Blocked by Docs</span><b>{{ $blockedByDocs }}</b>
            </div>
        </div>
    </div>

    {{-- UPCOMING --}}
    <div class="col-md-4">
        <div class="crm-section">
            <div class="fw-semibold mb-2">Upcoming (7 Days)</div>

            @forelse($upcomingList as $u)
                <div class="crm-chip">
                    <span>{{ $u['label'] }}</span>
                    <small>{{ $u['date'] }}</small>
                </div>
            @empty
                <div class="text-muted small">No upcoming deadlines</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ROW 2 --}}
<div class="row g-3 mt-1">

    {{-- STATUS DISTRIBUTION --}}
    <div class="col-md-12">
        <div class="crm-section">
            <div class="fw-semibold mb-2">Project Status Distribution</div>

            <div class="progress" style="height:10px">
                <div class="progress-bar bg-secondary" style="width: {{ $newPct }}%"></div>
                <div class="progress-bar bg-primary" style="width: {{ $installPct }}%"></div>
                <div class="progress-bar bg-success" style="width: {{ $completePct }}%"></div>
            </div>

            <div class="d-flex justify-content-between small text-muted mt-1">
                <span>New</span>
                <span>Execution</span>
                <span>Completed</span>
            </div>
        </div>
    </div>

</div>
