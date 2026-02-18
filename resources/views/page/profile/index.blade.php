@extends('temp.common')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- HEADER --}}
        <div class="mb-2">
            <h4 class="mb-0">
                <i class="fa-solid fa-user me-2"></i>
                My Profile
            </h4>
            <div class="text-muted small">
                Personal details & assignments
            </div>
        </div>

        {{-- ================= WEEKLY TIMELINE ================= --}}
        <div class="row g-3 mt-2 position-relative" id="profileTimeline" style="min-height: 100px">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>
        {{-- ================= USER INFO ================= --}}
        <div class="crm-section">
            <div class="fw-semibold mb-2">
                <i class="fa-solid fa-id-card me-1"></i>
                Basic Information
            </div>

            <div class="row g-3 align-items-center">

                <div class="col-md-2 text-center">
                    <img src="https://api.dicebear.com/7.x/adventurer-neutral/svg?seed={{ auth()->user()->fname }}+{{ auth()->user()->lname }}"
                        class="rounded-circle"
                        style="width:80px;height:80px;object-fit:cover">
                </div>

                <div class="col-md-10">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">
                                {{ auth()->user()->fname }} {{ auth()->user()->lname }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">
                                {{ auth()->user()->email ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Mobile</div>
                            <div class="fw-semibold">
                                {{ auth()->user()->mobile ?? '—' }}
                            </div>
                        </div>
                        {{-- @dump(auth()->user(),$role) --}}
                        <div class="col-md-3">
                            <div class="text-muted small">Role</div>
                            <div class="fw-semibold">
                                {{ $role }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        {{-- ================= ASSIGNMENT SUMMARY ================= --}}
        <div class="crm-section mt-2">
            <div class="fw-semibold mb-2">
                <i class="fa-solid fa-diagram-project me-1"></i>
                Assignment Overview
            </div>

            <div class="row g-3 text-center">

                <div class="col-md-4">
                    <div class="fs-3 fw-bold text-primary">{{ $leadCount }}</div>
                    <div class="text-muted small">Leads Assigned</div>
                </div>

                <div class="col-md-4">
                    <div class="fs-3 fw-bold text-warning">{{ $quoteCount }}</div>
                    <div class="text-muted small">Quote Requests</div>
                </div>

                <div class="col-md-4">
                    <div class="fs-3 fw-bold text-success">{{ $projectCount }}</div>
                    <div class="text-muted small">Active Projects</div>
                </div>

            </div>
        </div>
        {{-- ================= ASSIGNED Requests ================= --}}
        <div class="crm-section mt-2">
            <div class="fw-semibold mb-2">Assigned Quote Requests</div>
            <div class="crm-table-wrapper">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Quote Request</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quoteRequests as $qr)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>
                                            {{ $qr->customer->name }} (<span class="fst-italic">{{ $qr->type }}</span>)
                                        </span>
                                        <span>
                                            {{ $qr->customer->mobile }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $qr->customer->email }}</td>
                                <td>{{ str_replace('_',' ',$qr->status) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('quote_requests.view.show', $qr->id) }}"
                                       class="btn btn-sm btn-light">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No leads assigned
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ================= ASSIGNED LEADS ================= --}}
        <div class="crm-section mt-2">
            <div class="fw-semibold mb-2">Assigned Leads</div>

            <div class="crm-table-wrapper">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Lead</th>
                            <th>Status</th>
                            <th>Next Follow-up</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $l)
                            <tr>
                                <td>{{ $l->lead_code }}</td>
                                <td>{{ str_replace('_',' ',$l->status) }}</td>
                                <td>{{ optional($l->next_followup_at)->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('leads.view.show', $l->id) }}"
                                       class="btn btn-sm btn-light">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No leads assigned
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ================= ASSIGNED PROJECTS ================= --}}
        <div class="crm-section mt-2">
            <div class="fw-semibold mb-2">Assigned Projects</div>

            <div class="crm-table-wrapper">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $p)
                            <tr>
                                <td>{{ $p->project_code }}</td>
                                <td>{{ str_replace('_',' ',$p->status) }}</td>
                                <td>{{ ucfirst($p->priority) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('projects.view.edit', $p->id) }}"
                                       class="btn btn-sm btn-light">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No projects assigned
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const BASIC_INFO_URL = "{{ route('profile.ajax.basic-info') }}";
    const ASSIGNMENTS_URL = "{{ route('profile.ajax.assignments') }}";
    const TIMELINE_URL = "{{ route('profile.ajax.timeline') }}";

    document.addEventListener('DOMContentLoaded', () => {
        loadProfileWidgets();
    });
</script>
<script src="{{ asset('assets/js/page/profile/index.js') }}"></script>
@endpush