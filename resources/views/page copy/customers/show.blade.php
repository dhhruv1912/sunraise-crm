@extends('temp.common')

@section('content')
@php
    // $customer comes from CustomersController@show
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>{{ $customer->name }} @if($customer->mobile) <small class="text-muted">({{ $customer->mobile }})</small> @endif</h4>

        <div>
            <a href="{{ route('customers.create') }}" class="btn btn-outline-secondary btn-sm">New Customer</a>

            <!-- Quick-create project: redirects to projects.create with query params -->
            <a href="{{ route('customers.create_project', $customer->id) }}" class="btn btn-success btn-sm">Create Project</a>

            <!-- Create Lead: fetch JSON and open lead modal (JS handles) -->
            <button class="btn btn-primary btn-sm" id="btn-create-lead" data-id="{{ $customer->id }}">Create Lead</button>
        </div>
    </div>

    <div class="card-body">
        <div class="nav nav-pills mb-3">
            <a class="nav-link active" data-bs-toggle="pill" href="#tab-details">Details</a>
            <a class="nav-link" data-bs-toggle="pill" href="#tab-notes">Notes</a>
            <a class="nav-link" data-bs-toggle="pill" href="#tab-activity">Activity</a>
        </div>

        <div class="tab-content">
            <!-- DETAILS -->
            <div class="tab-pane fade show active" id="tab-details">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <div class="form-control-plaintext">{{ $customer->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <div class="form-control-plaintext">{{ $customer->mobile }}</div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Email</label>
                        <div class="form-control-plaintext">{{ $customer->email }}</div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Address</label>
                        <div class="form-control-plaintext">{{ $customer->address }}</div>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">Notes</label>
                        <div class="form-control-plaintext">{!! nl2br(e($customer->note ?? '—')) !!}</div>
                    </div>
                </div>
            </div>

            <!-- NOTES -->
            <div class="tab-pane fade" id="tab-notes">
                <div class="mb-3">
                    <label class="form-label">Add Note</label>
                    <textarea id="customer-note-text" class="form-control" rows="4" placeholder="Add quick note..."></textarea>
                    <div class="mt-2 d-flex justify-content-between">
                        <small class="text-muted">Notes are visible to internal users only.</small>
                        <div>
                            <button class="btn btn-secondary btn-sm" id="btn-clear-note">Clear</button>
                            <button class="btn btn-primary btn-sm" id="btn-save-note" data-id="{{ $customer->id }}">Save Note</button>
                        </div>
                    </div>
                </div>

                <hr>

                <div id="notes-list" data-customer-id="{{ $customer->id }}">
                    @if(isset($notes) && $notes->count())
                        @foreach($notes as $note)
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <div><strong>{{ $note->user->name ?? 'System' }}</strong> <small class="text-muted">— {{ $note->created_at->diffForHumans() }}</small></div>
                                    </div>
                                    <div class="mt-1">{!! nl2br(e($note->note)) !!}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted">No notes yet.</div>
                    @endif
                </div>

                <div class="text-center mt-2">
                    <button id="btn-load-more-notes" class="btn btn-outline-primary btn-sm">Load More</button>
                </div>
            </div>

            <!-- ACTIVITY -->
            <div class="tab-pane fade" id="tab-activity">
                <div id="activity-timeline" data-customer-id="{{ $customer->id }}">
                    @if(isset($activities) && $activities->count())
                        @foreach($activities as $act)
                            <div class="mb-2">
                                <small class="text-muted">{{ $act->created_at }}</small>
                                <div class="p-2 border rounded mt-1">
                                    <div><strong>{{ $act->user->name ?? 'System' }}</strong> — <small>{{ $act->action }}</small></div>
                                    <div class="mt-1">{!! e($act->message) !!}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted">No activity yet.</div>
                    @endif
                </div>

                <div class="text-center mt-2">
                    <button id="btn-load-more-activities" class="btn btn-outline-primary btn-sm">Load More</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lead modal placeholder (JS will optionally render) -->
<div id="leadModalContainer"></div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/page/customers.js') }}"></script>
@endsection
