{{-- Document Upload Section (Original UI preserved) --}}
<div class="tab-pane fade" id="tab-docs" role="tabpanel">

    {{-- Aadhaar --}}
    <div class="row py-2">
        <div class="border border-dark col-6 p-1 rounded position-relative h-100 document-upload-element"
             data-placeholder_url="{{ asset('assets/img/placeholder/aadhar-card.png') }}">

            <button type="button" class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-close"></span> Remove
            </button>

            <button type="button" class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-plus"></span> Add
            </button>

            <input type="file" name="aadhar_card" id="aadhar-card" class="d-none" accept="image/*">
            <input type="hidden" name="aadhar_card_hidden" value="{{ $project->aadhar_card }}">

            <img src="@if($project->aadhar_card) {{ asset('storage/'.$project->aadhar_card) }} @else {{ asset('assets/img/placeholder/aadhar-card.png') }} @endif"
                 class="rounded w-100 placeholder_img" style="height:auto; object-fit:cover;">
        </div>

        <div class="col-6 p-1">
            <div class="row mb-3 px-2">
                <label class="col-form-label">Aadhar Card Number</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                        <input type="text" class="form-control" id="aadhar-card-number"
                               name="aadhar_card_number" value="{{ $project->aadhar_card_number }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAN --}}
    <div class="row py-2">
        <div class="border border-dark col-6 p-1 rounded position-relative h-100 document-upload-element"
             data-placeholder_url="{{ asset('assets/img/placeholder/pan-card.png') }}">

            <button type="button" class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-close"></span> Remove
            </button>

            <button type="button" class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-plus"></span> Add
            </button>

            <input type="file" name="pan_card" id="pan-card" class="d-none" accept="image/*">
            <input type="hidden" name="pan_card_hidden" value="{{ $project->pan_card }}">

            <img src="@if($project->pan_card) {{ asset('storage/'.$project->pan_card) }} @else {{ asset('assets/img/placeholder/pan-card.png') }} @endif"
                 class="rounded w-100 placeholder_img" style="height:auto; object-fit:cover;">
        </div>

        <div class="col-6 p-1">
            <div class="row mb-3 px-2">
                <label class="col-form-label">PAN Card Number</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                        <input type="text" class="form-control" name="pan_card_number"
                               value="{{ $project->pan_card_number }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LIGHT BILL --}}
    <div class="row py-2">
        <div class="border border-dark col-6 p-1 rounded h-100 document-upload-element"
             data-placeholder_url="{{ asset('assets/img/placeholder/light-bill.png') }}">

            <button type="button" class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-close"></span> Remove
            </button>

            <button type="button" class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-plus"></span> Add
            </button>

            <input type="file" name="light_bill" class="d-none">
            <input type="hidden" name="light_bill_hidden" value="{{ $project->light_bill }}">

            <img src="@if($project->light_bill) {{ asset('storage/'.$project->light_bill) }} @else {{ asset('assets/img/placeholder/light-bill.png') }} @endif"
                 class="rounded w-100 placeholder_img" style="height:auto; object-fit:cover;">
        </div>

        <div class="col-6 p-1">

            <div class="row mb-3 px-2">
                <label class="col-form-label">Light Bill Number</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="lightbill_number"
                               value="{{ $project->lightbill_number }}">
                    </div>
                </div>
            </div>

            <div class="row mb-3 px-2">
                <label class="col-form-label">Sanction Load</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="sanction_load"
                               value="{{ $project->sanction_load }}">
                    </div>
                </div>
            </div>

            <div class="row mb-3 px-2">
                <label class="col-form-label">Service Number</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="service_number"
                               value="{{ $project->service_number }}">
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- BANK --}}
    <div class="row py-2">
        <div class="border border-dark col-7 p-1 rounded document-upload-element"
             data-placeholder_url="{{ asset('assets/img/placeholder/chaque-2.png') }}">

            <button type="button" class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-close"></span> Remove
            </button>

            <button type="button" class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 d-none"
                    style="bottom:0; left:0; opacity:0.5;">
                <span class="tf-icons mdi mdi-plus"></span> Add
            </button>

            <input type="file" name="cancel_cheque" class="d-none" accept="image/*">
            <input type="hidden" name="cancel_cheque_hidden" value="{{ $project->cancel_cheque }}">

            <img src="@if($project->cancel_cheque) {{ asset('storage/'.$project->cancel_cheque) }} @else {{ asset('assets/img/placeholder/chaque-2.png') }} @endif"
                 class="rounded w-100 placeholder_img" style="height:auto; object-fit:cover;">
        </div>

        <div class="col-5">

            <div class="row mb-3">
                <label class="col-form-label">Bank Account Number</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="bank_account_number"
                               value="{{ $project->bank_account_number }}">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-form-label">MICR Code</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="micr_code"
                               value="{{ $project->micr_code }}">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-form-label">IFSC Code</label>
                <div class="">
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="ifsc_code"
                               value="{{ $project->ifsc_code }}">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-form-label">Bank Name</label>
                <div class="">
                    <input type="text" class="form-control" name="bank_name"
                           value="{{ $project->bank_name }}">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-form-label">A/C Holder Name</label>
                <div class="">
                    <input type="text" class="form-control" name="ac_holder_name"
                           value="{{ $project->ac_holder_name }}">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-form-label">Branch Name</label>
                <div class="">
                    <input type="text" class="form-control" name="branch_name"
                           value="{{ $project->branch_name }}">
                </div>
            </div>

        </div>

    </div>

</div>
