@extends('temp.common')

@section('title', 'Projects')
@section('head')
    <style>
        .crm-radial {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background:
        conic-gradient(
            var(--bs-primary) calc(var(--value) * 1%),
            #e9ecef 0
        );
    display: grid;
    place-items: center;
    margin: auto;
}

.crm-radial span {
    width: 80px;
    height: 80px;
    background: #fff;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 1.25rem;
    font-weight: 600;
}
.crm-alert {
    display: flex;
    justify-content: space-between;
    padding: .5rem .75rem;
    border-radius: .5rem;
    margin-bottom: .4rem;
    font-size: .85rem;
}

.crm-alert.danger { background:#fdecec; color:#b02a37; }
.crm-alert.warning { background:#fff4e5; color:#b58105; }
.crm-alert.secondary { background:#f1f3f5; color:#495057; }
.crm-chip {
    display: flex;
    justify-content: space-between;
    padding: .4rem .6rem;
    border-radius: 1rem;
    background: #eef2ff;
    color: #364fc7;
    font-size: .8rem;
    margin-bottom: .4rem;
}
.crm-chip-action {
    display:inline-block;
    padding:.25rem .6rem;
    border-radius:1rem;
    font-size:.75rem;
    font-weight:500;
}
.crm-chip-action.danger { background:#fdecec; color:#b02a37; }
.crm-chip-action.warning { background:#fff4e5; color:#b58105; }
.crm-chip-action.secondary { background:#f1f3f5; color:#495057; }
.crm-chip-action.info { background:#e7f5ff; color:#1971c2; }
.crm-chip-action.success { background:#ebfbee; color:#2b8a3e; }


    </style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-1">
                    <i class="fa-solid fa-diagram-project me-2"></i>
                    Projects
                </h4>
                <div class="text-muted small">
                    Execution & billing tracking
                </div>
            </div>
        </div>

        {{-- WIDGETS --}}
        <div class="row g-3 mt-2 position-relative" id="projectWidgets" style="min-height: 100px">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="crm-section mt-2">
            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All</option>
                        <option value="new">New</option>
                        <option value="installation_started">Installation</option>
                        <option value="complete">Completed</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Priority</label>
                    <select id="filterPriority" class="form-select">
                        <option value="">All</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" id="searchBox"
                           class="form-control"
                           placeholder="Project / Customer">
                </div>

                <div class="col-md-2 text-end">
                    <button class="btn btn-primary w-100"
                            onclick="loadProjects(1)">
                        <i class="fa-solid fa-filter me-1"></i>
                        Apply
                    </button>
                </div>

            </div>
        </div>

        {{-- TABLE --}}
        <div class="crm-section mt-3">
            <div class="crm-table-wrapper position-relative">

                <table class="table crm-table mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Billing</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="projectTable">
                        @for($i=0;$i<5;$i++)
                            <tr>
                                <td colspan="6">
                                    <div class="crm-skeleton"></div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div id="projectLoader"
                     class="crm-loader-overlay d-none">
                    <div class="crm-spinner"></div>
                </div>

            </div>

            <div class="d-flex justify-content-end mt-2"
                 id="projectPagination"></div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const PROJECT_LIST_URL   = "{{ route('projects.ajax.list') }}";
    const PROJECT_WIDGET_URL = "{{ route('projects.ajax.widgets') }}";
</script>
<script src="{{ asset('assets/js/page/projects/list.js') }}"></script>
@endpush
