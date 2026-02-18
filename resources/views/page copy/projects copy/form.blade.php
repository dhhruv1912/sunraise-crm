@extends('temp.common')

@section('title', isset($project) ? "Edit Project #{$project->id}" : "Create Project")

@section('head')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@2/dist/tesseract.min.js"></script>
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card mt-3">
        <div class="card-body">

            <h4 class="mb-4">
                {{ isset($project) ? "Edit Project #{$project->project_code}" : "Create Project" }}
            </h4>

            <form id="project-master-form" enctype="multipart/form-data">

                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">

                        <li class="nav-item m-1">
                            <button type="button" class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#tab-customer">
                                Customer Profile
                            </button>
                        </li>

                        <li class="nav-item m-1">
                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#tab-pricing">
                                Pricing
                            </button>
                        </li>

                        <li class="nav-item m-1">
                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#tab-documents">
                                Documents
                            </button>
                        </li>

                        <li class="nav-item m-1">
                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#tab-bank">
                                Bank Details
                            </button>
                        </li>

                        <li class="nav-item m-1">
                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#tab-notes">
                                Notes
                            </button>
                        </li>

                        <li class="nav-item m-1">
                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#tab-logs">
                                Logs
                            </button>
                        </li>

                    </ul>

                    <div class="tab-content">

                        {{-- -------------------------------------- --}}
                        {{-- TAB 1: CUSTOMER PROFILE --}}
                        {{-- -------------------------------------- --}}
                        <div class="tab-pane fade show active" id="tab-customer">
                            <div class="row">

                                <div class="col-9">
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Name</label>
                                        <div class="col-sm-10">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                                <input type="text" name="customer_name" class="form-control"
                                                    value="{{ $project->customer_name ?? '' }}"
                                                    placeholder="John Doe">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ $project->email ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Phone</label>
                                        <div class="col-sm-10">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="mdi mdi-phone"></i></span>
                                                <input type="text" name="mobile" class="form-control"
                                                    value="{{ $project->mobile ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Address</label>
                                        <div class="col-sm-10">
                                            <textarea name="address" class="form-control" rows="3">{{ $project->address ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Passport Photo --}}
                                <div class="col-3">
                                    <div class="border p-1 rounded document-upload-element"
                                        data-placeholder_url="{{ asset('assets/img/placeholder/user.jpg') }}">

                                        <button type="button" class="remove-document-icon btn btn-danger btn-icon w-100 d-none"
                                            style="bottom:0; left:0; opacity:.6; position:absolute;">
                                            <i class="mdi mdi-close"></i> Remove
                                        </button>

                                        <button type="button" class="upload-document-icon btn btn-primary btn-icon w-100 d-none"
                                            style="bottom:0; left:0; opacity:.6; position:absolute;">
                                            <i class="mdi mdi-plus"></i> Add
                                        </button>

                                        <input type="file" name="passport_size_photo" class="d-none" accept="image/*">
                                        <input type="hidden" name="passport_size_photo_hidden"
                                            value="{{ $project->passport_size_photo ?? '' }}">

                                        <img src="@if($project->passport_size_photo) {{ asset('storage/'.$project->passport_size_photo) }} @else {{ asset('assets/admin/img/placeholder/user.jpg') }} @endif"
                                            class="rounded w-100 placeholder_img">
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- -------------------------------------- --}}
                        {{-- TAB 2: PRICING --}}
                        {{-- -------------------------------------- --}}
                        <div class="tab-pane fade" id="tab-pricing">
                            @include('page.projects._pricing')
                        </div>

                        {{-- -------------------------------------- --}}
                        {{-- TAB 3: DOCUMENTS --}}
                        {{-- -------------------------------------- --}}
                        <div class="tab-pane fade" id="tab-documents">
                            @include('page.projects._documents')
                        </div>

                        {{-- -------------------------------------- --}}
                        {{-- TAB 4: BANK --}}
                        {{-- -------------------------------------- --}}
                        <div class="tab-pane fade" id="tab-bank">
                            @include('page.projects.bank')
                        </div>

                        {{-- -------------------------------------- --}}
                        {{-- TAB 5: NOTES --}}
                        {{-- -------------------------------------- --}}
                        <div class="tab-pane fade" id="tab-notes">
                            <div id="project_note" class="h-px-300">{!! $project->project_note ?? '' !!}</div>
                        </div>

                        {{-- -------------------------------------- --}}
                        {{-- TAB 6: LOGS --}}
                        {{-- -------------------------------------- --}}
                        <div class="tab-pane fade" id="tab-logs">
                            @include('page.projects.logs')
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>


    <div class="card mt-3">
        <div class="card-body text-center">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-danger">Exit</a>

            <button type="button" id="SaveProject"
                data-id="{{ $project->id ?? 0 }}"
                class="btn btn-success">
                Save
            </button>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
$(function () {

    // CSRF
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});

    // Quill
    const quill = new Quill('#project_note', { theme: 'snow' });

    // Save Project
    $(document).on('click', '#SaveProject', function () {

        let formData = new FormData($('#project-master-form')[0]);
        formData.append('project_note', quill.root.innerHTML);

        let id = $(this).data("id");
        let url = id > 0 ? `/projects/${id}/update` : `/projects/store`;

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                toastr.success("Project saved successfully!");
                window.location.href = "{{ route('projects.index') }}";
            },
            error: function(err) {
                toastr.error("Save failed");
            }
        });
    });

});
</script>
@endsection
