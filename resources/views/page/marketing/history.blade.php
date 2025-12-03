@foreach($history as $h)
<div class="border rounded p-2 mb-2">
    <div>
        <strong>{{ $h->action }}</strong>
    </div>
    <div>{{ $h->message }}</div>
    <div class="text-muted small">
        {{ $h->created_at->format('d M Y h:i A') }}
        @if($h->user) — by {{ $h->user->name }} @endif
    </div>
</div>
@endforeach
