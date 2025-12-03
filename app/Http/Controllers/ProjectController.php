<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Lead;
use App\Models\Document;
use App\Models\ProjectHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Show projects list page.
     */
    public function index()
    {
        // status labels from model
        $statuses = Project::STATUS_LABELS;
        $users = User::orderBy('fname')->get(['id','fname','lname']);
        return view('page.projects.list', compact('statuses','users'));
    }

    /**
     * AJAX: paginated list with filters / search
     */
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $query = Project::query();

        if ($q = $request->get('search')) {
            $query->where(function($s) use ($q) {
                $s->where('customer_name', 'like', "%{$q}%")
                  ->orWhere('project_code', 'like', "%{$q}%")
                  ->orWhere('mobile', 'like', "%{$q}%")
                  ->orWhere('address', 'like', "%{$q}%");
            });
        }

        if ($status = $request->get('filter_status')) {
            $query->where('status', $status);
        }

        if ($assignee = $request->get('filter_assignee')) {
            $query->where('assignee', $assignee);
        }

        if ($from = $request->get('filter_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('filter_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $data = $query->orderBy('id','desc')->paginate($perPage);
        // For each project add small metadata used by JS view
        $data->getCollection()->transform(function($p){
            $p->status_label = $p->status_label;
            $p->assignee_name = $p->assigneeUser ? $p->assigneeUser->name : null;
            return $p;
        });

        return response()->json($data);
    }

    /**
     * Create form
     */
    public function create()
    {
        $users = User::orderBy('fname')->get(['id','fname','lname']);
        $statuses = Project::STATUS_LABELS;
        return view('page.projects.form', compact('users','statuses'));
    }

    /**
     * Store new project
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'lead_id' => 'nullable|exists:leads,id',
            'project_code' => 'nullable|string|unique:projects,project_code',
            'customer_name' => 'required|string',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'kw' => 'nullable|numeric',
            'module_brand' => 'nullable|string',
            'inverter_brand' => 'nullable|string',
            'module_count' => 'nullable|integer',
            'assignee' => 'nullable|exists:users,id',
            'reporter' => 'nullable|exists:users,id',
            'status' => 'nullable|in:'.implode(',',array_keys(Project::STATUS_LABELS)),
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $payload = $request->only([
            'lead_id','project_code','customer_name','mobile','address',
            'kw','module_brand','inverter_brand','module_count','assignee','reporter','status',
            'project_value','finalize_price','emi','project_note'
        ]);

        if (empty($payload['project_code'])) {
            $payload['project_code'] = 'PRJ-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        }

        $project = Project::create($payload);

        // create history
        ProjectHistory::create([
            'project_id' => $project->id,
            'status' => $project->status ?? 'new',
            'changed_by' => auth()->id() ?? null,
            'notes' => 'Project created'
        ]);

        return redirect()->route('projects.index')->with('success','Project created.');
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $users = User::orderBy('fname')->get(['id','fname','lname']);
        $statuses = Project::STATUS_LABELS;
        return view('page.projects.form', compact('project','users','statuses'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $v = Validator::make($request->all(), [
            'project_code' => 'nullable|string|unique:projects,project_code,'.$project->id,
            'customer_name' => 'required|string',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'kw' => 'nullable|numeric',
            'module_brand' => 'nullable|string',
            'inverter_brand' => 'nullable|string',
            'module_count' => 'nullable|integer',
            'assignee' => 'nullable|exists:users,id',
            'reporter' => 'nullable|exists:users,id',
            'status' => 'nullable|in:'.implode(',',array_keys(Project::STATUS_LABELS)),
        ]);
        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $project->update($request->only([
            'project_code','customer_name','mobile','address',
            'kw','module_brand','inverter_brand','module_count','assignee','reporter','status','finalize_price','emi','project_note'
        ]));

        ProjectHistory::create([
            'project_id' => $project->id,
            'status' => $project->status,
            'changed_by' => auth()->id() ?? null,
            'notes' => 'Project updated'
        ]);

        return redirect()->route('projects.index')->with('success','Project updated.');
    }

    /**
     * HTML show page
     */
    public function view($id)
    {
        $project = Project::with(['documents','history','assigneeUser','reporterUser'])->findOrFail($id);
        return view('page.projects.view', compact('project'));
    }

    /**
     * AJAX JSON for modal
     */
    public function viewJson($id)
    {
        $p = Project::with(['assigneeUser','reporterUser','documents','history'])->findOrFail($id);
        $p->assignee_name = $p->assigneeUser ? $p->assigneeUser->name : null;
        $p->reporter_name = $p->reporterUser ? $p->reporterUser->name : null;
        return response()->json($p);
    }

    /**
     * Assign project to user (AJAX)
     */
    public function assign(Request $request, $id)
    {
        $request->validate(['assignee' => 'required|exists:users,id']);
        $project = Project::findOrFail($id);
        $from = $project->assignee;
        $project->assignee = $request->assignee;
        $project->save();

        ProjectHistory::create([
            'project_id' => $project->id,
            'status' => $project->status,
            'changed_by' => auth()->id() ?? null,
            'notes' => "Assigned to user {$request->assignee}"
        ]);

        return response()->json(['status'=>true,'message'=>'Assigned']);
    }

    /**
     * Change status (AJAX)
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate(['status'=>'required|in:'.implode(',',array_keys(Project::STATUS_LABELS))]);
        $project = Project::findOrFail($id);
        $old = $project->status;
        $project->status = $request->status;
        $project->save();

        ProjectHistory::create([
            'project_id' => $project->id,
            'status' => $project->status,
            'changed_by' => auth()->id() ?? null,
            'notes' => "Status changed from {$old} to {$project->status}"
        ]);

        return response()->json(['status'=>true,'message'=>'Status updated']);
    }

    /**
     * Attach a document to project (keeps your existing design)
     */
    public function attachDocument(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:10240',
            'type' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $path = $file->store("documents/{$project->id}", 'public');

        $doc = Document::create([
            'project_id' => $project->id,
            'type' => $request->type ?? 'other',
            'file_path' => $path,
            'uploaded_by' => auth()->id() ?? null,
            'meta' => null
        ]);

        ProjectHistory::create([
            'project_id' => $project->id,
            'status' => $project->status,
            'changed_by' => auth()->id() ?? null,
            'notes' => "Uploaded document: {$doc->type}"
        ]);

        return response()->json(['status'=>true,'message'=>'Uploaded','data'=>$doc]);
    }

    /**
     * JSON history list
     */
    public function history($id)
    {
        $history = ProjectHistory::where('project_id', $id)->orderBy('id','desc')->get();
        return response()->json($history);
    }

    /**
     * Delete (AJAX)
     */
    public function delete(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        // soft-delete if you prefer. here hard delete:
        $project->delete();

        return response()->json(['status'=>true,'message'=>'Deleted']);
    }
}
