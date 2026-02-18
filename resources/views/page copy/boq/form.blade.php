@extends('temp.common')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><b>{{$boq->boq_no ?? "Boq "}}</b></h4>
            <button class="btn btn-sm btn-outline-primary add-boq-item">Add Item</button>
        </div>
        <div class="card-body p-3">
            <form method="POST" action="{{ route('projects.boq.store', $project_id) }}">
                @csrf
                <input type="hidden" name="boq_id" id="boq_id" value="{{ $boq->id }}">
                <input type="hidden" name="project_id" id="project_id" value="{{ $project_id }}">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Specification</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="boqItems">
                        <tr>
                            <td style="text-align: center" colspan="7">Loading...</td>
                        </tr>
                    </tbody>
                </table>

                {{-- <button type="submit" class="btn btn-primary">Save BOQ</button> --}}
            </form>
        </div>
    </div>
    <div class="modal fade" id="EditItemModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modalTitel">Add</span> Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="item_id" value="">
                    <div class="mb-2">
                        <label class="form-label" for="item">Item Name</label>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="item" id="itemDropper" class="form-select">
                                    <option value="" selected>Select from drop-down or enter manually</option>
                                    @foreach ($boq_items as $item_name => $item_unit)
                                        <option value="{{ $item_name }}-{{ $item_unit }}">{{ $item_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="itemName" placeholder="Item Name">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="itemUnit" placeholder="Item Unit ">
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label" for="item">Item Rate</label>
                                <input type="number" class="form-control" id="itemRate" placeholder="Item Rate">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="item">Item Quentity</label>
                                <input type="number" class="form-control" id="itemQuentity" placeholder="Item Quentity ">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="item">Item Amount</label>
                                <input type="number" class="form-control" id="itemAmount" placeholder="Item Amount ">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Specification</label>
                        <textarea id="specification" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="saveItem" class="btn btn-warning">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/page/boqs.js') }}"></script>
@endsection