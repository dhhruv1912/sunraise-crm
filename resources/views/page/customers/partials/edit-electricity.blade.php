<div class="col-md-12">
    <div class="d-flex flex-column">
        <div class="w-100 px-1 mb-3">
            @include('page.customers.partials.doc_uploader', [
                'type' => 'light_bill',
                'label' => 'Light Bill',
                'placeholder' => asset('assets/img/placeholder/light-bill.png')
            ])
        </div>
        <div class="w-100 px-1">
            <label class="form-label small text-muted">Light Bill Number</label>
            <input type="text" class="form-control fw-semibold" name="lightbill_number" value="{{ $customer->lightbill_number }}">
        </div>
        <div class="w-100 px-1">
            <label class="form-label small text-muted">Sanction Load</label>
            <input type="text" class="form-control fw-semibold" name="sanction_load" value="{{ $customer->sanction_load }}">
        </div>
        <div class="w-100 px-1">
            <label class="form-label small text-muted">Service Number</label>
            <input type="text" class="form-control fw-semibold" name="service_number" value="{{ $customer->service_number }}">
        </div>
    </div>
</div>
