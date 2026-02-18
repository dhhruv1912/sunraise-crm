    <div class="fw-semibold mb-2">Milestones</div>

    @foreach ($milestones as $m)
        <div class="milestone {{ $m['state'] }}">

            <div class="left">
                <div class="title">{{ $m['title'] }}</div>
                <div class="dates small text-muted">
                    Plan: {{ $m['planned_date'] ?? '—' }}
                    @if ($m['actual_date'])
                        | Done: {{ $m['actual_date'] }}
                    @endif
                </div>
            </div>

            <div class="right">
                @if (!$m['completed'])
                    @can('project.edit')
                        <button class="btn btn-sm btn-outline-success" onclick="completeMilestone('{{ $m['key'] }}')">
                            Complete
                        </button>
                    @endcan
                @else
                    <span class="badge bg-success">Done</span>
                @endif

                @if ($m['slip'] !== null)
                    <span class="badge {{ $m['slip'] > 0 ? 'bg-danger' : 'bg-success' }}">
                        {{ abs($m['slip']) }}d
                    </span>
                @endif
            </div>

        </div>
    @endforeach
