<div class="row g-3 align-items-end">

    <div class="col-md-3">
        <label class="form-label small">Entity</label>
        <select id="filterEntity" class="form-select">
            <option value="">All</option>
            <option value="App\Models\Customer">Customer</option>
            <option value="App\Models\Project">Project</option>
            <option value="App\Models\Invoice">Invoice</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label small">Document Type</label>
        <select id="filterType" class="form-select">
            <option value="">All</option>
            <option value="aadhar_card">Aadhar Card</option>
            <option value="pan_card">PAN Card</option>
            <option value="light_bill">Light Bill</option>
            <option value="cancel_cheque">Cancel Cheque</option>
            <option value="passport_size_photo">Photo</option>
            <option value="general">General</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small">Search</label>
        <input type="text" id="searchBox"
               class="form-control"
               placeholder="File name">
    </div>

    <div class="col-md-2 text-end">
        <button class="btn btn-primary w-100"
                onclick="loadDocuments(1)">
            <i class="fa-solid fa-filter me-1"></i>
            Apply
        </button>
    </div>

</div>
