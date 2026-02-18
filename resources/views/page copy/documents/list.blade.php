@extends('temp.common')

@section('title', 'Documents')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Documents</h4>

            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload</button>
                <a href="{{ route('documents.export') }}" class="btn btn-success">Export CSV</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input id="searchBox" class="form-control" placeholder="Search file name, type, description">
                </div>
                <div class="col-md-2">
                    <select id="filterType" class="form-control">
                        <option value="">All Types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}">{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterProject" class="form-control">
                        <option value="">All Projects</option>
                        {{-- optionally populate via JS/ajax --}}
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="perPage" class="form-control">
                        <option value="10">10 / page</option>
                        <option value="20" selected>20 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>File name</th>
                            <th>Type</th>
                            <th>Project</th>
                            <th>Uploader</th>
                            <th>Size</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="documentsBody"></tbody>
                </table>
            </div>

            <nav id="documentsPagination" class="my-2"></nav>
        </div>
    </div>

@include('page.documents.upload-modal')
@include('page.documents.view-modal')
@endsection

@section('scripts')
<script src="{{ asset('assets/js/page/documents.js') }}"></script>
@endsection
