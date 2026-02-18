<h2>Bill of Quantities</h2>
<p>BOQ No: {{ $boq->boq_no }}</p>
<p>Date: {{ $boq->boq_date }}</p>

<table width="100%" border="1" cellspacing="0">
<tr>
    <th>#</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Rate</th>
    <th>Amount</th>
</tr>

@foreach($boq->items as $i => $item)
<tr>
    <td>{{ $i+1 }}</td>
    <td>{{ $item->item }}</td>
    <td>{{ $item->quantity }}</td>
    <td>{{ $item->rate }}</td>
    <td>{{ $item->amount }}</td>
</tr>
@endforeach

<tr>
    <td colspan="4"><strong>Total</strong></td>
    <td>{{ $boq->total_amount }}</td>
</tr>
</table>
