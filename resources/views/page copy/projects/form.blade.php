@extends('temp.common')
@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/calender.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/page/project.edit.css') }}">
@endsection
@section('content')
    <div class="card">
        <div class="card-header project-header">

            <div class="project-header-left">
                <h3 class="project-title">
                    {{ isset($project) ? 'Project #' . $project->project_code : 'New Project' }}
                </h3>
                <span class="project-subtitle">
                    {{ isset($project) ? 'Manage all project information and workflow' : 'Create a new project entry' }}
                </span>
            </div>

            @if(isset($project))
            <div class="project-header-right">
                <span class="project-badge {{ $project->is_on_hold ? 'badge-hold' : 'badge-active' }}">
                    {{ $project->is_on_hold ? 'On Hold' : 'Active' }}
                </span>
            </div>
            @endif

        </div>
        <div class="card-body">
            <div class="project-control-bar mb-3" id="subMenuBar">
                @include('page.projects._menu')
            </div>
            <div class="nav-align-top">
                <div class="project-tabs">
                    <ul class="nav project-tab-list" role="tablist" id="projectTabList">

                        @php
                            $tabs = [
                                ['id' => 'project', 'icon' => 'mdi-solar-power-variant-outline', 'label' => 'Project'],
                                ['id' => 'customer', 'icon' => 'mdi-account-tie', 'label' => 'Customer'],
                                ['id' => 'quote', 'icon' => 'mdi-email-fast-outline', 'label' => 'Quote'],
                                ['id' => 'billing', 'icon' => 'mdi-wallet-outline', 'label' => 'Billing'],
                                ['id' => 'documents', 'icon' => 'mdi-file-multiple', 'label' => 'Documents'],
                                ['id' => 'notes', 'icon' => 'mdi-calendar-text-outline', 'label' => 'Notes'],
                                ['id' => 'history', 'icon' => 'mdi-clipboard-text-clock', 'label' => 'History'],
                            ];
                        @endphp

                        @foreach ($tabs as $i => $tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $i === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                    data-bs-target="#tab-{{ $tab['id'] }}" type="button" role="tab">

                                    <i class="mdi {{ $tab['icon'] }}"></i>
                                    <span>{{ $tab['label'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="tab-content project-tab-content">

                    <div class="tab-pane fade show active" id="tab-project">@include('page.projects._project')</div>
                    <div class="tab-pane fade" id="tab-customer">@include('page.projects._customer')</div>
                    <div class="tab-pane fade" id="tab-quote">@include('page.projects._quote')</div>
                    <div class="tab-pane fade" id="tab-billing">@include('page.projects._billing')</div>
                    <div class="tab-pane fade" id="tab-documents">@include('page.projects._documents')</div>
                    <div class="tab-pane fade" id="tab-notes">@include('page.projects._note')</div>
                    <div class="tab-pane fade" id="tab-history">@include('page.projects._history')</div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="docModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="model-file-name"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center" id="model-file">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="holdProjectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hold Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="project_id" value="{{ $project->id }}">

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea id="hold_reason" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="saveHold" class="btn btn-warning">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="nextStepModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Steps</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="project_id" value="{{ $project->id }}">
                    <div class="text-end">
                        <button class="btn btn-sm btn-outline-info" id="addStepItem">
                            <span class="icon-base icon-22px mdi mdi-plus"></span> Add Step
                        </button>
                    </div>
                    <div class="row mx-0 align-items-baseline gx-1 gy-2" id="stepWrapper">
                        <div class="col-1">#</div>
                        <div class="col-9">Step</div>
                        <div class="col-2">Action</div>
                        <hr class="my-1">
                        @if (isset($project->next_step))
                            @foreach ($project->next_step as $id => $step)
                                <div class="col-1 stepID" id="id-div-{{ $id + 1 }}">{{ $id + 1 }}</div>
                                <div class="col-9" id="step-div-{{ $id + 1 }}">
                                    <input type="text" class="form-control steps-field" name="steps[]"
                                        value="{{ $step }}">
                                </div>
                                <div class="col-2" id="action-div-{{ $id + 1 }}">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill btn-icon deleteStepItem"
                                        data-id="{{ $id + 1 }}">
                                        <span class="icon-base icon-22px mdi mdi-delete"
                                            data-id="{{ $id + 1 }}"></span>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="col-1 stepID" id="id-div-1">1</div>
                            <div class="col-9" id="step-div-1">
                                <input type="text" class="form-control steps-field" name="steps[]">
                            </div>
                            <div class="col-2" id="action-div-1">
                                <button class="btn btn-sm btn-outline-danger rounded-pill btn-icon deleteStepItem"
                                    data-id="1">
                                    <span class="icon-base icon-22px mdi mdi-delete" data-id="1"></span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="saveSteps" class="btn btn-warning">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        PROJECT_ID = "{{ $project->id }}"
        PROJECT = @json($project);
        __PROJECT_USERS = @json($users);
        __PROJECT_STATUSES = @json($statuses);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@2/dist/tesseract.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="{{ asset('assets/js/page/project.edit.customer.js') }}"></script>
    <script src="{{ asset('assets/js/page/project.edit.quote.js') }}"></script>
    <script src="{{ asset('assets/js/page/project.edit.documents.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js"></script>
    <script src="{{ asset('assets/js/calender.js') }}"></script>
    <script src="{{ asset('assets/js/page/project.edit.js') }}"></script>
@endsection
