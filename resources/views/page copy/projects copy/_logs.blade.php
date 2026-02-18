@php
    $iconMap = [
        'status'   => 'mdi-chart-timeline-variant',
        'assign'   => 'mdi-account-switch',
        'document' => 'mdi-file-document',
        'note'     => 'mdi-note-text',
        'create'   => 'mdi-plus-circle',
        'update'   => 'mdi-pencil',
        'default'  => 'mdi-information-outline'
    ];
@endphp

<div class="timeline mt-3">

    @forelse($project->history()->orderBy('id','desc')->get() as $log)
        @php
            $type = $log->type ?? 'default';
            $icon = $iconMap[$type] ?? $iconMap['default'];

            // Determine background color
            $bg = match($type) {
                'status'   => 'bg-label-info',
                'assign'   => 'bg-label-warning',
                'document' => 'bg-label-primary',
                'note'     => 'bg-label-success',
                'create'   => 'bg-label-secondary',
                'update'   => 'bg-label-dark',
                default    => 'bg-label-secondary',
            };
        @endphp

        <div class="timeline-item d-flex mb-4">
            {{-- Left: Icon Bubble --}}
            <div class="flex-shrink-0 timeline-point {{ $bg }}">
                <i class="mdi {{ $icon }}"></i>
            </div>

            {{-- Right: Content --}}
            <div class="flex-grow-1 ms-3">

                <div class="d-flex justify-content-between">
                    <h6 class="mb-1 fw-bold">
                        {{ $log->title ?? ucfirst($log->type ?? 'log') }}
                    </h6>

                    <small class="text-muted">
                        {{ $log->created_at->format('d M Y – h:i A') }}
                    </small>
                </div>

                <div class="text-muted small">
                    {!! nl2br(e($log->message)) !!}
                </div>

                @if($log->meta)
                    <div class="mt-2">
                        <pre class="bg-light p-2 rounded small">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

            </div>
        </div>
    @empty
        <p class="text-muted text-center py-4">No logs available.</p>
    @endforelse
</div>
