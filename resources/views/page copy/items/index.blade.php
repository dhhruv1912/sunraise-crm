@extends('temp.common')

@section('title', 'Items')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color: var(--arham-text-heading);">Items</h4>

        <button class="btn"
            style="background: var(--arham-gradient-button); color:white;"
            onclick="openCreateItemModal()">
            + Add Item
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body row g-3">

            <div class="col-md-4">
                <input type="text" class="form-control" id="itemSearch"
                    placeholder="Search item..."
                    style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
            </div>

            <div class="col-md-4">
                <select class="form-select" id="filterCategory"
                    style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    <!-- Table -->
    <div class="card" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body" id="itemsTable">
            <p class="text-center text-muted">Loading...</p>
        </div>
    </div>

@include('page.items.modal_form')

@endsection

@section('scripts')
<script src="{{ asset('assets/js/page/items.js') }}"></script>
@endsection
