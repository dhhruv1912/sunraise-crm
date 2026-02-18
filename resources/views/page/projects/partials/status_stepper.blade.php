    <div class="fw-semibold mb-3">Project Status</div>

    <div class="crm-stepper">
        @foreach($stepper as $step)
            <div class="crm-step {{ $step['state'] }}">
                <div class="dot"></div>

                <div class="label">
                    {{ $step['label'] }}
                    @if($step['changed_at'])
                        <div class="date">
                            {{ $step['changed_at'] }}
                        </div>
                    @endif
                </div>
                @can("project.edit")
                    @if($step['state'] === 'current')
                        <button class="btn btn-sm btn-outline-primary mt-1"
                                onclick="moveToNextStatus()">
                            Mark Complete
                        </button>
                    @endif
                @endcan
            </div>
        @endforeach
    </div>
