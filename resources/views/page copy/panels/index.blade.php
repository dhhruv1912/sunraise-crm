@extends('temp.common')

@section('title', 'Solar Panels Inventory')

@section('content')
<div class="container-fluid py-3">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color: var(--arham-text-heading)">Solar Panels Inventory</h4>

        <a href="{{ route('panels.receive.uploadPage') }}" 
           class="btn"
           style="
               background: var(--arham-gradient-button);
               color: var(--arham-white);
               border-radius: 8px;
           ">
            + Receive New Panels
        </a>
    </div>

    <!-- FILTERS -->
    <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted">Search Serial</label>
                    <input type="text" id="search" class="form-control"
                        placeholder="Search..." 
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted">Item Type</label>
                    <select id="filter_item_id" class="form-select"
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                        <option value="">All</option>
                        @foreach($items as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted">Warehouse</label>
                    <select id="filter_warehouse_id" class="form-select" 
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                        <option value="">All</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted">Status</label>
                    <select id="filter_status" class="form-select"
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                        <option value="">All</option>
                        <option value="in_stock">In Stock</option>
                        <option value="sold">Sold</option>
                        <option value="returned">Returned</option>
                        <option value="damaged">Damaged</option>
                        <option value="removed">Removed</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body">
            <div id="panelsTable">
                {{-- Table loaded via AJAX --}}
                <p class="text-center text-muted">Loading panels...</p>
            </div>
        </div>
    </div>

</div>
@endsection
