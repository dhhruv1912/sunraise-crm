<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="fa-solid fa-folder-open me-1"></i>
        Documents
    </h6>
</div>

@if($documents->isEmpty())
    <div class="text-muted small">
        No documents uploaded
    </div>
@else
    <div class="row g-3 crm-doc-grid">

        @foreach($documents as $d)
            <div class="col-md-3 col-sm-4 col-6">
                <div class="crm-doc-card">

                    {{-- PREVIEW --}}
                    <div class="crm-doc-preview">
                        @if(Str::startsWith($d->mime_type, 'image/'))
                            <img src="{{ Storage::disk('public')->url($d->file_path) }}"
                                 alt="{{ $d->file_name }}">
                        @else
                            <i class="fa-solid fa-file-pdf"></i>
                        @endif
                    </div>

                    {{-- META --}}
                    <div class="crm-doc-body">
                        <div class="fw-semibold text-truncate"
                             title="{{ $d->file_name }}">
                            {{ $d->file_name }}
                        </div>

                        <div class="small text-muted">
                            {{ Str::headline($d->type ?? 'Document') }}
                        </div>

                        <div class="small text-muted">
                            {{ $d->created_at->format('d M Y') }}
                        </div>
                    </div>

                    {{-- ACTION --}}
                    <div class="crm-doc-actions">
                        <a href="{{ Storage::disk('public')->url($d->file_path) }}"
                           target="_blank"
                           class="btn btn-sm btn-light w-100">
                            <i class="fa-solid fa-eye me-1"></i>
                            Preview
                        </a>
                    </div>

                </div>
            </div>
        @endforeach

    </div>
@endif
