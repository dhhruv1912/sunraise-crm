@php
    use Carbon\Carbon;
@endphp
<div class="">
    <div class="alert alert-info d-flex align-items-baseline justify-content-between" role="alert">
        <span>Current Step : <b id="currentStepText">{{ $project->current_step }}</b></span>
        <div class="actions">
            <button class="btn btn-sm btn-outline-info rounded-pill btn-icon" id="completeCurrentStep"><span
                    class="icon-base mdi mdi-check icon-22px"></span></button>
        </div>
    </div>
    <div class="alert alert-secondary d-flex align-items-baseline justify-content-between flex-column" role="alert">
        <div class="d-flex align-items-baseline justify-content-between w-100">
            <span class="collapsed" data-bs-toggle="collapse" data-bs-target="#StepsCollapse" aria-expanded="false"
                aria-controls="StepsCollapse" style="cursor:pointer;">
                Next Steps <span class="mdi mdi-chevron-down"></span>
            </span>
            <div class="actions">
                <button class="btn btn-sm btn-outline-primary rounded-pill btn-icon" id="editNextStep"><span
                        class="mdi mdi-pencil"></span></button>
            </div>
        </div>
        <div class="collapse w-100" id="StepsCollapse" style="">
            <div class="">
                {{-- @dd($project->next_step) --}}
                <div class="list-group list-group-flush list-group-numbered" id="stepsList">
                    @foreach ($project->next_step as $id => $next_step)
                        <a href="javascript:void(0);"
                            class="fs-6 list-group-item list-group-item-action">{{ $next_step }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-2">
        <div class="card-header">Dates :</div>
        <hr class="m-0">
        <div class="card-body">
            <div class="table-responsive text-nowrap pb-2">
                <table class="table table-striped">
                    <thead>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr>
                            <td>Survey Date</td>
                            <td>
                                <input type="date" class="form-control" name="survey_date" id="survey_date"
                                    value="{{ Carbon::parse($project->survey_date)->format('Y-m-d') }}">
                            </td>
                            <td>Installation Start Date</td>
                            <td>
                                <input type="date" class="form-control" name="installation_start_date"
                                    id="installation_start_date"
                                    value="{{ Carbon::parse($project->installation_start_date)->format('Y-m-d') }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Installation End Date</td>
                            <td>
                                <input type="date" class="form-control" name="installation_end_date"
                                    id="installation_end_date"
                                    value="{{ Carbon::parse($project->installation_end_date)->format('Y-m-d') }}">
                            </td>
                            <td>Inspection Date</td>
                            <td>
                                <input type="date" class="form-control" name="inspection_date" id="inspection_date"
                                    value="{{ Carbon::parse($project->inspection_date)->format('Y-m-d') }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Handover Date</td>
                            <td>
                                <input type="date" class="form-control" name="handover_date" id="handover_date"
                                    value="{{ Carbon::parse($project->handover_date)->format('Y-m-d') }}">
                            </td>
                            <td>Estimated Complete Date</td>
                            <td>
                                <input type="date" class="form-control" name="estimated_complete_date"
                                    id="estimated_complete_date"
                                    value="{{ Carbon::parse($project->estimated_complete_date)->format('Y-m-d') }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card mb-2">
        <div class="card-header">Subsidy :</div>
        <hr class="m-0">
        <div class="card-body">
            <div class="row">
                @php
                    $docId = $project->subsidy_file;
                    $subsidy_file = $docId ? optional($project->projectDocuments)->firstWhere('id', $docId) : null;
                @endphp
                <div class="col-md-6">
                    <div class="row mx-0 align-items-baseline my-1">
                        <div class="col-4">
                            <h6>Status</h6>
                        </div>
                        <div class="col-8">
                            <select id="subsidy_status" class="form-select form-select-sm mb-1"
                                class="mb-2form-select form-select-sm subsidy_status-select w-auto"
                                data-id={{ $project->id }}>
                                <option {{ $project->subsidy_status == 'not_applied' ? 'selected' : '' }}
                                    value="not_applied">Not Applied</option>
                                <option {{ $project->subsidy_status == 'applied' ? 'selected' : '' }} value="applied">
                                    Applied</option>
                                <option {{ $project->subsidy_status == 'inspection_pending' ? 'selected' : '' }}
                                    value="inspection_pending">Inspection Pending</option>
                                <option {{ $project->subsidy_status == 'approved' ? 'selected' : '' }}
                                    value="approved">Approved</option>
                                <option {{ $project->subsidy_status == 'rejected' ? 'selected' : '' }}
                                    value="rejected">Rejected</option>
                                <option {{ $project->subsidy_status == 'subsidy_released' ? 'selected' : '' }}
                                    value="subsidy_released">Subsidy Released</option>
                            </select>
                            <a href="https://consumer.pmsuryaghar.gov.in/consumer/" target="_blank"
                                class="btn btn-sm btn-primary">
                                Check Subsidy Status (Gov Portal)
                            </a>
                        </div>
                    </div>
                    <div class="row mx-0 align-items-baseline mb-2">
                        <div class="col-4">

                        </div>
                        <div class="col-8">

                        </div>
                    </div>
                    @if ($subsidy_file)
                        <div class="row mx-0 align-items-baseline my-1">
                            <div class="col-4">
                                <h6>Uploaded By</h6>
                            </div>
                            <div class="col-8">
                                {{ $subsidy_file->uploader->fname }} {{ $subsidy_file->uploader->lname }}
                            </div>
                        </div>
                        <div class="row mx-0 align-items-baseline my-1">
                            <div class="col-4">
                                <h6>Uploaded At</h6>
                            </div>
                            <div class="col-8">
                                {{ $subsidy_file->updated_at }}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="row mx-0">
                        <div class="col-12">
                            <h6>Subsidy File</h6>
                        </div>
                        <div class="col-12 w-100 h-px-400" id="pdf17" style="">

                            @if ($subsidy_file)
                                {{-- subsidy_file --}}
                                <div class="col-12 h-100" id="pdf9" style="">
                                    <embed src="{{ asset('storage/' . $subsidy_file->file_path) }}" width="100%"
                                        class="border border-3 rounded-3 h-100">
                                </div>
                            @else
                                <div
                                    class="h-100 d-flex flex-column justify-content-center align-items-center border border-3 rounded-3">
                                    Subsidy File is not uploaded yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-2">
        <div class="card-body">
            <div class="col-12 text-end">
                <button class="btn btn-outline-warning btn-sm" type="reset">Reset</button>
                <button class="btn btn-sm btn-outline-primary" id="saveProjectData"
                    data-project-id="{{ $project->id }}" type="button">Save Project Data</button>
            </div>
        </div>
    </div>
    @if ($project->design_file)
        <div class="card mb-2">
            <div class="card-header">Design File :</div>
            <hr class="m-0">
            @php
                $docId = $project->design_file;
                $design_file = $docId ? optional($project->projectDocuments)->firstWhere('id', $docId) : null;
            @endphp
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row mx-0 align-items-baseline my-1">
                            <div class="col-4">
                                <h6>Uploaded By</h6>
                            </div>
                            <div class="col-8">
                                {{ $design_file->uploader->fname }} {{ $design_file->uploader->lname }}
                            </div>
                        </div>
                        <div class="row mx-0 align-items-baseline my-1">
                            <div class="col-4">
                                <h6>Uploaded At</h6>
                            </div>
                            <div class="col-8">
                                {{ $design_file->updated_at }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mx-0">
                            <div class="col-12 w-100 h-px-400" id="pdf17" style="">

                                <div class="col-12 h-100" id="pdf9" style="">
                                    <embed src="{{ asset('storage/' . $design_file->file_path) }}" width="100%"
                                        class="border border-3 rounded-3 h-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="card mb-2">
        <div class="card-header">Documents :</div>
        <hr class="m-0">
        <div class="card-body">
            <div class="table-responsive text-nowrap pb-2">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>name</th>
                            <th>file</th>
                            <th></th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach (['design_file', 'subsidy_file'] as $doc)
                            <tr data-doc="{{ $doc }}">
                                @php
                                    $docId = $project->{$doc};
                                    $document = $docId
                                        ? optional($project->projectDocuments)->firstWhere('id', $docId)
                                        : null;
                                @endphp

                                <td>
                                    @if ($document)
                                        @if (Str::startsWith($document->mime_type, 'image/'))
                                            <img src="{{ asset('storage/' . $document->file_path) }}"
                                                class="img-fluid rounded viewDoc"
                                                style="max-height:120px;cursor:pointer" data-id="{{ $document->id }}"
                                                data-file_name="{{ $document->file_name }}"
                                                data-mime_type="{{ $document->mime_type }}"
                                                data-file_path="{{ asset('storage/' . $document->file_path) }}"
                                                id="{{ $doc }}Preview">
                                        @else
                                            <i id="{{ $doc }}Preview"
                                                class="bi bi-file-earmark-pdf text-danger viewDoc"
                                                style="font-size:3rem;cursor:pointer" data-id="{{ $document->id }}"
                                                data-file_name="{{ $document->file_name }}"
                                                data-mime_type="{{ $document->mime_type }}"
                                                data-file_path="{{ asset('storage/' . $document->file_path) }}"></i>
                                        @endif
                                    @else
                                        <img src="{{ asset('assets/img/placeholder/user.jpg') }}"
                                            id="{{ $doc }}Preview" class="img-fluid rounded"
                                            style="max-height:120px;cursor:pointer">
                                    @endif
                                    <input type="hidden" id="{{ $doc }}_hidden"
                                        value="{{ $docId }}">
                                </td>

                                <td>{{ ucfirst($doc) }}</td>

                                <td>
                                    <input type="file" {{ $docId ? 'disabled placeholder="ASAJDIOH"' : '' }}
                                        id="{{ $doc }}File" class="form-control">
                                </td>

                                <td>
                                    <div class="progress d-none" id="{{ $doc }}ProgressWrap">
                                        <div class="progress-bar" id="{{ $doc }}Progress"></div>
                                    </div>
                                </td>

                                <td>
                                    <div class="btn-group">
                                        <!-- Upload -->
                                        <button class="btn btn-sm btn-outline-warning upload-document"
                                            data-file="{{ $doc }}File" data-doc_type="{{ $doc }}"
                                            data-hidden="{{ $doc }}_hidden"
                                            data-preview="{{ $doc }}Preview"
                                            data-progress="{{ $doc }}Progress"
                                            data-progress-wrap="{{ $doc }}ProgressWrap">
                                            <i class="mdi mdi-upload"></i>
                                        </button>

                                        <!-- Delete -->
                                        <button class="btn btn-sm btn-outline-danger delete-document"
                                            data-hidden="{{ $doc }}_hidden"
                                            data-preview="{{ $doc }}Preview">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card mb-2">
        <div class="card-header">
            Project Photos
        </div>
        <hr class="m-0">
        <div class="card-body">

            <div class="input-group mb-3">
                <input type="hidden" name="project_id" id="projectId" value="{{ $project->id }}">
                <input type="file" class="form-control" id="projectPhotos" aria-describedby="inputGroupFileAddon04"
                    name="files[]" multiple aria-label="Upload Image or PDF" accept="image/*">
                <button class="btn btn-primary" type="submit" id="ProjectPhotoUpload">Upload</button>
            </div>
            <div class="progress mt-3 d-none" id="projectPhotosProgressWrapper">
                <div class="progress-bar" id="projectPhotosProgressBar" role="progressbar" style="width: 0%">
                    0%
                </div>
            </div>
            <div id="projectPhotosStatus" class="mt-2"></div>
            <div id="projectPhotosPreviewBox" class="mt-3 d-none">
                <div class="card">
                    <div class="card-body">
                        <div id="previewContent" class="row g-3"></div>
                    </div>
                </div>
            </div>
            @if ($project->site_photos)
                <div class="row g-3" id="SitePhotoContainer">
                    @foreach ($project->site_photos as $photo)
                        @if ($project->projectDocuments()->find($photo))
                            @php
                                $site_photo = $project->projectDocuments()->find($photo)
                            @endphp
                            <div class="col-md-3 photo-item">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">

                                        {{-- Preview --}}
                                        <img src="{{ asset('storage/' . $site_photo->file_path) }}"
                                            class="img-fluid rounded viewDoc" style="max-height:120px;cursor:pointer"
                                            data-id="{{ $site_photo->id }}" data-file_name="{{ $site_photo->file_name }}"
                                            data-mime_type="{{ $site_photo->mime_type }}"
                                            data-file_path="{{ asset('storage/' . $site_photo->file_path) }}">
                                        <p class="small mt-2 mb-1 text-truncate">
                                            {{ $site_photo->file_name }}
                                        </p>
                                        <button class="btn btn-sm btn-outline-danger delete-site-photo"
                                            data-id="{{ $site_photo->id }}">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                        @endif

                        {{-- View Modal --}}
                    @endforeach
                </div>
            @else
                <p class="text-muted">No documents uploaded.</p>
            @endif
        </div>
    </div>
</div>
