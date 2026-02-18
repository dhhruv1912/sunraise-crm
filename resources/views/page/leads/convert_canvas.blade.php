<div class="crm-section">

    <h6 class="mb-3">
        <i class="fa-solid fa-diagram-project me-1 text-primary"></i>
        Convert Lead → Project
    </h6>

    {{-- SUMMARY --}}
    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <div class="text-muted small">Customer</div>
            <div class="fw-semibold">{{ $lead->customer->name }}</div>
        </div>
        <div class="col-md-6">
            <div class="text-muted small">Quote Package</div>
            <div class="fw-semibold">
                {{ optional($lead->quoteMaster)->module }} ({{ optional($lead->quoteMaster)->kw }} kW)
            </div>
        </div>
    </div>

    <hr>

    {{-- FORM --}}
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small">Final Price</label>
            <input type="number" id="finalizePrice"
                   class="form-control"
                   value="{{ optional($lead->quoteMaster)->payable ?? 0 }}">
        </div>

        <div class="col-md-6">
            <label class="form-label small">Priority</label>
            <select id="priority" class="form-select">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>
        </div>
    </div>

    <hr>

    {{-- ACTIONS --}}
    <div class="d-flex justify-content-end gap-2 mt-3">
        <button class="btn btn-light"
                onclick="closeLeadCanvas()">Cancel</button>

        <button class="btn btn-success"
                onclick="submitLeadConversion({{ $lead->id }})">
            <i class="fa-solid fa-check me-1"></i>
            Create Project
        </button>
    </div>

    <div class="crm-loader-overlay d-none" id="convertLoader">
        <div class="crm-spinner"></div>
    </div>

</div>
<div class="crm-section mt-3" id="emiSection">
    <div class="fw-semibold mb-2">
        <i class="fa-solid fa-wallet me-1"></i>
        EMI Breakdown
    </div>

    <div id="emiRows"></div>

    <button class="btn btn-sm btn-outline-primary mt-2"
            type="button"
            onclick="addEmiRow()">
        <i class="fa-solid fa-plus"></i> Add EMI
    </button>

    <hr>

    <div class="row small">
        <div class="col">
            <div class="text-muted">Total EMI</div>
            <div class="fw-bold text-success" id="totalEmi">₹ 0</div>
        </div>
        <div class="col text-end">
            <div class="text-muted">Remaining</div>
            <div class="fw-bold text-warning" id="remainingAmount">₹ 0</div>
        </div>
    </div>
</div>

<div class="crm-section mt-4" id="milestoneSection">
    <div class="fw-semibold mb-2">
        <i class="fa-solid fa-flag-checkered me-1"></i>
        Project Milestones
    </div>

    <div id="milestoneRows"></div>

    <button type="button"
            class="btn btn-sm btn-outline-primary mt-2"
            onclick="addMilestoneRow()">
        <i class="fa-solid fa-plus"></i> Add Milestone
    </button>
</div>