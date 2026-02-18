<div class="col-md-12">
    <div class="crm-section">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">
                <i class="fa-solid fa-calendar-week me-1"></i>
                Weekly Timeline
            </div>
            <span class="text-muted small">
                Upcoming 7 days
            </span>
        </div>

        <div class="row g-3">

            @foreach($days as $date => $items)
                @php
                    $d = \Carbon\Carbon::parse($date);
                @endphp

                <div class="col-md-3 col-lg-2">
                    <div class="border rounded p-2 h-100">

                        <div class="fw-semibold small">
                            {{ $d->format('D') }}
                        </div>
                        <div class="text-muted small mb-2">
                            {{ $d->format('d M') }}
                        </div>

                        @if($items->isEmpty())
                            <div class="text-muted small">
                                No events
                            </div>
                        @else
                            <div class="crm-timeline-mini">
                                @foreach($items as $e)
                                    <div class="mb-2">
                                        <div class="fw-semibold small">
                                            {{ $e['type'] }}
                                        </div>
                                        <div class="small">
                                            {{ $e['title'] }}
                                        </div>
                                        @if($e['meta'])
                                            <div class="text-muted small">
                                                {{ $e['meta'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach

        </div>

    </div>
</div>
