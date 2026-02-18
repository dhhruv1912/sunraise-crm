<div class="timeline">
    @foreach($project->history()->orderBy('created_at','asc')->get() as $h)
        <div class="mb-3">
            <div><small class="text-muted">{{ $h->created_at->format('d M Y H:i') }}</small></div>
            <div><strong>{{ $h->status }}</strong> — {{ $h->notes }}</div>
        </div>
    @endforeach
</div>
