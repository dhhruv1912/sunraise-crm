@extends('temp.common')
@section('content')
    <form onsubmit="event.preventDefault(); saveSettings(this)">

        {{-- GROUP HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="fw-semibold fs-6">
                    {{ $prefix }}
                </div>
                {{-- <div class="text-muted small">
                    {{ $description ?? '' }}
                </div> --}}
            </div>

            <button class="btn btn-sm btn-primary">
                <i class="fa-solid fa-save me-1"></i>
                Save
            </button>
        </div>

        {{-- SETTINGS --}}
        <div class="row g-3">
            @foreach ($settings as $s)
                <div class="col-md-12">
                    <div class="crm-setting-row">

                        <div class="row g-3 align-items-start">

                            {{-- LABEL --}}
                            <div class="col-md-4">
                                <div class="fw-semibold">
                                    {{ $s->label }}
                                </div>

                                @if ($s->description)
                                    <div class="text-muted small">
                                        {{ $s->description }}
                                    </div>
                                @endif
                            </div>

                            {{-- INPUT --}}
                            <div class="col-md-8">
                                @include('page.settings.partials.input', ['s' => $s])
                            </div>

                        </div>
                        <div class="d-flex justify-content-end gap-2 mb-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary"
                                onclick="editSetting(@json($s))">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button type="button" class="btn btn-xs btn-outline-danger"
                                onclick="deleteSetting({{ $s->id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </form>
@endsection
