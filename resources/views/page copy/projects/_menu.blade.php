
<div class="control-group">
    <span class="control-label">Assignee</span>
    <select class="form-select form-select-sm assignee-select" data-id="{{ $project->id }}">
        <option value="">—</option>
    </select>
</div>

<div class="control-group">
    <span class="control-label">Reporter</span>
    <select class="form-select form-select-sm reporter-select" data-id="{{ $project->id }}">
        <option value="">—</option>
    </select>
</div>

<div class="control-group">
    <span class="control-label">Status</span>
    <select class="form-select form-select-sm status-select" data-id="{{ $project->id }}"></select>
</div>

<div class="control-group">
    <span class="control-label">Priority</span>
    <select class="form-select form-select-sm priority-select" data-id="{{ $project->id }}">
        <option value="low"    {{ $project->priority == 'low' ? 'selected' : '' }}>Low</option>
        <option value="medium" {{ $project->priority == 'medium' ? 'selected' : '' }}>Medium</option>
        <option value="high"   {{ $project->priority == 'high' ? 'selected' : '' }}>High</option>
    </select>
</div>

<div class="control-group gap-2 switch-group">
    <span class="control-label">Hold</span>
    <label class="form-switch m-0 pb-1">
        <input class="form-check-input" type="checkbox" id="HoldProject" {{ $project->is_on_hold ? 'checked' : '' }}>
        <span class="switch-ui"></span>
    </label>
</div>
