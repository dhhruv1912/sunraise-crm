<tr data-id="{{ $project->id }}">
    <td>{{ $project->project_code }}</td>
    <td>{{ $project->customer_name }}</td>
    <td>{{ $project->mobile }}</td>
    <td>{{ $project->kw ?? '—' }}</td>
    <td>
        <select class="form-select assignee-select" data-id="{{ $project->id }}">
            <option value="">—</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ $project->assignee == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="form-select status-select" data-id="{{ $project->id }}">
            @foreach(\App\Models\Project::STATUS_LABELS as $k => $label)
                <option value="{{ $k }}" {{ $project->status == $k ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </td>
    <td>{{ $project->created_at->format('Y-m-d') }}</td>
    <td>
        <button class="btn btn-sm btn-info view-btn" data-id="{{ $project->id }}">View</button>
        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-primary">Edit</a>
        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $project->id }}">Delete</button>
    </td>
</tr>
