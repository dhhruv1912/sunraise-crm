<form onsubmit="event.preventDefault(); saveUserSettings(this)">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="fw-semibold fs-6">{{ $title }}</div>
            <div class="text-muted small">{{ $description }}</div>
        </div>

        <button class="btn btn-sm btn-primary">
            <i class="fa-solid fa-save me-1"></i>
            Save
        </button>
    </div>

    <div class="row g-3">
        @foreach($settings as $s)
            <div class="col-md-12">
                <div class="crm-setting-row">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="fw-semibold">{{ $s->label }}</div>
                        </div>
                        <div class="col-md-8">
                            @include('page.settings.partials.input', ['s'=>$s])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</form>
