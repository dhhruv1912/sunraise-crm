<div class="row g-2">
    <div class="col-md-6">
        <h6>Quote Request Information</h6>
        <hr>
        <dl class="row g-2">
            <div class="col-sm-4">Module</div>
            <div class="col-sm-8">{{ $project->quoteRequest->module }}</div>
            <div class="col-sm-4">KW</div>
            <div class="col-sm-8">{{ $project->quoteRequest->kw }}</div>
            <div class="col-sm-4">MC</div>
            <div class="col-sm-8">{{ $project->quoteRequest->mc }}</div>
            <div class="col-sm-4">Status</div>
            <div class="col-sm-8">{{ $project->quoteRequest->status }}</div>
            <div class="col-sm-4">Date</div>
            <div class="col-sm-8">{{ $project->quoteRequest->created_at }}</div>
            <div class="col-sm-4">Notes</div>
            <div class="col-sm-8">{{ $project->notes }}</div>
        </dl>
        <h6>Finalize</h6>
        <hr>
        <dl class="row g-2">
            <div class="col-sm-4">Price</div>
            <div class="col-sm-8">{{ $project->finalize_price }}</div>
            <div class="col-sm-4">EMI</div>
            <div class="col-sm-8">
                @foreach ($project->emi as $date => $emi)
                    @php
                        $diff = date_diff(date_create(date('Y-m-d')), date_create($date));
                        if ($diff->format('%R') == '+') {
                            $diff = $diff->format('%a');
                            $message = "EMI payment is due in $diff days.";
                            $status = 'info';
                        } else {
                            $diff = $diff->format('%a');
                            $message = "EMI payment was due $diff days ago.";
                            $status = 'warning';
                        }
                        if ($diff == 0 || $diff == '0') {
                            $message = 'EMI payment is due today.';
                            $status = 'success';
                        }
                    @endphp
                    <div class="bg-label-{{ $status }} row mx-0 mb-2">
                        <div class="px-1 col-4">{{ $date }}</div>
                        <div class="px-1 col-8">{{ $emi }}</div><br>
                        <div class="col-12 mb-1">
                            <small><i>
                                    {{ $message }}
                                </i></small>
                        </div>
                    </div>
                @endforeach
            </div>
        </dl>
    </div>
    <div class="col-md-6">
        <h6>Quote Master Information</h6>
        <hr>
        <dl class="row g-2">
            <div class="col-sm-4">SKU</div>
            <div class="col-sm-8" id="modal-sku">{{ optional($project->quoteMaster)->sku }}</div>
            <div class="col-sm-4">Module</div>
            <div class="col-sm-8" id="modal-module">{{ optional($project->quoteMaster)->module }}
            </div>
            <div class="col-sm-4">KW</div>
            <div class="col-sm-8" id="modal-kw">{{ optional($project->quoteMaster)->kw }}</div>
            <div class="col-sm-4">Module Count</div>
            <div class="col-sm-8" id="modal-module_count">
                {{ optional($project->quoteMaster)->module_count }}
            </div>
            <div class="col-sm-4">Value</div>
            <div class="col-sm-8" id="modal-value">{{ optional($project->quoteMaster)->value }}
            </div>
            <div class="col-sm-4">Taxes</div>
            <div class="col-sm-8" id="modal-taxes">{{ optional($project->quoteMaster)->taxes }}
            </div>
            <div class="col-sm-4">Metering Cost</div>
            <div class="col-sm-8" id="modal-metering_cost">
                {{ optional($project->quoteMaster)->metering_cost }}
            </div>
            <div class="col-sm-4">MCB/PPA</div>
            <div class="col-sm-8" id="modal-mcb_ppa">
                {{ optional($project->quoteMaster)->mcb_ppa }}
            </div>
            <div class="col-sm-4 bg-label-info">Payable</div>
            <div class="col-sm-8 bg-label-info" id="modal-payable">
                {{ optional($project->quoteMaster)->payable }}
            </div>
            <div class="col-sm-4 bg-label-warning">Subsidy</div>
            <div class="col-sm-8 bg-label-warning" id="modal-subsidy">
                {{ optional($project->quoteMaster)->subsidy }}</div>
            <div class="col-sm-4 bg-label-success">Projected</div>
            <div class="col-sm-8 bg-label-success" id="modal-projected">
                {{ optional($project->quoteMaster)->projected }}
            </div>
        </dl>
    </div>
