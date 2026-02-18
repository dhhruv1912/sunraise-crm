    <div class="fw-semibold mb-2">EMI Schedule & Actions</div>

    {{-- EMI TIMELINE --}}
    <div class="mb-3">
        @foreach($emiActions as $e)
            <div class="crm-chip-action {{ $e['state'] }} mb-1 rounded px-2 d-flex justify-content-between align-items-baseline">
                <span>
                    {{ \Carbon\Carbon::parse($e['date'])->format('d M Y') }}
                    @if($e['state'] === 'overdue')
                        <small class="ms-1">(Overdue)</small>
                    @endif
                </span>
                <b>₹ {{ number_format($e['amount']) }}</b>
            </div>
        @endforeach
    </div>
    {{-- EMI PAYMENT FORM --}}
    <div class="border-top pt-2">
        <div class="fw-semibold small mb-2">Record EMI Payment</div>

        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small">EMI Date</label>
                <select id="emiDate" class="form-select">
                    <option value="">Select EMI</option>
                    @foreach($emiActions as $e)
                        <option value="{{ $e['date'] }}"
                            data-amount="{{ $e['amount'] }}"
                            {{ $e['state'] === 'paid' ? 'disabled' : '' }}>
                            {{ \Carbon\Carbon::parse($e['date'])->format('d M Y') }}
                            {{ $e['state'] === 'paid' ? ' (Paid)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small">Amount</label>
                <input type="number" id="emiAmount" class="form-control" readonly>
            </div>

            <div class="col-md-10">
                <label class="form-label small">Reference</label>
                <input type="text" id="emiRef" class="form-control"
                       placeholder="UTR / Cheque / Cash">
            </div>


            <div class="col-md-2">
                <button class="btn btn-success w-100"
                        onclick="submitEmiPayment()">
                    Pay
                </button>
            </div>
        </div>
    </div>
