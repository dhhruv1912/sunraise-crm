<form action="{{ route('customers.update', $project->customer->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="border border-dark  p-1 rounded position-relative h-100 document-upload-element"
                id="custome-aadhar-card-image-wraper"
                data-placeholder_url="{{ asset('assets/img/placeholder/user.jpg') }}">
                <button type="button"
                    class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-close"></span> Remove
                </button>
                <button type="button"
                    class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-plus"></span> Add
                </button>
                <input type="file" name="passport_size_photo" id="passport_size_photo" class="d-none"
                    accept="image/*">
                <input type="hidden" name="passport_size_photo_hidden" id="passport_size_photo_hidden"
                    value="{{ $project->customer->passport_size_photo }}">
                <img src="@if (
                    $project->customer->passport_size_photo != null &&
                        $project->customer->documents()->find($project->customer->passport_size_photo) != '') {{ $project->customer->documents()->find($project->customer->passport_size_photo)->url }}@else{{ asset('assets/img/placeholder/user.jpg') }} @endif"
                    alt="" id="choose-customer" style="height: auto;object-fit: cover;"
                    class="rounded w-100 placeholder_img">
            </div>
        </div>
        <div class="align-content-start col-md-9 d-flex flex-wrap p-3">
            <div class="col-12 col-md-6 p-1">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ $project->customer->name }}"
                    class="form-control">
            </div>
            <div class="col-12 col-md-6 p-1">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="{{ $project->customer->email }}"
                    class="form-control">
            </div>
            <div class="col-12 col-md-6 p-1">
                <label for="mobile">Mobile</label>
                <input type="text" id="mobile" name="mobile" value="{{ $project->customer->mobile }}"
                    class="form-control">
            </div>
            <div class="col-12 col-md-6 p-1">
                <label for="alternate_mobile">Alternate Mobile</label>
                <input type="text" id="alternate_mobile" name="alternate_mobile"
                    value="{{ $project->customer->alternate_mobile }}" class="form-control">
            </div>
            <div class="p-1 col-12">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control h-px-100" cols="30" rows="10">{{ $project->customer->address }}</textarea>
            </div>
        </div>

        <div class="divider">
            <div class="divider-text">Identity Documents</div>
        </div>
        <div class="col-md-4">
            <div class="border border-dark  p-1 rounded position-relative h-100 document-upload-element"
                id="custome-aadhar-card-image-wraper"
                data-placeholder_url="{{ asset('assets/img/placeholder/aadhar-card.png') }}">
                <button type="button"
                    class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-close"></span> Remove
                </button>
                <button type="button"
                    class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-plus"></span> Add
                </button>
                <input type="file" name="aadhar_card" id="aadhar_card" class="d-none" accept="image/*">
                <input type="hidden" name="aadhar_card_hidden" id="aadhar_card_hidden"
                    value="{{ $project->customer->aadhar_card }}">
                <img src="@if (
                    $project->customer->aadhar_card != null &&
                        $project->customer->documents()->find($project->customer->aadhar_card) != '') {{ $project->customer->documents()->find($project->customer->aadhar_card)->url }}@else{{ asset('assets/img/placeholder/aadhar-card.png') }} @endif"
                    alt="" id="choose-customer" style="height: auto;object-fit: cover;"
                    class="rounded w-100 placeholder_img">
            </div>
        </div>

        <div class="col-md-4">
            <div class="border border-dark p-1 rounded position-relative h-100 document-upload-element"
                id="custome-pan-card-image-wraper"
                data-placeholder_url="{{ asset('assets/img/placeholder/pan-card.png') }}">
                <button type="button"
                    class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-close"></span> Remove
                </button>
                <button type="button"
                    class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-plus"></span> Add
                </button>
                <input type="file" name="pan_card" id="pan_card" class="d-none" accept="image/*">
                <input type="hidden" name="pan_card_hidden" id="pan_card_hidden"
                    value="{{ $project->customer->pan_card }}">
                <img src="@if ($project->customer->pan_card != null && $project->customer->documents()->find($project->customer->pan_card) != '') {{ $project->customer->documents()->find($project->customer->pan_card)->url }}@else{{ asset('assets/img/placeholder/pan-card.png') }} @endif"
                    alt="" id="choose-customer" style="height: auto;object-fit: cover;"
                    class="rounded w-100 placeholder_img">
            </div>
        </div>
        <div class="col-md-4">
            <label for="aadhar_card_number">Aadhar Card Number</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                <input type="text" class="form-control" id="aadhar_card_number" name="aadhar_card_number"
                    value="{{ $project->customer->aadhar_card_number }}">
                <span class="input-group-text get_aadhar_number cursor-pointer" data-regex="\d{4} \d{4} \d{4}"
                    data-field="#aadhar_card_number" data-progress_bar=".aadhar_ai_progress"
                    data-file="#aadhar_card"><i class="mdi mdi-robot"></i></span>
            </div>
            <div class="mt-2 aadhar_ai_progress progress h-px-20 rounded-pill" style="display: none">
                <div class="progress-bar progress-bar-striped rounded-pill   progress-bar-animated fs-6"
                    role="progressbar" style="width: 0%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <label for="pan_card_number">pan Card Number</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                <input type="text" class="form-control" id="pan_card_number" name="pan_card_number"
                    value="{{ $project->customer->pan_card_number }}">
                <span class="input-group-text get_pan_number cursor-pointer" data-regex="[A-Z]{5}[0-9]{4}[A-Z]"
                    data-field="#pan_card_number" data-progress_bar=".pan_ai_progress" data-file="#pan_card"><i
                        class="mdi mdi-robot"></i></span>
            </div>
            <div class="mt-2 pan_ai_progress progress h-px-20 rounded-pill" style="display: none">
                <div class="progress-bar progress-bar-striped rounded-pill   progress-bar-animated fs-6"
                    role="progressbar" style="width: 0%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>

        <div class="divider">
            <div class="divider-text">Light Bill Details</div>
        </div>
        <div class="col-md-4">
            <div class="border border-dark  p-1 rounded position-relative h-100 document-upload-element"
                id="custome-aadhar-card-image-wraper"
                data-placeholder_url="{{ asset('assets/img/placeholder/light-bill.png') }}">
                <button type="button"
                    class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-close"></span> Remove
                </button>
                <button type="button"
                    class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-plus"></span> Add
                </button>
                <input type="file" name="light_bill" id="light_bill" class="d-none" accept="image/*">
                <input type="hidden" name="light_bill_hidden" id="light_bill_hidden"
                    value="{{ $project->customer->light_bill }}">
                <img src="@if (
                    $project->customer->light_bill != null &&
                        $project->customer->documents()->find($project->customer->light_bill) != '') {{ $project->customer->documents()->find($project->customer->light_bill)->url }}@else{{ asset('assets/img/placeholder/light-bill.png') }} @endif"
                    alt="" id="choose-customer" style="height: auto;object-fit: cover;"
                    class="rounded w-100 placeholder_img">
            </div>
        </div>
        <div class="col-md-6">
            <label for="lightbill_number">Light Bill Number</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                <input type="text" class="form-control" id="lightbill_number" name="lightbill_number"
                    value="{{ $project->customer->lightbill_number }}">
                <span class="input-group-text get_lightbill_number cursor-pointer grouped-lightbill group-regex"
                    data-regex="\d{4} \d{4} \d{4}" data-field="#lightbill_number"
                    data-progress_bar=".lightbill_number_ai_progress" data-file="#light_bill"><i
                        class="mdi mdi-robot"></i></span>
            </div>
            <label for="sanction_load">Sanction Load</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                <input type="text" class="form-control" id="sanction_load" name="sanction_load"
                    value="{{ $project->customer->sanction_load }}">
                <span class="input-group-text get_sanction_load cursor-pointer grouped-lightbill group-regex"
                    data-regex="\s*([0-9]+(?:\.[0-9]+)?)\s*KW" data-field="#sanction_load"
                    data-progress_bar=".lightbill_number_ai_progress" data-file="#light_bill"><i
                        class="mdi mdi-robot"></i></span>
            </div>
            <label for="service_number">Service Number</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                <input type="text" class="form-control" id="service_number" name="service_number"
                    value="{{ $project->customer->service_number }}">
                <span class="input-group-text get_service_number cursor-pointer grouped-lightbill group-regex"
                    data-regex="\d{5} \d{5}" data-field="#service_number"
                    data-progress_bar=".lightbill_number_ai_progress" data-file="#light_bill"><i
                        class="mdi mdi-robot"></i></span>
            </div>
            <div class="mt-2 lightbill_number_ai_progress progress h-px-20 rounded-pill" style="display: none">
                <div class="progress-bar progress-bar-striped rounded-pill   progress-bar-animated fs-6"
                    role="progressbar" style="width: 0%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>

        <div class="divider">
            <div class="divider-text">Bank Details</div>
        </div>
        <div class="col-md-8">
            <div class="border border-dark p-1 rounded position-relative h-100 document-upload-element"
                id="custome-cheque-image-wraper"
                data-placeholder_url="{{ asset('assets/img/placeholder/chaque-2.png') }}">
                <button type="button"
                    class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-close"></span> Remove
                </button>
                <button type="button"
                    class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                    style="bottom: 0px;left: 0px;opacity: 0.5;">
                    <span class="tf-icons mdi mdi-plus"></span> Add
                </button>
                <input type="file" name="cancel_cheque" id="cancel_cheque" class="d-none" accept="image/*">
                <input type="hidden" name="cancel_cheque-hidden" id="cancel_cheque-hidden"
                    value="{{ $project->customer->cancel_cheque }}">
                <img src="@if (
                    $project->customer->cancel_cheque != null &&
                        $project->customer->documents()->find($project->customer->cancel_cheque) != '') {{ $project->customer->documents()->find($project->customer->cancel_cheque)->url }}@else{{ asset('assets/img/placeholder/chaque-2.png') }} @endif"
                    alt="" id="choose-customer-cancel-cheque" style="height: auto;object-fit: cover;"
                    class="rounded w-100 placeholder_img">
            </div>
        </div>
        <div class="col-md-4">
            <label class="col-form-label" for="bank_account_number">Bank Account
                Number</label>
            <div class="">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number"
                        value="{{ @$project->customer->bank_account_number }}">
                    <span class="input-group-text get_bank_account_number cursor-pointer grouped-cheque group-regex"
                        data-regex="\d{9,18}" data-field="#bank_account_number"
                        data-progress_bar=".bank_account_number_ai_progress" data-file="#bank_cheque"><i
                            class="mdi mdi-robot"></i></span>
                </div>
            </div>
            <label class="col-form-label" for="micr_code">MIRC Code</label>
            <div class="">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control" id="micr_code" name="micr_code"
                        value="{{ @$project->customer->micr_code }}">
                    <span class="input-group-text get_micr_code cursor-pointer grouped-cheque group-regex"
                        data-regex="\b\d{9,10}\b" data-field="#micr_code"
                        data-progress_bar=".bank_account_number_ai_progress" data-file="#bank_cheque"><i
                            class="mdi mdi-robot"></i></span>
                </div>
            </div>
            <label class="col-form-label" for="ifsc_code">IFSC Code</label>
            <div class="">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control" id="ifsc_code" name="ifsc_code"
                        value="{{ @$project->customer->ifsc_code }}">
                    <span class="input-group-text get_ifsc_code cursor-pointer grouped-cheque group-regex"
                        data-regex="\b[A-Z]{4}0[A-Z0-9]{6}\b" data-field="#ifsc_code"
                        data-progress_bar=".bank_account_number_ai_progress" data-file="#bank_cheque"><i
                            class="mdi mdi-robot"></i></span>
                </div>
            </div>
            <label class="col-form-label" for="bank_name">Bank Name</label>
            <div class="">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control" id="bank_name" name="bank_name"
                        value="{{ @$project->customer->bank_name }}">
                    <span class="input-group-text get_bank_name cursor-pointer grouped-cheque group-regex"
                        data-regex="" data-field="#bank_name" data-progress_bar=".bank_account_number_ai_progress"
                        data-file="#bank_cheque"><i class="mdi mdi-robot"></i></span>
                </div>
            </div>
            <label class="col-form-label" for="ac_holder_name">A/C Holder Name</label>
            <div class="">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control" id="ac_holder_name" name="ac_holder_name"
                        value="{{ @$project->customer->ac_holder_name }}">
                    <span class="input-group-text get_ac_holder_name cursor-pointer" data-regex="\d{9,18}"
                        data-field="#ac_holder_name" data-progress_bar=".bank_account_number_ai_progress"
                        data-file="#bank_cheque"><i class="mdi mdi-robot"></i></span>
                </div>
            </div>
            <label class="col-form-label" for="branch_name">Branch Name</label>
            <div class="">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control" id="branch_name" name="branch_name"
                        value="{{ @$project->customer->branch_name }}">
                    <span class="input-group-text get_branch_name cursor-pointer grouped-cheque group-regex"
                        data-regex="\b([A-Z\s\-]{5,40}BRANCH)\b" data-field="#branch_name"
                        data-progress_bar=".bank_account_number_ai_progress" data-file="#bank_cheque"><i
                            class="mdi mdi-robot"></i></span>
                </div>
            </div>
            <div class="mt-2 bank_account_number_ai_progress progress h-px-20 rounded-pill" style="display: none">
                <div class="progress-bar progress-bar-striped rounded-pill   progress-bar-animated fs-6"
                    role="progressbar" style="width: 0%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
        <div id="customerNoteWrapper">
            <label for="note">Customer Note</label>
            <textarea name="note" id="customer_note_hidden" class="d-none">{{ $project->customer->note }}</textarea>
            <div class="h-px-250" id="customer_note">{!! @$project->customer->note ?? '' !!}
            </div>
        </div>
        <div class="col-12 text-end">
            <button class="btn btn-outline-warning btn-sm" type="reset">Reset</button>
            <button class="btn btn-sm btn-outline-primary" type="submit">Save Customer
                Data</button>
        </div>
    </div>
</form>
<hr>
