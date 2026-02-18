<div class="crm-section">
    <div class="fw-semibold mb-2">Execution Health</div>

    <div class="progress" style="height:12px">
        @foreach($rows as $r)
            <div class="progress-bar"
                 style="width: {{ $r['percent'] }}%;
                        background: {{ $r['color'] }}">
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between small text-muted mt-1">
        @foreach($rows as $r)
            <span>{{ $r['label'] }}</span>
        @endforeach
    </div>
</div>
