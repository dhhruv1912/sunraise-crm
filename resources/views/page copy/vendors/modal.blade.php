<div class="modal fade" id="vendorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="vendorForm">

                <div class="modal-header">
                    <h5 class="modal-title">Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="vendor_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="name" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Company Name</label>
                            <input type="text" id="company_name" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" id="phone" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>GST</label>
                            <input type="text" id="gst_number" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>PAN</label>
                            <input type="text" id="pan_number" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Address</label>
                            <textarea id="address" class="form-control"></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Type</label>
                            <input type="text" id="type" class="form-control">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
