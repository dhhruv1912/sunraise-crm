<div class="modal fade" id="warehouseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="warehouseForm">

                <div class="modal-header">
                    <h5 class="modal-title">Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="warehouse_id">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="name" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Code</label>
                            <input type="text" id="code" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Address</label>
                            <textarea id="address" class="form-control"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Loaction</label>
                            <input type="text" id="location" class="form-control">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label>Longitude & Latitude</label>
                            <input type="text" id="cords" class="form-control">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label>City</label>
                            <input type="text" id="city" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>State</label>
                            <input type="text" id="state" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Pincode</label>
                            <input type="text" id="pincode" class="form-control">
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
