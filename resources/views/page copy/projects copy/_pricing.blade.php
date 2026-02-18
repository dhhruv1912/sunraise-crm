<div class="row">

    {{-- LEFT SIDE: QUOTE MASTER VALUES --}}
    <div class="col-7">

        {{-- VALUE --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Value</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->value ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="value"
                           value="{{ $project->value ?? '' }}">
                </div>
            </div>
        </div>

        {{-- TAXES --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Taxes</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->taxes ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="taxes"
                           value="{{ $project->taxes ?? '' }}">
                </div>
            </div>
        </div>

        {{-- METERING COST --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Metering Cost</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->metering_cost ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="metering_cost"
                           value="{{ $project->metering_cost ?? '' }}">
                </div>
            </div>
        </div>

        {{-- MCB / PPA --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">MCB / PPA</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->mcb_ppa ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="mcb_ppa"
                           value="{{ $project->mcb_ppa ?? '' }}">
                </div>
            </div>
        </div>

        {{-- PAYABLE --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Payable</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->payable ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="payable"
                           value="{{ $project->payable ?? '' }}">
                </div>
            </div>
        </div>

        {{-- SUBSIDY --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Subsidy</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->subsidy ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="subsidy"
                           value="{{ $project->subsidy ?? '' }}">
                </div>
            </div>
        </div>

        {{-- PROJECTED --}}
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Projected</label>
            <div class="col-sm-5">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="text" disabled class="form-control" value="{{ $project->projected ?? '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-pencil"></i></span>
                    <input type="number" class="form-control"
                           name="projected"
                           value="{{ $project->projected ?? '' }}">
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT SIDE: FINAL PRICE & EMI --}}
    <div class="col-5">

        {{-- FINALIZE PRICE --}}
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Final Price</label>
            <div class="col-sm-8">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="number" class="form-control"
                           name="finalize_price"
                           value="{{ $project->finalize_price ?? '' }}">
                </div>
            </div>
        </div>

        {{-- EMI --}}
        <div class="row mb-3">
            <label class="col-sm-12 col-form-label">EMI Schedule</label>

            @php
                $emiList = $project->emi ? json_decode($project->emi, true) : [];
            @endphp

            @if(!empty($emiList))
                @foreach($emiList as $date => $amount)
                    <div class="col-7 mb-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            <input type="date" class="form-control" name="emi_date[]" value="{{ $date }}">
                        </div>
                    </div>
                    <div class="col-5 mb-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                            <input type="number" class="form-control" name="emi_amount[]" value="{{ $amount }}">
                        </div>
                    </div>
                @endforeach
            @else
                {{-- First EMI Field --}}
                <div class="col-7 mb-2">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                        <input type="date" class="form-control" name="emi_date[]" value="">
                    </div>
                </div>
                <div class="col-5 mb-2">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                        <input type="number" class="form-control" name="emi_amount[]" value="">
                    </div>
                </div>
            @endif

            <div class="col-12 mt-2">
                <button type="button" class="btn btn-sm btn-primary" id="add-emi-row">
                    + Add EMI Row
                </button>
            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const container = document.querySelector("#tab-pricing");

    document.getElementById("add-emi-row").addEventListener("click", () => {
        let row = `
            <div class="col-7 mb-2">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                    <input type="date" class="form-control" name="emi_date[]" value="">
                </div>
            </div>
            <div class="col-5 mb-2">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-currency-rupee"></i></span>
                    <input type="number" class="form-control" name="emi_amount[]" value="">
                </div>
            </div>
        `;
        container.querySelector(".row.mb-3:last-child").insertAdjacentHTML("beforebegin", row);
    });
});
</script>
