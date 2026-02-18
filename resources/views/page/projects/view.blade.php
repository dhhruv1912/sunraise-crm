@extends('temp.common')

@section('title', 'Project')
@section('head')
<style>
    .crm-stepper {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: .5rem;
    }

    .crm-step {
        min-width: 140px;
        text-align: center;
        position: relative;
    }

    .crm-step .dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin: 0 auto .5rem;
        background: #dee2e6;
    }

    .crm-step.done .dot {
        background: var(--bs-success);
    }

    .crm-step.current .dot {
        background: var(--bs-primary);
    }

    .crm-step .label {
        font-size: .8rem;
        font-weight: 500;
    }

    .crm-step .date {
        font-size: .7rem;
        color: #6c757d;
    }

    .milestone {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .6rem .8rem;
        border-radius: .5rem;
        margin-bottom: .4rem;
        background: #f8f9fa;
    }

    .milestone.done {
        background: #ebfbee;
    }

    .milestone.overdue {
        background: #fff5f5;
    }

    .milestone .title {
        font-weight: 500;
        font-size: .9rem;
    }

    .doc-card {
        border: 1px dashed #dee2e6;
        border-radius: .5rem;
        padding: .6rem;
        background: #fff;
        text-align: center;
    }

    .doc-card.done {
        border-style: solid;
        border-color: #adb5bd;
        background: #f8f9fa;
    }

    .doc-title {
        font-size: .8rem;
        font-weight: 500;
        margin-bottom: .4rem;
    }

    .doc-preview img {
        max-width: 100%;
        max-height: 90px;
        border-radius: .25rem;
        cursor: pointer;
    }

    .doc-missing {
        font-size: .75rem;
        color: #adb5bd;
        margin: 1rem 0;
    }

    .doc-card {
        border: 1px dashed #dee2e6;
        border-radius: .6rem;
        padding: .6rem;
        background: #fff;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .doc-card.done {
        border-style: solid;
        background: #f8f9fa;
    }

    .doc-title {
        font-size: .8rem;
        font-weight: 600;
        margin-bottom: .4rem;
        text-align: center;
    }

    .doc-body {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .doc-grid {
        display: flex;
        gap: .4rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .doc-preview {
        width: 64px;
        height: 64px;
        border-radius: .4rem;
        border: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: #fff;
    }

    .doc-preview.single {
        width: 100%;
        height: 90px;
    }

    .doc-preview img {
        max-width: 100%;
        max-height: 100%;
        border-radius: .3rem;
    }

    .doc-empty {
        font-size: .75rem;
        color: #adb5bd;
        text-align: center;
    }

    .doc-footer {
        margin-top: .5rem;
    }

    .crm-chip-action.paid {
        background: #ebfbee;
        color: #2b8a3e;
    }

    .crm-chip-action.overdue {
        background: #fff5f5;
        color: #c92a2a;
    }

    .crm-chip-action.upcoming {
        background: #e7f5ff;
        color: #1971c2;
    }

    .crm-timeline {
        display: flex;
        flex-direction: column;
        gap: .8rem;
    }

    .timeline-item {
        display: flex;
        gap: .6rem;
        align-items: flex-start;
    }

    .timeline-item .icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #495057;
        flex-shrink: 0;
    }

    .timeline-item .content .title {
        font-size: .85rem;
        font-weight: 500;
    }

    .timeline-item .meta {
        font-size: .7rem;
        color: #868e96;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0">{{ $project->project_code }}</h4>
                <div class="text-muted small">
                    {{ $project->customer?->name }}
                </div>
            </div>
            @can('project.edit')
                <a href="{{ route('projects.view.edit', $project->id) }}"
                class="btn btn-outline-primary">
                    Edit Project
                </a>
            @endcan
        </div>

        {{-- WIDGETS --}}
        <div class="row g-3 position-relative crm-section" id="projectWidgets" style="max-height:1000px;overflow:auto;min-height: 100px">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>

        {{-- MAIN GRID --}}
        <div class="row g-3" style="height: calc(100vh - 400px);">

            <div class="col-lg-8 h-100">
                <div class="crm-section position-relative" id="projectStatus" style="max-height:1000px;overflow:auto;min-height: 25%"></div>
                <div class="crm-section mt-2 position-relative" id="projectTimeline" style="max-height:1000px;overflow:auto;min-height: 40%"></div>
                <div class="crm-section mt-2 position-relative" id="projectDocuments" style="max-height:1000px;overflow:auto;min-height: 35%"></div>
            </div>

            <div class="col-lg-4">
                {{-- <div class="crm-section position-relative" id="projectBilling" style="max-height:1000px;overflow:auto;min-height: 100px"></div> --}}
                @can('project.edit')
                    <div class="crm-section mt-2 position-relative" id="projectEmi" style="max-height:1000px;overflow:auto;min-height: 50%"></div>
                @endcan
                <div class="crm-section mt-2 position-relative" id="projectActivities" style="max-height:1000px;overflow:auto;min-height: 50%"></div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const PROJECT_ID = {{ $project->id }};
    const DASHBOARD_URL = "{{ route('projects.ajax.dashboard', $project->id) }}";
    const DASHBOARD_STATUS_URL = "{{ route('projects.ajax.status', $project->id) }}";
    const DOC_UPLOAD_URL_CUSTOMER = "{{ route('documents.ajax.uploadCustomer') }}"
    const DOC_UPLOAD_URL_PROJECT = "{{ route('documents.ajax.uploadProject') }}"
    const EMI_PAY_URL = "{{ route('projects.ajax.emi.pay', $project->id) }}";
</script>
<script src="{{ asset('assets/js/page/projects/view.js') }}"></script>
@endpush
