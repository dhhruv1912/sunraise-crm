@extends('temp.common')

@section('title', 'View Lead')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <a href="{{ route('marketing.index') }}" class="btn btn-secondary mb-3">Back</a>

    <div class="card">
        <div class="card-header">
            <h4>Lead — {{ $lead->lead_code }}</h4>
        </div>
        @php
            dump($lead);
        @endphp
        <div class="card-body">

            <dl class="row">
                <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $lead->quoteRequest->name }}</dd>
                <dt class="col-sm-3">Mobile</dt><dd class="col-sm-9">{{ $lead->quoteRequest->number }}</dd>
                <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $lead->quoteRequest->email }}</dd>

                <dt class="col-sm-3">Assigned</dt>
                <dd class="col-sm-9">
                    {{ $lead->assignee->fname . ' ' . $lead->assignee->lname ?? '—' }}
                </dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    {{ \App\Models\Lead::$STATUS[$lead->status] }}
                </dd>

                <dt class="col-sm-3">Next Follow-up</dt>
                <dd class="col-sm-9">
                    {{ $lead->next_followup_at ?? '—' }}
                </dd>

                <dt class="col-sm-3">Remarks</dt>
                <dd class="col-sm-9">{{ $lead->remarks ?? '—' }}</dd>
            </dl>

            <hr>

            <h5>History</h5>
            @include('page.marketing.history', ['history' => $lead->history])
        </div>
    </div>
</div>

@endsection
