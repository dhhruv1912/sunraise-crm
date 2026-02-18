<div id="project-history-list">
    @foreach($project->history()->with('changedBy')->orderBy('created_at','desc')->get() as $h)
        <div class="mb-2">
            <div><strong>{{ $h->status }}</strong> <small class="text-muted">by {{ optional($h->changedBy)->name ?? 'system' }} — {{ $h->created_at->diffForHumans() }}</small></div>
            <div>{{ $h->notes }}</div>
        </div>
    @endforeach
</div>
