{{-- resources/views/page/quote_requests/view_wrapper.blade.php --}}
@extends('temp.common') {{-- or @extends('temp.common') / your layout --}}

@section('title', 'Quote Request View')

@section('content')

    <div class="card" id="contentCard">

        {{-- @dump(['data' => $data->toArray(), 'history' => $history->toArray(), 'master' => $master->toArray(), 'users' => $users->toArray(), 'projects' => $projects]) --}}
        <div class="card-body">
            <h5 class="card-title d-flex align-items-center justify-content-between">
                <div class="">
                    Request — #{{ $data->id }}
                    <span class="badge rounded-pill bg-label-primary ms-2" id="inv-status">{{ $data->status }}</span>
                </div>
                <div class="d-flex gap-1">
                    <select name="quote_master_id" id="quote_master_id" class="form-select w-auto">
                        <option value="">-- Select Quote Master --</option>
                        @foreach ($master as $masterItem)
                            <option value="{{ $masterItem->id }}"
                                {{ optional($data->quoteMaster)->id == $masterItem->id ? 'selected' : '' }}>{{ $masterItem->sku }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-icon btn-primary d-none" id="updateQuoteMasterButton">
                        <span class="icon-base mdi mdi-content-save icon-22px"></span>
                    </button>
                </div>
            </h5>
            <hr>
            <div class="row g-2">
                <div class="col-md-6">
                    <h6>Quote Request Information</h6>
                    <dl class="row g-2">
                        <div class="col-sm-4">Name</div>
                        <div class="col-sm-8">{{ $data->customer->name }}</div>
                        <div class="col-sm-4">Mobile</div>
                        <div class="col-sm-8">{{ $data->customer->mobile }}</div>
                        <div class="col-sm-4">Email</div>
                        <div class="col-sm-8">{{ $data->customer->email }}</div>
                        <div class="col-sm-4">Module</div>
                        <div class="col-sm-8">{{ $data->module }}</div>
                        <div class="col-sm-4">KW</div>
                        <div class="col-sm-8">{{ $data->kw }}</div>
                        <div class="col-sm-4">MC</div>
                        <div class="col-sm-8">{{ $data->mc }}</div>
                        <div class="col-sm-4">Status</div>
                        <div class="col-sm-8">{{ $data->status }}</div>
                        <div class="col-sm-4">Assigned</div>
                        <div class="col-sm-8">{{ $data->assignedUser->fname }} {{ $data->assignedUser->lname }}</div>
                        <div class="col-sm-4">Notes</div>
                        <div class="col-sm-8">{{ $data->notes }}</div>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h6>Quote Master Information</h6>
                    <dl class="row g-2">
                        <div class="col-sm-4">SKU</div>
                        <div class="col-sm-8" id="modal-sku">{{ optional($data->quoteMaster)->sku }}</div>
                        <div class="col-sm-4">Module</div>
                        <div class="col-sm-8" id="modal-module">{{ optional($data->quoteMaster)->module }}</div>
                        <div class="col-sm-4">KW</div>
                        <div class="col-sm-8" id="modal-kw">{{ optional($data->quoteMaster)->kw }}</div>
                        <div class="col-sm-4">Module Count</div>
                        <div class="col-sm-8" id="modal-module_count">{{ optional($data->quoteMaster)->module_count }}</div>
                        <div class="col-sm-4">Value</div>
                        <div class="col-sm-8" id="modal-value">{{ optional($data->quoteMaster)->value }}</div>
                        <div class="col-sm-4">Taxes</div>
                        <div class="col-sm-8" id="modal-taxes">{{ optional($data->quoteMaster)->taxes }}</div>
                        <div class="col-sm-4">Metering Cost</div>
                        <div class="col-sm-8" id="modal-metering_cost">{{ optional($data->quoteMaster)->metering_cost }}</div>
                        <div class="col-sm-4">MCB/PPA</div>
                        <div class="col-sm-8" id="modal-mcb_ppa">{{ optional($data->quoteMaster)->mcb_ppa }}</div>
                        <div class="col-sm-4 bg-label-info">Payable</div>
                        <div class="col-sm-8 bg-label-info" id="modal-payable">{{ optional($data->quoteMaster)->payable }}</div>
                        <div class="col-sm-4 bg-label-warning">Subsidy</div>
                        <div class="col-sm-8 bg-label-warning" id="modal-subsidy">{{ optional($data->quoteMaster)->subsidy }}</div>
                        <div class="col-sm-4 bg-label-success">Projected</div>
                        <div class="col-sm-8 bg-label-success" id="modal-projected">{{ optional($data->quoteMaster)->projected }}
                        </div>
                    </dl>
                </div>
            </div>
            {{-- <h6 class="card-subtitle">Support card subtitle</h6> --}}
            <h6>Projects Information</h6>
            <table class="table table-sm">
                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                </tr>
                @if ($projects && count($projects) > 0)
                    <tr>
                        <td colspan="4">/* ADD COMPLETD PROJECT LIST */</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4">No Project Found</td>
                    </tr>
                @endif
            </table>

            <hr>
            <h6>History</h6>
            @if ($history && count($history) > 0)
                @foreach ($history as $h)
                    <div class="list-group-item list-group-item-action mb-1 border-bottom">
                        <div class="d-flex w-100 justify-content-between">
                            <small class="text-muted">{{ $h['user'] ?? 'System' }}</small>
                            <small class="text-muted">{{ $h['datetime'] ?? 'System' }}</small>
                        </div>
                        <div class="mt-1">{{ $h['message'] ?? 'System' }}</div>
                    </div>
                @endforeach
            @else
                <div class="text-muted small">No history</div>
            @endif
            </table>


            <hr>
            <div class="d-flex gap-2 justify-content-end">
                <button id="sendMailBtn" class="btn btn-outline-primary btn-sm">Send Mail</button>
                <button id="convertToLeadBtn" class="btn btn-success btn-sm">Convert to Lead</button>
                <a href="{{ route('quote_requests.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
        QUOTE_MASTERS = @json($master);
        QUOTE_MASTER_ID = {{ optional($data->quoteMaster)->id }}
        QUOTE_REQUEST_ID = {{ $data->id }};
        const MASTERS = {}
        Object.entries(QUOTE_MASTERS).map((QM) => {
            MASTERS[QM[1].id] = QM[1]
        })
    </script>
    <script src="{{ asset('assets/js/page/quote_requests_view.js') }}"></script>
@endsection