</div>
<form action="">
    <h6>Change EMIs</h6>
    <hr>
    <div class="mb-2 mt-5 d-flex">
        <div class="col-md-12">
            <div class="row mx-0 my-1">
                <div class="col-4 my-1">
                    <label class="form-label" for="FinalizePrice">Finalize Price</label>
                    <input type="number" class="form-control" name="FinalizePrice"
                        data-dp="{{ $project->finalize_price }}" disabled value="{{ $project->finalize_price }}"
                        id="FinalizePrice" placeholder="Finalize Price">
                </div>
                <div class="col-3">
                    <label class="form-label" for="FinalizePrice">Edit</label>
                    <button type="button"
                        class="d-block edit-finalize-price btn-sm rounded-circle my-2 my-1 btn btn-icon btn-outline-primary waves-effect">
                        <span class="tf-icons mdi mdi-pen"></span>
                    </button>
                </div>
            </div>
            <div class="row emi-fields mx-0 my-1">
                @if ($project->emi)
                    <div class="col-4 my-1">
                        <label class="form-label" for="EMI1">EMI Amounts</label>
                    </div>
                    <div class="col-4 my-1">
                        <label class="form-label" for="EMI1">EMI Dates</label>
                    </div>
                    <div class="col">
                        <label class="form-label" for="EMI1">Action</label>
                    </div>
                    @php
                        $index = 1;
                    @endphp
                    @foreach ($project->emi as $date => $emi)
                        <div class="col-4 my-1">
                            <input type="number" class="form-control emi-amount" id="EMI{{ $index }}"
                                placeholder="EMI {{ $index }}" value="{{ $emi }}">
                        </div>
                        <div class="col-4 my-1">
                            <input type="date" class="form-control emi-date" id="EMIdate{{ $index }}"
                                placeholder="EMI {{ $index }} Date" value="{{ $date }}">
                        </div>
                        <div class="col d-flex gap-2">
                            <button type="button" data-id="{{ $index }}"
                                class="delete-emi btn-sm rounded-circle my-2 my-1 btn btn-icon btn-outline-danger waves-effect">
                                <span class="tf-icons mdi mdi-delete-empty"></span>
                            </button>
                            @if ($index == 1)
                                <button type="button"
                                    class="d-block btn-sm rounded-circle my-2 my-1 add-emi-field btn btn-icon btn-outline-info waves-effect">
                                    <span class="tf-icons mdi mdi-plus"></span>
                                </button>
                            @endif
                        </div>
                        @php
                            $index++;
                        @endphp
                    @endforeach
                @else
                    <div class="col-4 my-1">
                        <label class="form-label" for="EMI1">EMI Amounts</label>
                        <input type="number" class="form-control emi-amount" id="EMI1" placeholder="EMI 1">
                    </div>
                    <div class="col-4 my-1">
                        <label class="form-label" for="EMI1">EMI Dates</label>
                        <input type="date" class="form-control emi-date" id="EMIdate1" placeholder="EMI 1 Date">
                    </div>
                    <div class="col">
                        <label class="form-label" for="EMI1">Action</label>
                        <button type="button"
                            class="d-block btn-sm rounded-circle my-2 my-1 add-emi-field btn btn-icon btn-outline-info waves-effect">
                            <span class="tf-icons mdi mdi-plus"></span>
                        </button>
                    </div>
                @endif
            </div>
            <div class="row emi-fields mx-0 my-1">
                <div class="col-4 my-1"><label class="form-label">Total EMI Amount</label></div>
                <div class="col-6 my-1">
                    <div class="text-success" id="emisetAmount">{{ $project->finalize_price }}</div>
                </div>
                <div class="col-4 my-1"><label class="form-label">EMI Amount Remaining</label></div>
                <div class="col-6 my-1">
                    <div class="text-warning" id="emiunsetAmount">0</div>
                </div>
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-outline-warning btn-sm" type="reset">Reset</button>
                <button class="btn btn-sm btn-outline-primary" id="saveEmiData" data-project-id="{{ $project->id }}"
                    type="button">Save Emi Data</button>
            </div>
        </div>
        
    </div>
</form>
<div class="mb-2 mt-5">
    <h6>Quotetions</h6>
    <hr>
    <div class="row mx-0 ">
        @foreach ($project->lead->quotation as $ix => $quotation)
            <div class="col-xl-6 mb-3">
                <div class="bg-label-secondary px-3">
                    <div class="col-12 my-2 pt-2 d-flex align-items-baseline justify-content-between">
                        <h6>{{ $ix + 1 }}. {{ $quotation->quotation_no }}</h6><a
                            href="/quotations/{{ $quotation->id }}" class="btn btn-sm btn-info">Go to
                            Quotations</a>
                    </div>
                    <hr>
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr class="row">
                                <td class="col-4">Sent</td>
                                <td class="col-6">{{ $quotation->sentBy ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr class="row">
                                <td class="col-4">Sent By</td>
                                <td class="col-6">{{ $quotation->sentBy->fname }} {{ $quotation->sentBy->lname }}
                                </td>
                            </tr>
                            <tr class="row">
                                <td class="col-4">Sent At</td>
                                <td class="col-6">{{ $quotation->sent_at }}</td>
                            </tr>
                            <tr class="row">
                                <td class="col-12 collapsed d-flex justify-content-between" data-bs-toggle="collapse"
                                    data-bs-target="#pdf{{ $quotation->id }}" aria-expanded="false"
                                    aria-controls="pdf{{ $quotation->id }}">
                                    <span>File</span>
                                    <span class="mdi mdi-chevron-down"></span>
                                </td>
                                <td class="col-12 collapse" id="pdf{{ $quotation->id }}"><embed
                                        src="{{ asset('storage/' . $quotation->pdf_path) }}" width="100%"
                                        class="border border-3 rounded-3 h-100" style="min-height: 800px;">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        @endforeach
    </div>
</div>