<div class="col-md-8">
    @include('page.customers.partials.doc_uploader', [
        'type' => 'cancel_cheque',
        'label' => 'Cancelled Cheque',
                'placeholder' => asset('assets/img/placeholder/chaque-2.png')
    ])
</div>
<div class="col-md-4">
    <div class="d-flex w-100 flex-wrap align-items-baseline">
        <div class="w-100 px-2">
            <label class="form-label small text-muted">Bank Name</label>
            <input type="text" class="form-control fw-semibold" name="bank_name" value="{{ $customer->bank_name }}">
        </div>
        <div class="w-100 px-2">
            <label class="form-label small text-muted">IFSC Code</label>
            <input type="text" class="form-control fw-semibold" name="ifsc_code" value="{{ $customer->ifsc_code }}">
        </div>
        <div class="w-100 px-2">
            <label class="form-label small text-muted">Account Holder Name</label>
            <input type="text" class="form-control fw-semibold" name="ac_holder_name" value="{{ $customer->ac_holder_name }}">
        </div>
        <div class="w-100 px-2">
            <label class="form-label small text-muted">Account Number</label>
            <input type="text" class="form-control fw-semibold" name="bank_account_number" value="{{ $customer->bank_account_number }}">
        </div>
        <div class="w-100 px-2">
            <label class="form-label small text-muted">Branch Name</label>
            <input type="text" class="form-control fw-semibold" name="branch_name" value="{{ $customer->branch_name }}">
        </div>
    </div>
</div>
