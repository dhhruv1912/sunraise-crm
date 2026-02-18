    <div class="fw-semibold mb-2">Documents</div>

    <div class="row g-2">
        @foreach($docChecklist as $d)
    @php
        $isProject = $d['entity'] === App\Models\Project::class;
        $entityId  = $isProject ? $project->id : $project->customer_id;
        $entityKey = $isProject ? 'project' : 'customer';
        $docs      = $d['uploaded'] ? collect($d['doc']) : collect();
    @endphp

    <div class="col-md-4">
        <div class="doc-card {{ $d['uploaded'] ? 'done' : 'missing' }}">

            {{-- TITLE --}}
            <div class="doc-title">
                {{ $d['label'] }}
            </div>

            {{-- PREVIEW AREA --}}
            <div class="doc-body">

                {{-- MULTIPLE DOCS --}}
                @if($d['multiple'])
                    @if($docs->isNotEmpty())
                        <div class="doc-grid">
                            @foreach($docs as $doc)
                                <div class="doc-preview"
                                     onclick="previewDoc('{{ Storage::disk('public')->url($doc->file_path) }}')">
                                    @if(Str::contains($doc->mime_type, 'image'))
                                        <img loading="eager" src="{{ Storage::disk('public')->url($doc->file_path) }}">
                                    @else
                                        <i class="fa-solid fa-file-pdf fa-2x text-danger"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="doc-empty">
                            No documents uploaded
                        </div>
                    @endif

                {{-- SINGLE DOC --}}
                @else
                    @if($docs->isNotEmpty())
                        @php $doc = $docs->last(); @endphp
                        <div class="doc-preview single"
                             onclick="previewDoc('{{ Storage::disk('public')->url($doc->file_path) }}')">
                            @if(Str::contains($doc->mime_type, 'image'))
                                <img loading="eager" src="{{ Storage::disk('public')->url($doc->file_path) }}">
                            @else
                                <i class="fa-solid fa-file-pdf fa-2x text-danger"></i>
                            @endif
                        </div>
                    @else
                        <div class="doc-empty">
                            Missing document
                        </div>
                    @endif
                @endif

            </div>

            {{-- ACTION --}}
            <div class="doc-footer">
                @can("project.edit")
                    <button class="btn btn-sm {{ $d['uploaded'] ? 'btn-outline-secondary' : 'btn-primary' }} w-100"
                            onclick="uploadDoc(
                                '{{ $d['key'] }}',
                                '{{ $d['entity'] }}',
                                '{{ $entityId }}',
                                '{{ $entityKey }}',
                                {{ $d['multiple'] ? 'true' : 'false' }}
                            )">
                        {{ $d['uploaded'] ? 'Replace' : 'Upload' }}
                    </button>
                    
                @endcan
            </div>

        </div>
    </div>
@endforeach


    </div>
