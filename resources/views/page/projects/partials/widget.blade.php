<div class="col-md-4">
        <div class="crm-section text-center">
            <div class="crm-radial" style="--value: {{ $executionPercent }};">
                <span>{{ $executionPercent }}%</span>
            </div>
            <div class="mt-2 fw-semibold">Execution Progress</div>
        </div>
    </div>
    {{-- FINANCIAL --}}
    <div class="col-md-4">
        <div class="crm-section">
            <div class="fw-semibold mb-2">Financial Health</div>

            <div class="progress" style="height:8px">
                <div class="progress-bar bg-success" style="width: {{ $paidPercent }}%"></div>
                <div class="progress-bar bg-warning" style="width: {{ $balancePercent }}%"></div>
            </div>

            <div class="d-flex justify-content-between small mt-1">
                <span>Paid ₹{{ number_format($paid) }}</span>
                <span>Balance ₹{{ number_format($balance) }}</span>
            </div>
        </div>
    </div>

    {{-- RISKS --}}
    <div class="col-md-4">
        <div class="crm-section">
            <div class="fw-semibold mb-2">Attention</div>

            @if ($isDelayed)
                <div class="crm-alert warning">Execution Delayed</div>
            @endif

            @if ($hasOverdueEmi)
                <div class="crm-alert danger">Overdue EMI</div>
            @endif
        </div>
    </div>