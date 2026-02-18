<div class="col-md-4 d-flex">
    @include('page.customers.partials.doc_uploader', [
        'type' => 'passport_size_photo',
        'label' => 'Passport Size Photo',
        'placeholder' => asset('assets/img/placeholder/user.jpg')
    ])
</div>

<div class="col-md-8">
    <div class="d-flex w-100 flex-wrap align-items-baseline">
        <div class="w-50 px-2">
            <label class="form-label small text-muted">Name *</label>
            <input type="text" class="form-control fw-semibold" name="name" value="{{ $customer->name }}" required>
        </div>
        <div class="w-50 px-2">
            <label class="form-label small text-muted">Email</label>
            <input type="email" class="form-control fw-semibold" name="email" value="{{ $customer->email }}">
        </div>
        <div class="w-50 px-2">
            <label class="form-label small text-muted">Mobile</label>
            <input type="text" class="form-control fw-semibold" name="mobile"
                value="{{ $customer->mobile }}">
        </div>
        <div class="w-50 px-2">
            <label class="form-label small text-muted">Alternate Mobile</label>
            <input type="text" class="form-control fw-semibold" name="alternate_mobile"
                value="{{ $customer->alternate_mobile }}">
        </div>
        <div class="w-100 px-2">
            <label class="form-label small text-muted">Address</label>
            <textarea class="form-control fw-semibold" name="address" rows="4">{{ $customer->address }}</textarea>
        </div>
    </div>
</div>
