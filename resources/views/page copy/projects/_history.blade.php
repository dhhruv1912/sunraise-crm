<ul class="list-group list-group-flush">
@forelse ($project->history as $history)
    <li class="list-group-item">
        <span class="badge bg-label-secondary mb-1 text-capitalize">
                {{ $history->status }}
            </span>
        <div class="fw-semibold">
            {{ $history->notes ?? '-' }}
            
        </div>
        <div class="d-flex justify-content-between mt-2">
            <div class="text-muted small">
                {{ $history->user->fname ?? 'System' }}
                {{ $history->user->lname ?? '' }}
            </div>
    
            <small class="text-muted">
                {{ $history->created_at }} ({{ \Carbon\Carbon::parse($history->created_at)->diffForHumans() }})
            </small>
        </div>
    </li>
@empty
    <li class="list-group-item text-muted">No history available</li>
@endforelse
</ul>
