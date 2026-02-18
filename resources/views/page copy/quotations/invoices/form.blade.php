@extends('temp.common')

@section('content')
    @php
        $total = [
            'sub' => 0,
            'tax' => 0,
            'grand' => 0,
            'discount' => 0,
        ];
    @endphp
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    @endif
    <div class="card">

        <div class="card-header d-flex align-items-baseline justify-content-between">
            <h4>{{ @$invoice ? 'Edit Invoice' : 'New Invoice' }}</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ @$invoice ? route('invoices.update', $invoice->id) : route('invoices.store') }}">
                @csrf
                <input type="hidden" id="project_id" name="project_id"
                    value="{{ old('project_id', $invoice->project_id ?? '') }}">
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="">Select Project</label>
                        <input type="text" id="projectSearch" autocomplete="off" class="form-control @error('project_id') is-invalid @enderror"
                             value="{{ @$invoice->project->project_code }}"
                            placeholder="Search project...">
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <ul id="projectResults" class="list-group mt-2 position-absolute w-px-500 bg-white"></ul>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control"
                            value="{{ old('invoice_date', @$invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') }}"
                            required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control"
                            value="{{ old('due_date', @$invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}">
                    </div>

                </div>

                <hr>
                <div class="d-flex justify-content-between pb-3 align-items-center">
                    <h5 class="mb-0">Items</h5>
                    <button type="button" id="addItem" class="btn btn-sm btn-primary">Add Item</button>
                </div>
                <div id="itemsWrapper">
                    <div class="row gap-1 my-2 invoice-item">
                        <div class="col-1 desc-input">SKU</div>
                        <div class="col-3 desc-input">Desc</div>
                        <div class="col price-input">Price</div>
                        <div class="col qty-input">Quentity</div>
                        <div class="col tax-input">Tax</div>
                        <div class="col"></div>
                    </div>
                    @if (isset($invoice))
                        @foreach ($invoice->items as $id => $item)
                            <div class="row gap-1 my-1 invoice-item">
                                <input type="hidden" name="items[{{ $id }}][sku]"
                                    value="{{ $item->quote_master_id ?? '' }}">

                                {{-- SKU only shown for first row --}}
                                <div class="col-1">
                                    @if ($id == 0)
                                        <select name="items[{{ $id }}][quote_master_id]"
                                            class="form-select sku-select">
                                            <option value="">Select SKU</option>
                                            @foreach ($quoteMasters as $m)
                                                <option value="{{ $m->id }}" @selected($m->id == $item->quote_master_id)>
                                                    {{ $m->sku }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="items[{{ $id }}][quote_master_id]"
                                            value="{{ $item->quote_master_id }}">
                                    @endif
                                </div>

                                <div class="col-3">

                                    <input name="items[{{ $id }}][description]" value="{{ $item->description }}"
                                        class="form-control desc-input">
                                </div>

                                <div class="col">

                                    <input name="items[{{ $id }}][unit_price]" value="{{ $item->unit_price }}"
                                        class="form-control price-input">
                                </div>

                                <div class="col">

                                    <input name="items[{{ $id }}][quantity]" value="{{ $item->quantity }}"
                                        class="form-control qty-input">
                                </div>

                                <div class="col">

                                    <input name="items[{{ $id }}][tax]" value="{{ $item->tax }}"
                                        class="form-control tax-input">
                                </div>

                                <div class="col d-flex justify-content-around align-items-center">
                                    <button type="button" class="btn btn-danger remove-item w-25">X</button>
                                    <span
                                        class="line-total text-end w-75">{{ number_format($item->unit_price * $item->quantity + $item->tax, 2) }}</span>
                                </div>
                                @php
                                    $total['sub'] = $total['sub'] + $item->unit_price * $item->quantity;
                                    $total['tax'] = $total['tax'] + $item->tax;
                                    $total['grand'] =
                                        $total['grand'] + $item->unit_price * $item->quantity + $item->tax;
                                @endphp
                            </div>
                        @endforeach
                        @php
                            $total['discount'] = $invoice->discount;
                            $total['grand'] = $total['grand'] - $total['discount'];

                        @endphp
                    @else
                        {{-- Default row when invoice not exist --}}
                        <div class="row gap-1 my-1 invoice-item">
                            <input type="hidden" name="items[0][sku]">
                            <div class="col-1">
                                <select name="items[0][quote_master_id]" class="form-select col-2 sku-select">
                                    <option value="">Select SKU</option>
                                    @foreach ($quoteMasters as $m)
                                        <option value="{{ $m->id }}">{{ $m->sku }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                <input name="items[0][description]" class="form-control desc-input">
                            </div>
                            <div class="col">
                                <input name="items[0][unit_price]" class="form-control price-input">
                            </div>
                            <div class="col">
                                <input name="items[0][quantity]" class="form-control qty-input">
                            </div>
                            <div class="col">
                                <input name="items[0][tax]" class="form-control tax-input">
                            </div>

                            <div class="col d-flex justify-content-around align-items-center">
                                <button type="button" class="btn btn-danger remove-item w-25">X</button>
                                <span class="line-total text-end w-75">0.00</span>
                            </div>
                        </div>
                    @endif


                </div>

                <div class="align-items-end d-flex flex-column mt-3 text-end gap-2">
                    <div class="w-25 d-flex justify-content-around align-items-center">
                        <span class="w-25">Subtotal:</span>
                        <span id="subTotal" class="text-end w-75">{{ $total['sub'] }}</span>
                    </div>

                    <div class="w-25 d-flex justify-content-around align-items-center">
                        <span class="w-25">Tax Total:</span>
                        <span id="taxTotal" class="text-end w-75">{{ $total['tax'] }}</span>
                    </div>

                    <div class="w-25 d-flex gap-2 justify-content-around align-items-center">
                        <span class="w-25">Discount:</span>
                        <input id="discount" name="discount" class="form-control d-inline text-end w-75"
                            value=" {{ $total['discount'] }}">
                    </div>

                    <div class="w-25 d-flex justify-content-around align-items-center">
                        <span class="w-25">Grand Total:</span>
                        <span id="grandTotal" class="text-end w-75">{{ $total['grand'] }}</span>
                    </div>

                </div>
                <hr>
                <div class="mb-3">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control">{{ old('notes', @$invoice->notes ?? '') }}</textarea>
                </div>
                <hr>


                <h5>Recurring Invoice</h5>

                <div class="row g-2">
                    <div class="col-md-2">
                        <label>Enabled?</label>
                        <select name="is_recurring" class="form-control">
                            <option value="0" {{ @$invoice->is_recurring ? '' : 'selected' }}>No</option>
                            <option value="1" {{ @$invoice->is_recurring ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Type</label>
                        <select name="recurring_type" class="form-control">
                            <option value="">Select</option>
                            @foreach (['daily', 'weekly', 'monthly', 'yearly', 'custom'] as $t)
                                <option value="{{ $t }}"
                                    {{ @$invoice->recurring_type === $t ? 'selected' : '' }}>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Interval</label>
                        <input name="recurring_interval" class="form-control"
                            value="{{ @$invoice->recurring_interval }}">
                    </div>

                    <div class="col-md-2">
                        <label>Next At</label>
                        <input type="date" name="recurring_next_at" class="form-control"
                            value="{{ @$invoice->recurring_next_at }}">
                    </div>

                    <div class="col-md-3">
                        <label>End At</label>
                        <input type="date" name="recurring_end_at" class="form-control"
                            value="{{ @$invoice->recurring_end_at }}">
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back</a>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <h5>Record Payment</h5>
            <form id="paymentForm" class="{{ @$invoice->status != 'paid' ? '' : 'd-none' }}">
                @csrf
                <input type="hidden" name="invoice_id" id="invoice_id"  value="{{ @$invoice->id }}">
                <input type="hidden" name="emi_date" id="emi_date" value="">
                <div class="row g-2">
                    <div class="col-md-3">
                        @php
                            $paidEmiDates = collect($invoice->payments ?? [])
                                ->pluck('meta.emi_date')
                                ->filter()
                                ->toArray();
                        @endphp
                        <select name="amount" id="amount" class="form-select">
                            <option value="" selected>Select EMI</option>
                            @if (isset($invoice->project))
                                @foreach ($invoice->project->emi as $date => $emi)
                                    <option value="{{ $emi }}" @if (in_array($date, $paidEmiDates)) disabled @endif>
                                        {{ $date }} - {{ $emi }}
                                        @if (in_array($date, $paidEmiDates))
                                            (Paid)
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3"><input name="method" class="form-control" placeholder="Method"></div>
                    <div class="col-md-3"><input name="reference" id="reference" class="form-control"
                            placeholder="Reference">
                    </div>
                    <div class="col-md-2"><input name="paid_at" type="date" class="form-control"
                            value="{{ date('Y-m-d') }}"></div>
                    <div class="col-md-1"><button class="btn btn-primary">Add</button></div>
                </div>
            </form>
            
            @if (isset($invoice->payments))
                <hr>
                <h5>Payments</h5>
                <ul id="paymentsList">
                    @foreach ($invoice->payments as $pay)
                        <li>{{ $pay->paid_at }} — {{ $pay->amount }} ({{ $pay->method }})
                            {{ $pay->meta && $pay->meta['emi_date'] ? 'for EMI on ' . $pay->meta['emi_date'] : '' }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    <script>
        // minimal dynamic items handlers

        const project_id = document.getElementById("project_id").value
        const invoice_id = document.getElementById("invoice_id").value
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await crmFetch(`/billing/invoices/${invoice_id}/payments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            const json = await res.json();
            if (json.status) {
                alert('Payment added');
                location.reload();
            } else {
                alert('Failed');
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'addItem') {
                counter = document.querySelectorAll('.invoice-item') || 0
                const wrapper = document.getElementById('itemsWrapper');
                const row = document.createElement('div');
                row.className = 'row gap-1 my-1 invoice-item';
                row.innerHTML = `
                    
                        <div class="col-1">
                            <input type="hidden" name="items[${counter.length}][sku]">
                            <input type="hidden" name="items[${counter.length}][quote_master_id]">
                        </div>

                        <div class="col-3">
                            <input name="items[${counter.length}][description]" class="form-control desc-input">
                        </div>
                        <div class="col">
                            <input name="items[${counter.length}][unit_price]" class="form-control price-input">
                        </div>
                        <div class="col">
                            <input name="items[${counter.length}][quantity]" class="form-control qty-input">
                        </div>
                        <div class="col">
                            <input name="items[${counter.length}][tax]" class="form-control tax-input">
                        </div>

                        <div class="col d-flex justify-content-around align-items-center">
                            <button type="button" class="btn btn-danger remove-item w-25">X</button>
                            <span class="line-total text-end w-75">0.00</span>
                        </div>
                `;
                wrapper.appendChild(row);
            }
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('.invoice-item').remove();
            }
        });

        async function fetchSku(skuId) {
            const res = await crmFetch(`/billing/sku/${skuId}`);
            return await res.json();
        }

        document.addEventListener("change", async function(e) {
            if (e.target.classList.contains("sku-select")) {
                console.log(e);

                const skuId = e.target.value;
                if (!skuId) return;

                const data = await fetchSku(skuId);
                const row = e.target.closest(".invoice-item");

                row.querySelector('.desc-input').value = data.description;
                row.querySelector('.price-input').value = data.unit_price;
                row.querySelector('.qty-input').value = data.quantity;
                row.querySelector('.tax-input').value = data.tax;

                calculateRow(row);
                calculateTotals();
            }
        });
        document.addEventListener("change", async function(e) {
            if (e.target.id == "amount") {
                const val = e.target.value;
                if (!val) return;
                option = e.target.selectedOptions[0]?.innerText;
                const [date, amount] = option.split(' - ')
                document.getElementById("emi_date").value = date.trim()
                document.getElementById("reference").value = "EMI on " + date.trim()
            }
        });

        function calculateRow(row) {
            const qty = parseFloat(row.querySelector('.qty-input').value || 0);
            const price = parseFloat(row.querySelector('.price-input').value || 0);
            const tax = parseFloat(row.querySelector('.tax-input').value || 0);

            const total = qty * price + tax;
            row.querySelector('.line-total').innerText = total.toFixed(2);
        }

        function calculateTotals() {
            let sub = 0,
                tax = 0;

            document.querySelectorAll('.invoice-item').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value || 0);
                const price = parseFloat(row.querySelector('.price-input').value || 0);
                const t = parseFloat(row.querySelector('.tax-input').value || 0);

                sub += qty * price;
                tax += t;
            });

            const discount = parseFloat(document.getElementById("discount").value || 0);
            const total = sub + tax - discount;

            document.getElementById("subTotal").innerText = sub.toFixed(2);
            document.getElementById("taxTotal").innerText = tax.toFixed(2);
            document.getElementById("grandTotal").innerText = total.toFixed(2);
        }

        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("qty-input") ||
                e.target.classList.contains("price-input") ||
                e.target.classList.contains("tax-input")) {

                const row = e.target.closest(".invoice-item");
                calculateRow(row);
                calculateTotals();
            }
        });

        let projectSearchController = null;

        document.getElementById("projectSearch").addEventListener("input", async function() {
            const q = this.value.trim();
            const box = document.getElementById("projectResults");

            // Clear results if empty
            if (!q) {
                box.innerHTML = "";
                if (projectSearchController) {
                    projectSearchController.abort();
                    projectSearchController = null;
                }
                return;
            }

            // Abort previous request
            if (projectSearchController) {
                projectSearchController.abort();
            }

            projectSearchController = new AbortController();
            const {
                signal
            } = projectSearchController;

            // Show loader
            box.innerHTML = "";
            const loaderWrapper = document.createElement("li");
            loaderWrapper.className = "list-group-item text-center";

            const loader = document.createElement("div");
            loader.className = "spinner-border text-primary";
            loaderWrapper.appendChild(loader);

            box.appendChild(loaderWrapper);

            try {
                const res = await crmFetch(
                    `/ajax/projects/search?q=${encodeURIComponent(q)}`, {
                        signal
                    }
                );

                const data = await res.json();

                box.innerHTML = "";

                data.forEach(p => {
                    const li = document.createElement("li");
                    li.className = "list-group-item list-group-item-action";
                    li.dataset.pro = JSON.stringify(p);

                    li.innerHTML = `
                        <strong>${p.label}</strong><br>
                        <small>${p.sub}</small> — <span>${p.extra}</span>
                    `;

                    li.addEventListener("click", () => {
                        document.getElementById("project_id").value = p.id;
                        document.getElementById("projectSearch").value = p.label;
                        box.innerHTML = ""; // hide results
                    });

                    box.appendChild(li);
                });
            } catch (err) {
                if (err.name !== "AbortError") {
                    console.error("Search error:", err);
                    box.innerHTML = "";
                }
            }
        });
    </script>
@endsection
