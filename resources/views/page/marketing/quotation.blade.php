@if (count($quotations) > 0)
    @foreach ($quotations as $id => $quotation)
        <div class="row {{ ($id % 2) === 1 ?  "bg-label-light" : "bg-label-secondary" }}">
            <div class="col-12 my-2 pt-2 d-flex align-items-baseline justify-content-between">
                <h6>{{ $id + 1 }}. {{ $quotation->quotation_no }}</h6>
                <a href="{{ route('quotations.index',$quotation->id) }}" class="btn btn-sm btn-info"> Go to Quotations</a>
            </div>
            <hr>
                <table class="table ">
                    <tbody>
                        <tr class="row">
                            <td class="col-4">Sent </td>
                            <td class="col-6">{{ $quotation->sentBy ? "Yes" : "No" }}</td>
                        </tr>
                        @if ($quotation->sentBy)
                            
                        <tr class="row">
                            <td class="col-4">Sent By</td>
                            <td class="col-6">{{ $quotation->sentBy->fname }} {{ $quotation->sentBy->lname }}</td>
                        </tr>
                        <tr class="row">
                            <td class="col-4">Sent At</td>
                            <td class="col-6">{{ $quotation->sent_at }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                {{-- @dump($quotation->toArray()) --}}
                @if ($quotation->pdf_path)
                    {{-- {{ $quotation->pdf_path }} --}}
                    <embed src="{{ asset('storage/' . $quotation->pdf_path) }}" frameborder="0" width="100%"
                        class="border border-3 rounded-3 h-100">
                @else
                    <div class="w-100 h-px-400 d-flex flex-column justify-content-center align-items-center border border-3 rounded-3">PDF Is not cretaed yet. click this button to generate PDF
                        <button class="btn btn-sm btn-secondary" onclick="generatePdf({{ $quotation->id }})">PDF</button>
                    </div>
                @endif
        </div>
    @endforeach
@else
@endif
