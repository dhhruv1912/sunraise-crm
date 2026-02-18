    <div class="fw-semibold mb-2">Activity</div>

    <div class="crm-timeline">

        @forelse($activities as $a)
            <div class="timeline-item">

                <div class="icon">
                    <i class="fa-solid {{ $a['icon'] }}"></i>
                </div>

                <div class="content">
                    <div class="title">
                        {{ $a['title'] }}
                    </div>

                    <div class="meta">
                        {{ $a['user'] }}
                        · {{ \Carbon\Carbon::parse($a['time'])->diffForHumans() }}
                    </div>
                </div>

            </div>
        @empty
            <div class="text-muted small">
                No activity yet
            </div>
        @endforelse

    </div>
