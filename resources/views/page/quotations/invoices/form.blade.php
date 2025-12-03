@extends('temp.common')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-baseline justify-content-between">
            <h4>{{ @$invoice ? 'Edit Invoice' : 'New Invoice' }}</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" id="attachProjectBtn">
                    Attach Project
                </button>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ @$invoice ? route('invoices.update', $invoice->id) : route('invoices.store') }}">
                @csrf
                <input type="hidden" id="project_id" name="project_id"
                    value="{{ old('project_id', $invoice->project_id ?? '') }}">
                <div class="mb-3">
                    <label>Invoice Date</label>
                    <input type="date" name="invoice_date" class="form-control"
                        value="{{ old('invoice_date', @$invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') }}"
                        required>
                </div>
                <div class="mb-3">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ old('due_date', @$invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}">
                </div>

                <hr>
                <h5>Items</h5>
                <div id="itemsWrapper">
                    @if (isset($invoice))
                        @foreach ($invoice->items as $id => $item)
                            <div class="row mb-2 invoice-item">
                                <input type="hidden" name="items[{{ $id }}][quote_master_id]"
                                    value="{{ $item->quote_master_id }}">
                                <div class="col-6"><input name="items[{{ $id }}][description]"
                                        class="form-control items-description" value="{{ $item->description }}"></div>
                                <div class="col-2"><input name="items[{{ $id }}][unit_price]"
                                        class="form-control" value="{{ $item->unit_price }}"></div>
                                <div class="col-2"><input name="items[{{ $id }}][quantity]" class="form-control"
                                        value="{{ $item->quantity }}"></div>
                                <div class="col-2"><button type="button" class="btn btn-danger remove-item">X</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="d-flex gap-2 mb-2 invoice-item">
                            {{-- <div class="col-6"><input name="items[0][description]" class="form-control items-description"></div>
            <div class="col-2"><input name="items[0][unit_price]" type="number" class="form-control"></div>
            <div class="col-2"><input name="items[0][quantity]" type="number" class="form-control" value="1"></div>
            <div class="col-2"><button type="button" class="btn btn-danger remove-item">X</button></div> --}}
                            <select name="items[0][quote_master_id]" class="form-select sku-select">
                                <option value="">Select SKU</option>
                                @foreach ($quoteMasters as $m)
                                    <option value="{{ $m->id }}">{{ $m->sku }}</option>
                                @endforeach
                            </select>

                            <input name="items[0][description]" class="form-control desc-input">
                            <input name="items[0][unit_price]" class="form-control price-input">
                            <input name="items[0][quantity]" class="form-control qty-input">
                            <input name="items[0][tax]" class="form-control tax-input">

                            <div class="col-2"><button type="button" class="btn btn-danger remove-item">X</button></div>
                            <span class="line-total">0.00</span>
                        </div>
                    @endif
                </div>

                <button type="button" id="addItem" class="btn btn-sm btn-primary">Add Item</button>
                <div class="mt-3 text-end">
                    <p>Subtotal: <span id="subTotal">0.00</span></p>
                    <p>Tax Total: <span id="taxTotal">0.00</span></p>

                    <label>Discount</label>
                    <input id="discount" name="discount" class="form-control w-25 d-inline"
                        value="{{ @$invoice->discount ?? 0 }}">

                    <p class="mt-2 fw-bold">
                        Grand Total: <span id="grandTotal">0.00</span>
                    </p>
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
                        <input name="recurring_interval" class="form-control" value="{{ @$invoice->recurring_interval }}">
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
    <div class="modal fade" id="projectAttachModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Select Project</h5>
                </div>
                <div class="modal-body">
                    <input type="text" id="projectSearch" class="form-control" placeholder="Search project...">
                    <ul id="projectResults" class="list-group mt-2"></ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // minimal dynamic items handlers
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'addItem') {
                counter = document.querySelectorAll('.invoice-item') || 0
                const wrapper = document.getElementById('itemsWrapper');
                const row = document.createElement('div');
                row.className = 'd-flex gap-2 mb-2 invoice-item';
                row.innerHTML = `
                    <select name="items[${counter.length}][quote_master_id]" class="form-select sku-select">
                        <option value="">Select SKU</option>
                        @foreach ($quoteMasters as $m)
                            <option value="{{ $m->id }}">{{ $m->sku }}</option>
                        @endforeach
                    </select>

                    <input name="items[${counter.length}][description]" class="form-control desc-input">
                    <input name="items[${counter.length}][unit_price]" class="form-control price-input">
                    <input name="items[${counter.length}][quantity]" class="form-control qty-input">
                    <input name="items[${counter.length}][tax]" class="form-control tax-input">
                    <div class="col-2"><button type="button" class="btn btn-danger remove-item">X</button></div>
                    <span class="line-total">0.00</span>
                `;
                wrapper.appendChild(row);
            }
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('.invoice-item').remove();
            }
        });

        async function fetchSku(skuId) {
            const res = await fetch(`/billing/sku/${skuId}`);
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
        document.getElementById("attachProjectBtn").onclick = () => {
            new bootstrap.Modal(document.getElementById("projectAttachModal")).show();
        };

        document.getElementById("projectSearch").addEventListener("input", async function() {
            const q = this.value;
            if (!q.trim()) return;

            let res = await fetch(`/ajax/projects/search?q=${encodeURIComponent(q)}`);
            let data = await res.json();
            console.log();

            let box = document.getElementById("projectResults");
            box.innerHTML = "";

            data.forEach(p => {
                let li = document.createElement("li");
                li.className = "list-group-item list-group-item-action";
                li.dataset.pro = JSON.stringify(p);
                li.innerHTML = `
            <strong>${p.label}</strong><br>
            <small>${p.sub}</small> — <span>${p.extra}</span>
        `;
                li.onclick = () => {
                    document.getElementById("project_id").value = p.id;
                    bootstrap.Modal.getInstance(document.getElementById("projectAttachModal"))
                    .hide();
                };
                box.appendChild(li);
            });
        });
    </script>
@endsection
