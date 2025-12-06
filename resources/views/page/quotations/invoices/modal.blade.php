<div class="modal fade" id="invoiceViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">
            Invoice: <span id="inv-no"></span>
            <span class="badge bg-primary ms-2" id="inv-status"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        
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
                    <th>#</th><th>Description</th><th>Unit Price</th><th>Qty</th><th>Tax</th><th>Total</th>
                </tr>
            </thead>
            <tbody id="inv-items"></tbody>
        </table>

        <hr>

        <h6>Payments</h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th><th>Amount</th><th>Method</th><th>Reference</th>
                </tr>
            </thead>
            <tbody id="inv-payments"></tbody>
        </table>

        <hr>

        <h6>Summary</h6>
        <table class="table table-sm">
            <tr><th>Subtotal</th><td id="inv-subtotal"></td></tr>
            <tr><th>Tax</th><td id="inv-tax"></td></tr>
            <tr><th>Discount</th><td id="inv-discount"></td></tr>
            <tr><th>Total</th><td id="inv-total"></td></tr>
            <tr><th>Paid</th><td id="inv-paid"></td></tr>
            <tr><th>Balance</th><td id="inv-balance"></td></tr>
        </table>

      </div>

      <div class="modal-footer">
        <a id="inv-pdf-link" href="#" target="_blank" class="btn btn-success disabled">Download PDF</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
