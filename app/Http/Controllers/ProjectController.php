<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Document;
use App\Models\ProjectHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /* ---------------------------------------------------------
     | LIST PAGE
     --------------------------------------------------------- */
    public function index()
    {
        return view('page.projects.list', [
            'statuses' => Project::STATUS_LABELS,
            'users' => User::get(),
        ]);
    }

    /* ---------------------------------------------------------
     | AJAX LIST
     --------------------------------------------------------- */
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);

        $q = Project::query()->with(['customer']);

        if ($search = trim($request->search)) {
            $q->where(function ($x) use ($search) {
                $x->where('project_code', 'like', "%$search%")
                  ->orWhere('customer_name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('assignee')) {
            $q->where('assignee', $request->assignee);
        }

        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->to);
        }

        $data = $q->orderBy('id', 'desc')->paginate($perPage);

        $data->getCollection()->transform(function ($p) {
            $p->status_label = $p->status_label;
            $p->assignee_name = $p->assigneeUser->name ?? null;
            return $p;
        });

        return response()->json($data);
    }

    /* ---------------------------------------------------------
     | CREATE
     --------------------------------------------------------- */
    public function create()
    {
        return view('page.projects.form', [
            'statuses' => Project::STATUS_LABELS,
            'users' => User::get(),
        ]);
    }

    /* ---------------------------------------------------------
     | STORE
     --------------------------------------------------------- */
    public function store(Request $request)
    {
        $rules = [
            'customer_id'    => 'nullable|exists:customers,id',
            'lead_id'        => 'nullable|exists:leads,id',
            'project_code'   => 'nullable|string|unique:projects,project_code',
            'customer_name'  => 'required|string',
            'mobile'         => 'nullable|string',
            'address'        => 'nullable|string',
            'kw'             => 'nullable|numeric',
            'module_brand'   => 'nullable|string',
            'inverter_brand' => 'nullable|string',
            'module_count'   => 'nullable|integer',
            'assignee'       => 'nullable|exists:users,id',
            'status'         => 'nullable|in:' . implode(',', array_keys(Project::STATUS_LABELS)),
            'project_value'  => 'nullable|numeric',
            'finalize_price' => 'nullable|numeric',
            'emi'            => 'nullable|numeric',
            'project_note'   => 'nullable|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return back()->withErrors($v)->withInput();

        $payload = $request->only(array_keys($rules));

        if (empty($payload['project_code'])) {
            $payload['project_code'] = "PRJ-" . now()->format("Ymd") . "-" . strtoupper(Str::random(4));
        }

        /** Auto-sync customer info */
        if ($request->customer_id) {
            $c = Customer::find($request->customer_id);
            if ($c) {
                $payload['customer_name'] = $c->name;
                $payload['mobile']        = $c->mobile;
                $payload['address']       = $c->address;
            }
        }

        $project = Project::create($payload);

        $this->logHistory($project->id, "created", "Project created");

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully');
    }

    /* ---------------------------------------------------------
     | EDIT
     --------------------------------------------------------- */
    public function edit($id)
    {
        $project = Project::with(['customer'])->findOrFail($id);

        return view('page.projects.form', [
            'project'  => $project,
            'users' => User::get(),
            'statuses' => Project::STATUS_LABELS,
        ]);
    }

    /* ---------------------------------------------------------
     | UPDATE
     --------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $rules = [
            'customer_id'    => 'nullable|exists:customers,id',
            'lead_id'        => 'nullable|exists:leads,id',
            'project_code'   => 'nullable|string|unique:projects,project_code,' . $project->id,
            'customer_name'  => 'required|string',
            'mobile'         => 'nullable|string',
            'address'        => 'nullable|string',
            'kw'             => 'nullable|numeric',
            'module_brand'   => 'nullable|string',
            'inverter_brand' => 'nullable|string',
            'module_count'   => 'nullable|integer',
            'assignee'       => 'nullable|exists:users,id',
            'status'         => 'nullable|in:' . implode(',', array_keys(Project::STATUS_LABELS)),
            'project_value'  => 'nullable|numeric',
            'finalize_price' => 'nullable|numeric',
            'emi'            => 'nullable|numeric',
            'project_note'   => 'nullable|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return back()->withErrors($v)->withInput();

        $payload = $request->only(array_keys($rules));

        /** Auto-fill customer */
        if ($request->customer_id) {
            $c = Customer::find($request->customer_id);
            if ($c) {
                $payload['customer_name'] = $c->name;
                $payload['mobile']        = $c->mobile;
                $payload['address']       = $c->address;
            }
        }

        $project->update($payload);

        $this->logHistory($project->id, "updated", "Project updated");

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    /* ---------------------------------------------------------
     | VIEW PAGE
     --------------------------------------------------------- */
    public function view($id)
    {
        $project = Project::with([
            'customer',
            'documents',
            'history',
            'assigneeUser',
        ])->findOrFail($id);

        return view('page.projects.view', compact('project'));
    }

    /* ---------------------------------------------------------
     | VIEW JSON (modal)
     --------------------------------------------------------- */
    public function viewJson($id)
    {
        $p = Project::with([
            'customer',
            'documents',
            'history',
            'assigneeUser'
        ])->findOrFail($id);

        return response()->json($p);
    }

    /* ---------------------------------------------------------
     | ASSIGN USER
     --------------------------------------------------------- */
    public function assign(Request $request, $id)
    {
        $request->validate(['assignee' => 'required|exists:users,id']);

        $p = Project::findOrFail($id);
        $old = $p->assignee;

        $p->assignee = $request->assignee;
        $p->save();

        $this->logHistory($id, "assigned", "Assigned to User #{$request->assignee}");

        return response()->json(['status' => true]);
    }

    /* ---------------------------------------------------------
     | STATUS CHANGE
     --------------------------------------------------------- */
    public function changeStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Project::STATUS_LABELS))
        ]);

        $p = Project::findOrFail($id);
        $old = $p->status;

        $p->status = $validated['status'];
        $p->save();

        $this->logHistory($id, "status_change", "Status changed from $old → {$p->status}");

        return response()->json(['status' => true]);
    }

    /* ---------------------------------------------------------
     | DOCUMENT UPLOAD (POLYMORPHIC)
     --------------------------------------------------------- */
    public function attachDocument(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:20000',
            'type' => 'nullable|string|max:255',
        ]);

        $path = $request->file('file')->store("documents/projects/{$id}", "public");

        $doc = Document::create([
            'entity_type' => 'project',
            'entity_id'   => $id,
            'file_path'   => $path,
            'file_name'   => basename($path),
            'mime_type'   => $request->file('file')->getMimeType(),
            'size'        => $request->file('file')->getSize(),
            'uploaded_by' => Auth::id(),
            'type'        => $request->type ?? 'other',
        ]);

        $this->logHistory($id, "document_uploaded", "Uploaded document {$doc->file_name}");

        return response()->json(['status' => true, 'doc' => $doc]);
    }

    /* ---------------------------------------------------------
     | HISTORY JSON
     --------------------------------------------------------- */
    public function history($id)
    {
        return ProjectHistory::where('project_id', $id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /* ---------------------------------------------------------
     | DELETE
     --------------------------------------------------------- */
    public function delete($id)
    {
        $p = Project::findOrFail($id);
        $p->delete();

        return response()->json(['status' => true]);
    }

    /* ---------------------------------------------------------
     | LOG HISTORY
     --------------------------------------------------------- */
    private function logHistory($projectId, $action, $notes)
    {
        ProjectHistory::create([
            'project_id' => $projectId,
            'status'     => Project::find($projectId)->status ?? 'new',
            'changed_by' => Auth::id(),
            'notes'      => $notes,
        ]);
    }
}
