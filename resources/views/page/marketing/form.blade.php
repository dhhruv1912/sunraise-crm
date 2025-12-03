@extends('temp.common')

@section('title', isset($lead) ? 'Edit Lead' : 'Create Lead')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>{{ isset($lead) ? 'Edit Lead' : 'Create Lead' }}</h4>
        </div>

        <form action="{{ isset($lead) ? route('marketing.update', $lead->id) : route('marketing.store') }}"
              method="POST">
            @csrf

            <div class="card-body row g-3">

                <div class="col-md-4">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $lead->quoteRequest->name ?? '') }}" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Mobile</label>
                    <input type="text" name="number" value="{{ old('number', $lead->quoteRequest->number ?? '') }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Assigned To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- None --</option>
                        @foreach(\App\Models\User::all() as $u)
                            <option value="{{ $u->id }}" @selected(($lead->assigned_to ?? '') == $u->id)>
                                {{ $u->fname }} {{ $u->lname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        @foreach(\App\Models\Lead::$STATUS as $key => $label)
                            <option value="{{ $key }}" @selected(($lead->status ?? 'new') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Next Follow-up</label>
                    <input type="datetime-local" name="next_followup_at"
                           value="{{ old('next_followup_at', isset($lead) ? $lead->next_followup_at : '') }}"
                           class="form-control">
                </div>

                <div class="col-md-4 mb-3 position-relative">
                    <label>Select Customer</label>
                    <input type="text" class="form-control" id="customerSearch" autocomplete="off">
                    <input type="hidden" name="customer_id" id="customer_id">
                </div>

                <div class="col-md-12">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control">{{ old('remarks', $lead->remarks ?? '') }}</textarea>
                </div>

            </div>

            <div class="card-footer text-end">
                <button class="btn btn-primary">{{ isset($lead) ? 'Update' : 'Create' }}</button>
            </div>
        </form>

    </div>
</div>
<script src="{{ asset('assets/js/page/customers-search.js') }}"></script>
<script>
customerSearch("customerSearch","customer_id", function(c){
    document.getElementById("lead_name").value = c.name;
    document.getElementById("lead_email").value = c.email;
    document.getElementById("lead_mobile").value = c.mobile;
    document.getElementById("lead_address").value = c.address;
});
</script>
@endsection
