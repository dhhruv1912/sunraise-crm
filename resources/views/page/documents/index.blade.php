@extends('temp.common')

@section('title', 'Documents')
@section('head')
    <style>
        .crm-doc-grid {
            --card-radius: 12px;
        }

        .crm-doc-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .crm-doc-preview {
            height: 120px;
            background: #f4f5fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .crm-doc-preview img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .crm-doc-preview i {
            font-size: 32px;
            color: #dc3545;
        }

        .crm-doc-body {
            padding: 10px 12px;
            flex-grow: 1;
        }

        .crm-doc-actions {
            padding: 8px 10px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-folder-open me-2"></i>
                        Documents
                    </h4>
                    <div class="text-muted small">
                        All uploaded documents across CRM
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2 position-relative" id="documentWidgets" style="min-height:100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>
            <div class="row g-3 mt-2 position-relative" id="documentAdvancedWidgets" style="min-height:100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>
            {{-- FILTERS --}}
            <div class="crm-section mt-2">
                @include('page.documents.filters')
            </div>

            {{-- LIST --}}
            <div class="crm-section mt-3">
                <div class="position-relative">

                    <div class="row g-3" id="documentGrid">
                        {{-- skeleton --}}
                        @for ($i = 0; $i < 8; $i++)
                            <div class="col-md-3">
                                <div class="crm-skeleton" style="height:180px"></div>
                            </div>
                        @endfor
                    </div>

                    <div id="documentLoader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>
                </div>

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-end mt-3" id="documentPagination"></div>
            </div>

        </div>
    </div>
@include('page.documents.delete')
@endsection

@push('scripts')
    <script>
        const DOC_LIST_URL = "{{ route('documents.ajax.list') }}";
        const DOC_WIDGET_URL = '/documents/ajax/widgets';
        const DOC_ADV_WIDGET_URL = '/documents/ajax/advanced-widgets';     
    </script>
    <script src="{{ asset('assets/js/page/documents/list.js') }}"></script>
@endpush
