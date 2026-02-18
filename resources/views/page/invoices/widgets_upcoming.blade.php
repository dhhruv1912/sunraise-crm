{{-- <div class="col-md-12">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">
                Upcoming Payments
            </div>
            <i class="fa-solid fa-calendar-days text-primary"></i>
        </div>

        @if($rows->isEmpty())
            <div class="text-muted small">
                No upcoming payments
            </div>
        @else
            <div class="crm-timeline">

                @foreach($rows as $r)
                    @php
                        $date = \Carbon\Carbon::parse($r['date']);
                        $isToday = $date->isToday();
                        $isTomorrow = $date->isTomorrow();
                    @endphp

                    <div class="crm-timeline-item">
                        <div class="crm-timeline-dot
                            {{ $isToday ? 'bg-danger' : ($isTomorrow ? 'bg-warning' : 'bg-primary') }}">
                        </div>

                        <div class="crm-timeline-content">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="fw-semibold">
                                    {{ $date->format('d M Y') }}
                                    @if($isToday)
                                        <span class="badge bg-danger-subtle text-danger ms-1">
                                            Today
                                        </span>
                                    @elseif($isTomorrow)
                                        <span class="badge bg-warning-subtle text-warning ms-1">
                                            Tomorrow
                                        </span>
                                    @endif
                                </div>

                                <div class="fw-bold text-end">
                                    ₹ {{ number_format(abs($r['amount'])) }}
                                </div>
                            </div>

                            <div class="small text-muted mt-1">
                                @if (!$invoice_id)
                                    <span class="fw-semibold">
                                        {{ $r['project'] }}
                                    </span>
                                    ·
                                @endif
                                {{ $r['customer'] ?? '—' }}
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        @endif

</div> --}}
<div class="col-md-12">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">
                EMI Timeline
            </div>
            <i class="fa-solid fa-calendar-days text-primary"></i>
        </div>

        @if($rows->isEmpty())
            <div class="text-muted small">
                No EMI payments
            </div>
        @else
            <div class="crm-timeline">

                {{-- OVERDUE --}}
                @foreach($rows->where('days', '>', 0) as $r)
                    @include('page.invoices.emi_timeline_item', [
                        'r' => $r,
                        'type' => 'overdue'
                    ])
                @endforeach

                {{-- UPCOMING --}}
                @foreach($rows->where('days', '<=', 0) as $r)
                    @include('page.invoices.emi_timeline_item', [
                        'r' => $r,
                        'type' => 'upcoming'
                    ])
                @endforeach

            </div>
        @endif

</div>
