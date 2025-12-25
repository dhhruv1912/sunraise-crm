<div class="card mb-2">
    <div class="card-header">Boq Details :</div>
    <hr class="m-0">
    <div class="card-body">
        <div class="table-responsive text-nowrap pb-2">
            <table class="table table-striped">
                <thead>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                        <td>Boq No.</td>
                        <td>{{ $project->boqs->boq_no }}
                        </td>
                        <td>Boq Date</td>
                        <td>{{ $project->boqs->boq_date }}
                        </td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td>{{ $project->boqs->total_amount }}
                        </td>
                        <td>PDF</td>
                        <td>
                            @if ($project->boqs->pdf_path)
                                <a href="{{ asset('storage/' . $project->boqs->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-info">{{ $project->boqs->pdf_path }}</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Created Date</td>
                        <td>{{ $project->boqs->created_at }}
                        </td>
                        <td>Last Updated</td>
                        <td>{{ $project->boqs->updated_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card mb-2">
    <div class="card-header d-flex align-items-center justify-content-between">Boq Items : <a target="_blank"
            href="{{ route('projects.boq.edit.boq', [$project->id, $project->boqs->id]) }}" type="button"
            class="btn btn-sm btn-outline-primary">Edit</a></div>
    <hr class="m-0">
    <div class="card-body">
        <div class="row">
            <div class="col table-responsive text-nowrap pb-2">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Quentity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if ($project->boqs->items && count($project->boqs->items) > 0)
                            @foreach ($project->boqs->items as $index => $item)
                                {{-- @dump($item->toArray()) --}}
                                <tr>
                                    <td>{{ $index }}</td>
                                    <td>{{ $item->item }}</td>
                                    <td>{{ $item->quantity }} {{ $item->unit ?? '' }}</td>
                                    <td>{{ $item->rate }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>No Boq Items Available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="col-md-5 h-px-400">
                @if ($project->boqs->pdf_path)
                    <div class="h-100" id="pdf9" style="">
                        <embed src="{{ asset('storage/' . $project->boqs->pdf_path) }}" width="100%"
                            class="border border-3 rounded-3 h-100">
                    </div>
                @else
                    <div class="h-100 d-flex flex-column justify-content-center align-items-center border border-3 rounded-3">
                        BOQ File is not generated yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
