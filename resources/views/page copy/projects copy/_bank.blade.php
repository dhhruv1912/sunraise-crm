<div class="row">

    {{-- LEFT: CANCEL CHEQUE UPLOAD --}}
    <div class="col-7 border border-dark p-1 rounded position-relative document-upload-element"
         data-placeholder_url="{{ asset('assets/admin/img/placeholder/chaque-2.png') }}">

        {{-- Remove Button --}}
        <button type="button"
                class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                style="bottom:0;left:0;opacity:0.5;">
            <span class="tf-icons mdi mdi-close"></span> Remove
        </button>

        {{-- Upload Button --}}
        <button type="button"
                class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                style="bottom:0;left:0;opacity:0.5;">
            <span class="tf-icons mdi mdi-plus"></span> Add
        </button>

        {{-- File Input --}}
        <input type="file" name="cancel_cheque" id="cancel_cheque" class="d-none" accept="image/*">
        <input type="hidden" name="cancel_cheque_hidden" id="cancel_cheque_hidden"
               value="{{ $project->cancel_cheque ?? '' }}">

        {{-- Preview --}}
        <img src="@if($project->cancel_cheque)
                    {{ asset('storage/' . $project->cancel_cheque) }}
                  @else
                    {{ asset('assets/admin/img/placeholder/chaque-2.png') }}
                  @endif"
             class="rounded w-100 placeholder_img"
             style="object-fit:cover;height:auto;">
    </div>

    {{-- RIGHT: BANK DETAILS --}}
    <div class="col-5">

        {{-- Account Number --}}
        <div class="row mb-3">
            <label class="col-form-label" for="bank_account_number">Bank Account Number</label>
            <div>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control"
                           name="bank_account_number"
                           id="bank_account_number"
                           value="{{ $project->bank_account_number }}">
                    <span class="input-group-text get_ocr_data cursor-pointer"
                          data-regex="\d{9,18}"
                          data-field="#bank_account_number"
                          data-progress_bar=".bank_account_ai"
                          data-file="#cancel_cheque">
                        <i class="mdi mdi-robot"></i>
                    </span>
                </div>

                {{-- OCR progress --}}
                <div class="mt-2 bank_account_ai progress h-px-20 rounded-pill" style="display:none;">
                    <div class="progress-bar progress-bar-striped rounded-pill progress-bar-animated fs-6"
                         role="progressbar"
                         style="width:0%">
                    </div>
                </div>
            </div>
        </div>

        {{-- MICR Code --}}
        <div class="row mb-3">
            <label class="col-form-label" for="micr_code">MICR Code</label>
            <div>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control"
                           name="micr_code"
                           id="micr_code"
                           value="{{ $project->micr_code }}">
                    <span class="input-group-text get_ocr_data cursor-pointer"
                          data-regex="\b\d{9}\b"
                          data-field="#micr_code"
                          data-progress_bar=".micr_ai"
                          data-file="#cancel_cheque">
                        <i class="mdi mdi-robot"></i>
                    </span>
                </div>

                {{-- OCR Progress --}}
                <div class="mt-2 micr_ai progress h-px-20 rounded-pill" style="display:none;">
                    <div class="progress-bar progress-bar-striped rounded-pill progress-bar-animated fs-6"
                         role="progressbar"
                         style="width:0%">
                    </div>
                </div>
            </div>
        </div>

        {{-- IFSC Code --}}
        <div class="row mb-3">
            <label class="col-form-label" for="ifsc_code">IFSC Code</label>
            <div>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-numeric"></i></span>
                    <input type="text" class="form-control"
                           name="ifsc_code"
                           id="ifsc_code"
                           value="{{ $project->ifsc_code }}">
                    <span class="input-group-text get_ocr_data cursor-pointer"
                          data-regex="\b[A-Z]{4}0[A-Z0-9]{6}\b"
                          data-field="#ifsc_code"
                          data-progress_bar=".ifsc_ai"
                          data-file="#cancel_cheque">
                        <i class="mdi mdi-robot"></i>
                    </span>
                </div>

                {{-- OCR Progress --}}
                <div class="mt-2 ifsc_ai progress h-px-20 rounded-pill" style="display:none;">
                    <div class="progress-bar progress-bar-striped rounded-pill progress-bar-animated fs-6"
                         role="progressbar"
                         style="width:0%">
                    </div>
                </div>
            </div>
        </div>

        {{-- Bank Name --}}
        <div class="row mb-3">
            <label class="col-form-label" for="bank_name">Bank Name</label>
            <div>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-bank"></i></span>
                    <input type="text" class="form-control"
                           name="bank_name"
                           id="bank_name"
                           value="{{ $project->bank_name }}">
                </div>
            </div>
        </div>

        {{-- A/C Holder Name --}}
        <div class="row mb-3">
            <label class="col-form-label" for="ac_holder_name">A/C Holder Name</label>
            <div>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-account"></i></span>
                    <input type="text" class="form-control"
                           name="ac_holder_name"
                           id="ac_holder_name"
                           value="{{ $project->ac_holder_name }}">
                </div>
            </div>
        </div>

        {{-- Branch Name --}}
        <div class="row mb-3">
            <label class="col-form-label" for="branch_name">Branch Name</label>
            <div>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-home-city"></i></span>
                    <input type="text" class="form-control"
                           name="branch_name"
                           id="branch_name"
                           value="{{ $project->branch_name }}">
                </div>
            </div>
        </div>

    </div>
</div>
