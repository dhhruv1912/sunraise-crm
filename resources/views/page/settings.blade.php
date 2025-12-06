@extends('temp.common')

@section('title', 'Settings')

@section('content')

    {{-- Modal for Adding Setting --}}
    @include('page.modal.setting-modal')


        <div class="card">
            <h4 class="card-header">Settings ({{ strtoupper($module) }})</h4>

            <div class="card-body">

                {{-- inline alerts --}}
                @foreach ($settings as $key => $setting)
                    <div class="row align-items-center mb-2">
                        <div class="col-3">{{ $setting->label }}</div>
                        <div class="col">
                            {!! $setting->getField() !!}
                        </div>
                        <div class="col-2 d-flex">
                            {!! $setting->getButtons() !!}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>


    <button id="add-setting" class="btn btn-danger floating-btn shadow-danger">
        Add Setting
    </button>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/setting.js') }}"></script>
@endsection
