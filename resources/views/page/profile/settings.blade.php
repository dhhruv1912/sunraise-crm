@extends('temp.common')

@section('title', 'My Settings')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="">
                        <h4 class="mb-0">
                            <i class="fa-solid fa-user-gear me-2"></i>
                            My Settings
                        </h4>
                        <div class="text-muted small">
                            Customize your experience
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="resetUserSettings()">
                        <i class="fa-solid fa-rotate-left me-1"></i>
                        Reset to Default
                    </button>
                </div>
            </div>

            <div class="row g-3 mt-2">

                {{-- LEFT NAV --}}
                <div class="col-md-3">
                    <div class="crm-section p-0 bg-transparent shadow-none">
                        <ul class="list-group list-group-flush settings-nav">
                            @foreach ([
                                'appearance' => 'Appearance',
                                'dashboard' => 'Dashboard',
                                'notifications' => 'Notifications',
                                'preferences' => 'Preferences',
                                'security' => 'Security',
                            ] as $k => $v)
                                <li class="border-top-0 border-start-0 border-end-0 btn btn-outline-secondary my-1"
                                    onclick="loadUserSettings('{{ $k }}')" id="SettingBtn{{ $k }}">
                                    {{ $v }}
                                </li>
                                {{-- <li class="list-group-item list-group-item-action btn btn-outline-secondary my-1"
                                    onclick="loadUserSettings('{{ $k }}')" id="SettingBtn{{ $k }}">
                                    {{ $v }}
                                </li> --}}
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="col-md-9">
                    <div class="crm-section position-relative" id="userSettingsContent">
                        <div class="crm-loader-overlay">
                            <div class="crm-spinner"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const USER_SETTINGS_URL = "{{ route('profile.ajax.settings') }}";
    </script>
    <script src="{{ asset('assets/js/page/profile/settings.js') }}"></script>
@endpush
