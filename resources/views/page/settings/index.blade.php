@extends('temp.common')

@section('title', 'Settings')
@section('head')
    <style>
        .settings-nav .list-group-item {
            cursor: pointer;
            font-weight: 500;
            padding: 12px 16px;
        }

        .settings-nav .list-group-item:hover {
            background: var(--bs-primary-bg-subtle);
        }

        .crm-setting-row {
            background: var(--bs-body-bg);
            border-radius: 10px;
            padding: 16px;
            border: 1px solid var(--bs-border-color);
        }

        .crm-setting-row:hover {
            border-color: var(--bs-primary);
            background: var(--bs-primary-bg-subtle);
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0">
                    <i class="fa-solid fa-gear me-2"></i>
                    System Settings
                </h4>
                <div class="text-muted small">
                    Configure system, module and user preferences
                </div>
            </div>

            <button class="btn btn-sm btn-outline-primary"
                    onclick="openSettingModal()">
                <i class="fa-solid fa-plus me-1"></i>
                Add Setting
            </button>
        </div>

            <div class="row g-3 mt-2">

                {{-- LEFT SIDEBAR --}}
                <div class="col-md-3">
                    <div class="crm-section p-0">
                        <ul class="list-group list-group-flush settings-nav">
                            @foreach ($groups as $key => $label)
                                <li class="list-group-item list-group-item-action"
                                    onclick="loadSettings('{{ $key }}')">
                                    <i class="fa-solid fa-chevron-right me-2 small text-muted"></i>
                                    {{ $label }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="col-md-9">
                    <div class="crm-section position-relative" id="settingsContent">
                        <div class="crm-loader-overlay">
                            <div class="crm-spinner"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
<div class="modal fade" id="settingModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title" id="settingModalTitle">
                    Add Setting
                </h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="settingForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="settingId">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label small">Name</label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="project_default_status">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">Label</label>
                            <input type="text" name="label" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select">
                                @foreach([
                                    0=>'Text',1=>'Number',2=>'Select',
                                    3=>'Radio',4=>'Checkbox',5=>'Textarea',
                                    6=>'File',7=>'Date',8=>'JSON'
                                ] as $k=>$v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Default</label>
                            <input type="text" name="default" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Attributes</label>
                            <input type="text" name="attr" class="form-control"
                                   placeholder="required min=1">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small">Options (JSON)</label>
                            <textarea name="option"
                                      class="form-control font-monospace"
                                      rows="3"
                                      placeholder='{"key":"Label"}'></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button class="btn btn-primary">
                        Save Setting
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        const SETTINGS_LOAD_URL = "{{ route('settings.ajax.list', ':group') }}";
        const SETTINGS_SAVE_URL = "{{ route('settings.ajax.save') }}";
    </script>
    <script src="{{ asset('assets/js/page/settings/index.js') }}"></script>
@endpush
