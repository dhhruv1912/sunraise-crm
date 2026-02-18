@extends('temp.common')

@section('title', 'Item Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color: var(--arham-text-heading);">Item Categories</h4>

        <button class="btn"
            style="background: var(--arham-gradient-button); color: white;"
            onclick="openCreateCategoryModal()">
            + Add Category
        </button>
    </div>

    <!-- Search Bar -->
    <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body">
            <input type="text" id="searchCategory" class="form-control"
                placeholder="Search category..."
                style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
        </div>
    </div>

    <!-- Table Container -->
    <div class="card" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body" id="categoryTable">
            <p class="text-muted text-center">Loading...</p>
        </div>
    </div>

@include('page.item_categories.modal_form')

@endsection

@section('scripts')
<script src="{{ asset('assets/js/page/item_categories.js') }}"></script>
@endsection
