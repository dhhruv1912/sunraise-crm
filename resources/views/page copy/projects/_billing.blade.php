{{-- @dump($project->invoices->toArray()) --}}
@php
    use Carbon\Carbon;
@endphp
<div class="">
    <div class="table-responsive text-nowrap pb-2">
        <table class="table table-striped">
            <thead>
            </thead>
            <tbody class="table-border-bottom-0">
                <tr>
                    <td>Finalize Price</td>
                    <td>{{ number_format($project->finalize_price, 2) }}</td>
                    <td>Invoice Generated (Rs)</td>
                    <td>{{ number_format($project->invoices->total, 2) }}</td>
                </tr>
                <tr>
                    <td>Amount Paid</td>
                    <td>{{ number_format($project->invoices->total - $project->invoices->balance, 2) }}</td>
                    <td>Amount Remaining</td>
                    <td>{{ number_format($project->invoices->balance, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if ($project->invoices)
        @php
            $invoice = $project->invoices;
        @endphp
        <div class="card">
            <div class="card-header">Invoice No :
                <a target="_blank" href="{{ route('invoices.edit', $invoice->id) }}"><b>{{ $invoice->invoice_no }}
                        <span class="mdi mdi-open-in-new"></span></b></a>
            </div>
            <hr class="m-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row mx-0">
                            <div class="col-4">
                                <h6>Invoice Date</h6>
                            </div>
                            <div class="col-8">{{ $invoice->invoice_date }}</div>
                        </div>
                        <div class="row mx-0">
                            <div class="col-4">
                                <h6>Status</h6>
                            </div>
                            <div class="col-8">{{ $invoice->status }}</div>
                        </div>
                        <div class="row mx-0">
                            <div class="col-4">
                                <h6>Created By</h6>
                            </div>
                            <div class="col-8">{{ $invoice->creator->fname }} {{ $invoice->creator->lname }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mx-0">
                            <div class="col-4">
                                <h6>Due Date</h6>
                            </div>
                            <div class="col-8">{{ $invoice->due_date }}</div>
                        </div>
                        <div class="row mx-0">
                            <div class="col-4">
                                <h6>Balance</h6>
                            </div>
                            <div class="col-8">{{ number_format($invoice->balance, 2) }}</div>
                        </div>
                        <div class="row mx-0">
                            <div class="col-4">
                                <h6>Sent By</h6>
                            </div>
                            <div class="col-8">{{ $invoice->sent_by }}</div>
                        </div>
                    </div>
                </div>
                <hr>
                @if ($invoice->items && $invoice->items->count())
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 600px">description</th>
                                    <th>unit price</th>
                                    <th>quantity</th>
                                    <th>tax</th>
                                    <th>line total</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($invoice->items as $idx => $items)
                                    <tr>
                                        <td>{{ $items->description }}</td>
                                        <td>{{ number_format($items->unit_price, 2) }}</td>
                                        <td>{{ $items->quantity }}</td>
                                        <td>{{ number_format($items->tax, 2) }}</td>
                                        <td>{{ number_format($items->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th colspan="2"></th>
                                    <th colspan="2">sub total</th>
                                    <th>{{ number_format($invoice->sub_total, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="2"></th>
                                    <th colspan="2">tax total</th>
                                    <th>+ {{ number_format($invoice->tax_total, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="2"></th>
                                    <th colspan="2">discount</th>
                                    <th>- {{ number_format($invoice->discount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="2"></th>
                                    <th colspan="2">Total</th>
                                    <th>{{ number_format($invoice->total, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>

        <div class="card mt-3">
            <h5 class="card-header">EMI Schedule</h5>
            <hr class="m-0">
            @php
                $paidEmiDates = collect($invoice->payments ?? [])
                    ->mapWithKeys(function ($p) {
                        return [
                            data_get($p->meta, 'emi_date') => $p->paid_at,
                        ];
                    })
                    ->filter()
                    ->toArray();
            @endphp
            <div class="d-flex">
                <div class="table-responsive text-nowrap  col-md-8">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @php
                                $index = 0;
                            @endphp
                            @foreach ($project->emi as $date => $emi)
                                @php

                                    $today = Carbon::today();
                                    $emiDate = Carbon::parse($date);

                                    $icon = '';
                                    $status = '';
                                    $message = '';

                                    if (isset($paidEmiDates[$date])) {
                                        $paidAt = Carbon::parse($paidEmiDates[$date]);
                                        $diffDays = $paidAt->diffInDays($emiDate, false);

                                        if ($paidAt->lt($emiDate)) {
                                            // 🔵 Advance paid
                                            $message = 'EMI payment was paid ' . abs($diffDays) . ' days in advance.';
                                            $status = 'primary';
                                            $icon = 'mdi mdi-clock-fast';
                                        } elseif ($paidAt->isSameDay($today)) {
                                            // 🟢 Paid today
                                            $message = 'EMI payment was paid today.';
                                            $status = 'success';
                                            $icon = 'mdi mdi-clock-check-outline';
                                        } else {
                                            // ✅ Paid (normal / late)
                                            $daysAgo = $paidAt->diffInDays($today);
                                            $message = "EMI payment was paid $daysAgo days ago.";
                                            $status = 'success';
                                            $icon = 'mdi mdi-clock-check-outline';
                                        }
                                    } else {
                                        $diff = $emiDate->diffInDays($today, false);

                                        if ($diff === 0) {
                                            // 🟡 Due today
                                            $message = 'EMI payment is due today.';
                                            $status = 'warning';
                                            $icon = 'mdi mdi-clock-star-four-points-outline';
                                        } elseif ($diff > 0) {
                                            // 🔴 Overdue
                                            $message = "EMI payment was overdue by $diff days.";
                                            $status = 'danger';
                                            $icon = 'mdi mdi-clock-alert-outline';
                                        } else {
                                            // 🔵 Upcoming
                                            $message = 'EMI payment is due in ' . abs($diff) . ' days.';
                                            $status = 'info';
                                            $icon = 'mdi mdi-clock-plus-outline';
                                        }
                                    }

                                    $index++;
                                @endphp

                                <tr>
                                    <td class="text-{{ $status }}">{{ $index }}</td>
                                    <td class="text-{{ $status }}">
                                        <i
                                            class="icon-base {{ $icon }} icon-22px me-3"></i>
                                        <span>{{ number_format($emi, 2) }}</span>
                                    </td>
                                    <td class="text-{{ $status }}">{{ date_create($date)->format('D d, M Y') }}
                                    </td>
                                    <td class="text-{{ $status }}">
                                        {{ $message }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>-</td>
                                <td>
                                    <i class="icon-base mdi mdi-cash-clock icon-22px text-warning me-3"></i>
                                    <span>{{ number_format($project->finalize_price, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-label-primary me-1"></span>
                                </td>
                                <td>
                                    Final Price
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="container d-flex justify-content-center align-items-center col-md-4 px-1">
                    <div class="calendar card shadow w-100">
                        <div class="front">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <button id="prevMonth" class="btn btn-sm btn-outline-secondary">
                                    &laquo;
                                </button>

                                <h6 id="monthTitle" class="mb-0 fw-semibold"></h6>

                                <button id="nextMonth" class="btn btn-sm btn-outline-secondary">
                                    &raquo;
                                </button>
                            </div>
                            <!-- Weekdays -->
                            <ul class="bg-info-subtle d-flex fw-semibold list-unstyled mb-0 py-3 text-center week-days">
                                <li class="cell">MON</li>
                                <li class="cell">TUE</li>
                                <li class="cell">WED</li>
                                <li class="cell">THU</li>
                                <li class="cell">FRI</li>
                                <li class="cell">SAT</li>
                                <li class="cell">SUN</li>
                            </ul>

                            <!-- Calendar grid -->
                            <div class="weeks d-flex flex-column pb-4" id="calendarWeeks">
                                <!-- JS injects weeks here -->
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <h5 class="card-header">Payment History</h5>
            <hr class="m-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Recieved By</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($invoice->payments as $index => $paym)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <i class="icon-base mdi mdi-cash-check icon-22px text-success me-3"></i>
                                    {{-- <i class="icon-base mdi mdi-cash-clock icon-22px text-danger me-3"></i> --}}
                                    <span>{{ number_format($paym->amount, 2) }}</span>
                                </td>
                                <td>{{ $paym->paid_at }}</td>
                                <td>
                                    {{ $paym->method }}
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-label-primary me-1">{{ $paym->receiver->fname }}
                                        {{ $paym->receiver->lname }}</span>
                                </td>
                                <td>
                                    {{ $paym->reference }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>-</td>
                            <td>
                                {{-- <i class="icon-base mdi mdi-cash-check icon-22px text-success me-3"></i> --}}
                                <i class="icon-base mdi mdi-cash-clock icon-22px text-warning me-3"></i>
                                <span>{{ number_format($invoice->balance, 2) }}</span>
                            </td>
                            <td colspan="4">
                                Amount is due
                            </td>
                        </tr>
                        <tr>
                            <td>-</td>
                            <td>
                                <i class="icon-base mdi mdi-cash-check icon-22px text-success me-3"></i>
                                <span>{{ number_format($project->invoices->total - $project->invoices->balance, 2) }}</span>
                            </td>
                            <td colspan="4">
                                Amount Paid
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@php
    $EmiDates = array_keys(
        collect($project->emi ?? [])
            ->filter()
            ->toArray(),
    );
@endphp
<script>
    EMI_DATES = @json($EmiDates);
    EMIS = @json($project->emi);
    PAID_EMI_DATES = @json($paidEmiDates);
</script>
