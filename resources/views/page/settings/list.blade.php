<form onsubmit="event.preventDefault(); saveSettings(this)">
    <div class="row g-3">

        @foreach($settings as $s)
            <div class="col-md-12">
                <label class="form-label small">
                    {{ $s->label }}
                    <span class="text-muted small">({{ $s->name }})</span>
                </label>

                @include('page.settings.partials.input', ['s' => $s])
            </div>
        @endforeach

    </div>

    <button class="btn btn-primary mt-3">Save Settings</button>
</form>

<script>

</script>
