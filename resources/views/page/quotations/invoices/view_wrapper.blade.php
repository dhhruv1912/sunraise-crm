@extends('temp.common')

@section('content')
    <div class="card" id="loaderCard">
        <div class="card-body text-center py-5">
            <h4 class="mb-2">Invoice Details</h4>
            <p class="text-muted mb-4">
                Loading invoice information...
            </p>

            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
    <div class="card d-none" id="contentCard">
        <div class="card-body">
            <h5 class="card-title d-flex align-items-center">
                Invoice: <span id="inv-no"></span>
                <span class="badge bg-primary ms-2" id="inv-status"></span>
            </h5>
            <hr>
            {{-- <h6 class="card-subtitle">Support card subtitle</h6> --}}
            <h6>Invoice Information</h6>
            <table class="table table-sm">
                <tr>
                    <th>Date</th>
                    <td id="inv-date"></td>
                    <th>Due Date</th>
                    <td id="inv-due-date"></td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td id="inv-customer"></td>
                    <th>Project</th>
                    <td id="inv-project"></td>
                </tr>
            </table>

            <hr>

            <h6>Items</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Tax</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="inv-items"></tbody>
            </table>

            <hr>

            <h6>Payments</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody id="inv-payments"></tbody>
            </table>

            <hr>

            <h6>Summary</h6>
            <table class="table table-sm">
                <tr>
                    <th>Subtotal</th>
                    <td id="inv-subtotal"></td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td id="inv-tax"></td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td id="inv-discount"></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td id="inv-total"></td>
                </tr>
                <tr>
                    <th>Paid</th>
                    <td id="inv-paid"></td>
                </tr>
                <tr>
                    <th>Balance</th>
                    <td id="inv-balance"></td>
                </tr>
            </table>

            <hr>
            <div class="d-flex justify-content-end">

                <a id="inv-pdf-link" href="#" target="_blank" class="btn btn-success card-link">Download PDF</a>
                <a id="inv-pdf-link" href="{{ route('invoices.index') }}" class="btn btn-secondary card-link">Close</a>
            </div>

        </div>
    </div>
    </div>

    {{-- Modal --}}
    {{-- @include('page.quotations.invoices.modal') --}}
@endsection


@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let invoiceId = "{{ $id }}";
            loadInvoice(invoiceId);
        });

        async function loadInvoice(id) {
            // try {
                const res = await fetch(`/billing/invoices/${id}/view-json`);
                const json = await res.json();

                if (!json.status) {
                    return alert("Failed to load invoice");
                }

                populateInvoiceModal(json.data);
                document.getElementById("loaderCard").classList.add("d-none")
                document.getElementById("contentCard").classList.remove("d-none")
                // new bootstrap.Modal(document.getElementById('invoiceViewModal')).show();

            // } catch (e) {
            //     console.error(e);
            //     alert("Error loading invoice");
            // }
        }

        function populateInvoiceModal(inv) {
            // Header
            document.getElementById("inv-no").innerHTML = inv.invoice_no;
            document.getElementById("inv-status").innerHTML = inv.status_label;
            document.getElementById("inv-date").innerHTML = inv.invoice_date?.substring(0, 10) ?? "-";
            document.getElementById("inv-due-date").innerHTML = inv.due_date?.substring(0, 10) ?? "-";

            // Customer / Project
            document.getElementById("inv-customer").innerHTML = inv.customer_name ?? "—";
            document.getElementById("inv-project").innerHTML = inv.project_code ?? "—";

            // Amounts
            document.getElementById("inv-subtotal").innerHTML = inv.sub_total;
            document.getElementById("inv-tax").innerHTML = inv.tax_total;
            document.getElementById("inv-discount").innerHTML = inv.discount;
            document.getElementById("inv-total").innerHTML = inv.total;
            document.getElementById("inv-paid").innerHTML = inv.paid_amount;
            document.getElementById("inv-balance").innerHTML = inv.balance;

            // Items
            let tbody = "";
            inv.items.forEach((i, idx) => {
                tbody += `
            <tr>
                <td>${idx + 1}</td>
                <td>${i.description}</td>
                <td>${i.unit_price}</td>
                <td>${i.quantity}</td>
                <td>${i.tax}</td>
                <td>${i.line_total}</td>
            </tr>
        `;
            });
            document.getElementById("inv-items").innerHTML = tbody;

            // Payments
            let payHtml = "";
            inv.payments.forEach(p => {
                payHtml += `
            <tr>
                <td>${p.paid_at ?? "-"}</td>
                <td>${p.amount}</td>
                <td>${p.method ?? "-"}</td>
                <td>${p.reference ?? "-"}</td>
            </tr>
        `;
            });
            document.getElementById("inv-payments").innerHTML = payHtml ||
                `<tr><td colspan="4" class="text-center">No payments</td></tr>`;

            // PDF link
            document.getElementById("inv-pdf-link").href = inv.pdf_url ?? "#";
            document.getElementById("inv-pdf-link").classList.toggle("disabled", !inv.pdf_url);
        }
    </script>
@endsection
