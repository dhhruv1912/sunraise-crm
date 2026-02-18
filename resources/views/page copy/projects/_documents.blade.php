<div class="input-group mb-3">
    <input type="hidden" name="project_id" id="projectId" value="{{ $project->id }}">
    <input type="file" class="form-control" id="fileInput" aria-describedby="inputGroupFileAddon04" name="files[]"
        multiple aria-label="Upload Image or PDF" accept="image/*,application/pdf">
    <button class="btn btn-primary" type="submit" id="fileInputUpload">Upload</button>
</div>
<div class="progress mt-3 d-none" id="progressWrapper">
    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%">
        0%
    </div>
</div>
<div id="status" class="mt-2"></div>
<div id="previewBox" class="mt-3 d-none">
    <div class="card">
        <div class="card-body">
            <div id="previewContent" class="row g-3"></div>
        </div>
    </div>
</div>
@if ($project->projectDocuments && $project->projectDocuments->count())
    <div class="row g-3" id="container">
        @foreach ($project->projectDocuments as $doc)
            @if ($doc->type != 'site_photos')
                <div class="col-md-3 doc-item">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">

                            {{-- Preview --}}
                            @if (Str::startsWith($doc->mime_type, 'image/'))
                                <img src="{{ asset('storage/' . $doc->file_path) }}" class="img-fluid rounded viewDoc"
                                    style="max-height:120px;cursor:pointer" data-id="{{ $doc->id }}"
                                    data-file_name="{{ $doc->file_name }}" data-mime_type="{{ $doc->mime_type }}"
                                    data-file_path="{{ asset('storage/' . $doc->file_path) }}">
                            @else
                                <i class="bi bi-file-earmark-pdf text-danger viewDoc"
                                    style="font-size:3rem;cursor:pointer" data-id="{{ $doc->id }}"
                                    data-file_name="{{ $doc->file_name }}" data-mime_type="{{ $doc->mime_type }}"
                                    data-file_path="{{ asset('storage/' . $doc->file_path) }}"></i>
                            @endif

                            <p class="small mt-2 mb-1 text-truncate">
                                {{ $doc->file_name }}
                            </p>

                            <button class="btn btn-sm btn-outline-danger delete-project-doc"
                                data-id="{{ $doc->id }}">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@else
    <p class="text-muted">No documents uploaded.</p>
@endif
