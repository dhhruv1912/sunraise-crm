<div class="col-md-6">
    <div class="d-flex flex-column">
        <div class="w-100 mb-3">
                @include('page.customers.partials.doc_uploader', [
                    'type' => 'aadhar_card',
                    'label' => 'Aadhar Card',
                    'placeholder' => asset('assets/img/placeholder/aadhar-card.png')
                ])
            {{-- @include('page.customers.partials.doc_uploader', [
                'type' => 'aadhar_card',
                'label' => 'Aadhar Card',
            ]) --}}
        </div>
        <div class="w-100">
            <label class="form-label small text-muted">Aadhar Number</label>
            <input type="text" class="form-control fw-semibold" name="aadhar_card_number"
                value="{{ $customer->aadhar_card_number }}">
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="d-flex flex-column">
        <div class="w-100 mb-3">
            @include('page.customers.partials.doc_uploader', [
                'type' => 'pan_card',
                'label' => 'PAN Card',
                'placeholder' => asset('assets/img/placeholder/pan-card.png')
            ])
        </div>
        <div class="w-100">
            <label class="form-label small text-muted">PAN Number</label>
            <input type="text" class="form-control fw-semibold" name="pan_card_number" value="{{ $customer->pan_card_number }}">
        </div>
    </div>
</div>