@php
    $doc = $docs[$type] ?? null;
    $view = @$view == true ? true : false
@endphp
<div class="w-100">
    <div class="crm-doc-slot" data-doc-type="{{ $type }}">
        @if ($view)
            <div class="crm-doc-upload-box w-100 {{ $doc ? '' : 'crm-badge-missing' }}" id="wrapper-{{ $type }}">
                <div class="preview">
                    @if ($doc)
                        @if (Str::startsWith($doc->mime_type, 'image/'))
                            <img src="{{ Storage::disk('public')->url($doc->file_path) }}" id="preview-{{ $type }}" class="img-fluid rounded mb-2"
                                style="width: 100%; object-fit:contain;">
                        @else
                            <i class="fa-solid fa-cloud-arrow-up mb-1"></i>
                        @endif
                    @elseif (@$placeholder)
                        <img src="{{ $placeholder }}" class="img-fluid rounded mb-2" id="preview-{{ $type }}"
                            style="width: 100%; object-fit:contain;">

                    @endif
                </div>
                <div class="fw-semibold">{{ $label }}</div>
            </div>
        @else
            <div class="crm-doc-upload-box w-100 {{ $doc ? '' : 'crm-badge-missing' }}" id="wrapper-{{ $type }}"
                onclick="openDocUpload('{{ $type }}')">
                <div class="preview">
                    @if ($doc)
                        @if (Str::startsWith($doc->mime_type, 'image/'))
                            <img src="{{ Storage::disk('public')->url($doc->file_path) }}" id="preview-{{ $type }}" class="img-fluid rounded mb-2"
                                style="width: 100%; object-fit:contain;">
                        @else
                            <i class="fa-solid fa-cloud-arrow-up mb-1"></i>
                        @endif
                    @elseif (@$placeholder)
                        <img src="{{ $placeholder }}" class="img-fluid rounded mb-2" id="preview-{{ $type }}"
                            style="width: 100%; object-fit:contain;">

                    @endif
                </div>
                <div class="fw-semibold">{{ $label }}</div>
                <div class="small text-muted">Click to {{ $doc ? 'replace' : 'upload' }}</div>
            </div>
            
        @endif
        @if ($doc)
            <div class="small text-muted mt-1">
                Uploaded {{ $doc->created_at->diffForHumans() }}
            </div>
        @endif
    </div>

</div>
