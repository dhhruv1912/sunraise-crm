<div class="col-md-4">
    <div class="crm-section h-100">

        <div class="fw-semibold mb-2">
            <i class="fa-solid fa-diagram-project me-1"></i>
            My Assignments
        </div>

        <div class="row g-3">

            <div class="col-4 text-center">
                <div class="fs-4 fw-bold text-primary">
                    {{ $leads }}
                </div>
                <div class="text-muted small">
                    Leads
                </div>
            </div>

            <div class="col-4 text-center">
                <div class="fs-4 fw-bold text-warning">
                    {{ $quotes }}
                </div>
                <div class="text-muted small">
                    Quotes
                </div>
            </div>

            <div class="col-4 text-center">
                <div class="fs-4 fw-bold text-success">
                    {{ $projects }}
                </div>
                <div class="text-muted small">
                    Projects
                </div>
            </div>

        </div>

        <div class="mt-3 d-grid gap-1">
            <a href="{{ route('leads.view.list') }}"
               class="btn btn-sm btn-light">
                View Leads
            </a>
            <a href="{{ route('projects.view.list') }}"
               class="btn btn-sm btn-light">
                View Projects
            </a>
        </div>

    </div>
</div>
