@if($activities->isEmpty())
    <div class="text-muted small">No activity recorded yet</div>
@else
    <div class="crm-activity-timeline">

        @foreach($activities as $a)
            <div class="crm-activity-item">

                {{-- ICON --}}
                <div class="crm-activity-icon
                    @if(Str::contains($a->action, 'created')) bg-primary
                    @elseif(Str::contains($a->action, 'updated')) bg-warning
                    @elseif(Str::contains($a->action, 'payment')) bg-success
                    @else bg-secondary
                    @endif
                ">
                    <i class="fa-solid
                        @if(Str::contains($a->action, 'payment')) fa-indian-rupee-sign
                        @elseif(Str::contains($a->action, 'invoice')) fa-file-invoice
                        @elseif(Str::contains($a->action, 'project')) fa-diagram-project
                        @else fa-circle-info
                        @endif
                    "></i>
                </div>

                {{-- CONTENT --}}
                <div class="crm-activity-content">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-semibold">
                            {{ Str::headline($a->action) }}
                        </div>
                        <div class="small text-muted">
                            {{ $a->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if($a->message)
                        <div class="crm-activity-message">
                            {{ $a->message }}
                        </div>
                    @endif

                    <div class="small text-muted mt-1">
                        By {{ optional($a->user)->fname . " " . optional($a->user)->lname ?? 'System' }}
                        · {{ $a->created_at->format('d M Y, h:i A') }}
                    </div>

                </div>

            </div>
        @endforeach

    </div>
@endif
