@extends('temp.common')

@section('title', 'Document View')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-1">
                    <i class="fa-solid fa-file-lines me-2"></i>
                    Document Preview
                </h4>
                <div class="text-muted small">
                    {{ $document->file_name }}
                </div>
            </div>

            <a href="{{ route('documents.view.list') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Back
            </a>
        </div>

        <div class="row g-3 mt-2">

            {{-- PREVIEW --}}
            <div class="col-lg-8">
                <div class="crm-section">

                    @if(Str::startsWith($document->mime_type, 'image/'))
                        <img src="{{ Storage::disk('public')->url($document->file_path) }}"
                             class="img-fluid rounded"
                             alt="{{ $document->file_name }}">
                    @elseif($document->mime_type === 'application/pdf')
                        <iframe src="{{ Storage::disk('public')->url($document->file_path) }}"
                                style="width:100%; height:80vh;"
                                frameborder="0"></iframe>
                    @else
                        <div class="text-muted">
                            Preview not available
                        </div>
                    @endif

                </div>
            </div>

            {{-- META --}}
            <div class="col-lg-4">
                <div class="crm-section">

                    <h6 class="mb-3">Document Details</h6>

                    <div class="mb-2">
                        <div class="text-muted small">File Name</div>
                        <div class="fw-semibold">
                            {{ $document->file_name }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted small">Type</div>
                        <div>
                            {{ Str::headline($document->type ?? 'Document') }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted small">Uploaded On</div>
                        <div>
                            {{ $document->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted small">Uploaded By</div>
                        <div>
                            {{ optional($document->uploader)->fname . " " . optional($document->uploader)->lname ?? 'System' }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted small">File Size</div>
                        <div>
                            {{ number_format($document->size / 1024, 2) }} KB
                        </div>
                    </div>

                    <hr>

                    {{-- ENTITY LINK --}}
                    <div class="mb-3">
                        <div class="text-muted small">Linked To</div>

                        @if($document->entity_type === "App\Models\Customer")
                            <a href="{{ route('customers.view.show', $document->entity_id) }}"
                               class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fa-solid fa-user me-1"></i>
                                View Customer
                            </a>
                        @elseif($document->entity_type === "App\Models\Project")
                            <a href="{{ route('customers.view.show', $document->entity_id) }}"
                            {{-- <a href="{{ route('projects.view.show', $document->entity_id) }}" --}}
                               class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fa-solid fa-diagram-project me-1"></i>
                                View Project
                            </a>
                        @elseif($document->entity_type === "App\Models\Invoice")
                            <a href="{{ route('invoices.view.show', $document->entity_id) }}"
                               class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fa-solid fa-file-invoice me-1"></i>
                                View Invoice
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    {{-- ACTIONS --}}
                    <div class="d-flex gap-2">
                        <a href="{{ Storage::disk('public')->url($document->file_path) }}"
                           target="_blank"
                           class="btn btn-outline-success w-100">
                            <i class="fa-solid fa-download me-1"></i>
                            Download
                        </a>
                        @can('projects.document.edit')
                            <button class="btn btn-outline-danger w-100"
                                    onclick="openDeleteModal()">
                                <i class="fa-solid fa-trash me-1"></i>
                                Delete
                            </button>
                            
                        @endcan
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
@include('page.documents.delete')
@endsection

@push('scripts')
<script>
    const DOC_DELETE_URL = "{{ route('documents.ajax.delete', $document->id) }}";
</script>
<script src="{{ asset('assets/js/page/documents/view.js') }}"></script>
@endpush
