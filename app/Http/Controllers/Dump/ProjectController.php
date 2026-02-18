<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Storage;

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

        $q = Project::query()->with([
            'customer',
            'assigneeUser',
            'quoteMaster',
        ])
            ->select(
                'id',
                'quote_request_id',
                'quote_master_id',
                'lead_id',
                'customer_id',
                'project_code',
                'status',
                'priority',
                'updated_at',
                'created_at',
                'is_on_hold',
                'hold_reason',
                'assignee'
            );

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

        // $data->getCollection()->transform(function ($p) {
        //     $p->status_label = $p->status_label;
        //     $p->assignee_name = $p->assigneeUser->name ?? null;
        //     return $p;
        // });
        // dd($data);
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
            'customer_id' => 'nullable|exists:customers,id',
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
            'status' => 'nullable|in:'.implode(',', array_keys(Project::STATUS_LABELS)),
            'project_value' => 'nullable|numeric',
            'finalize_price' => 'nullable|numeric',
            'emi' => 'nullable|numeric',
            'project_note' => 'nullable|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $payload = $request->only(array_keys($rules));

        if (empty($payload['project_code'])) {
            $payload['project_code'] = 'PRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        }

        /** Auto-sync customer info */
        if ($request->customer_id) {
            $c = Customer::find($request->customer_id);
            if ($c) {
                $payload['customer_name'] = $c->name;
                $payload['mobile'] = $c->mobile;
                $payload['address'] = $c->address;
            }
        }

        $project = Project::create($payload);

        $this->logHistory($project->id, 'created', 'Project created');

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully');
    }

    /* ---------------------------------------------------------
     | EDIT
     --------------------------------------------------------- */
    public function edit($id)
    {
        $project = Project::with([
            'customer.documents',
            'lead',
            'boqs.items',
            'assigneeUser',
            'reporterUser',
            'invoices.items',
            'invoices.payments.receiver',
            'invoices.creator',
            'invoices.sender',
            'history.user',
            'projectDocuments.uploader',
        ])->findOrFail($id);

        return view('page.projects.form', [
            'project' => $project,
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
            'customer_id' => 'nullable|exists:customers,id',
            'lead_id' => 'nullable|exists:leads,id',
            'project_code' => 'nullable|string|unique:projects,project_code,'.$project->id,
            'customer_name' => 'nullable|string',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'kw' => 'nullable|numeric',
            'module_brand' => 'nullable|string',
            'inverter_brand' => 'nullable|string',
            'module_count' => 'nullable|integer',
            'assignee' => 'nullable|exists:users,id',
            'status' => 'nullable|in:'.implode(',', array_keys(Project::STATUS_LABELS)),
            'project_value' => 'nullable|numeric',
            'finalize_price' => 'nullable|numeric',
            'emi' => 'nullable|numeric',
            'project_note' => 'nullable|string',
            'survey_date' => 'nullable|'.Rule::date()->format('Y-m-d'),
            'installation_start_date' => 'nullable|'.Rule::date()->format('Y-m-d'),
            'installation_end_date' => 'nullable|'.Rule::date()->format('Y-m-d'),
            'inspection_date' => 'nullable|'.Rule::date()->format('Y-m-d'),
            'handover_date' => 'nullable|'.Rule::date()->format('Y-m-d'),
            'estimated_complete_date' => 'nullable|'.Rule::date()->format('Y-m-d'),
            'subsidy_status' => 'nullable|in:'.implode(',', array_keys(Project::SUBSIDY_STATUS_LABELS)),
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $v->errors()
            ], 422);
        }

        $payload = $request->only(array_keys($rules));
        /** Auto-fill customer */
        if ($request->customer_id) {
            $c = Customer::find($request->customer_id);
            if ($c) {
                $payload['customer_name'] = $c->name;
                $payload['mobile'] = $c->mobile;
                $payload['address'] = $c->address;
            }
        }
        // dd($payload);
        $project->update($payload);

        $this->logHistory($project->id, 'updated', 'Project updated');
        if ($request->ajax) {
            return response()->json(['status' => true]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function updateEmi(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if ($request->emi) {
            $project->emi = $request->emi;
        }
        if ($request->finalize_price) {
            $project->finalize_price = $request->finalize_price;
        }
        $project->save();

        $this->logHistory($project->id, 'updated', 'Project EMIs and Price updated');

        return response()->json(['status' => true]);
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
            'assigneeUser',
        ])->findOrFail($id);

        return response()->json($p);
    }

    /* ---------------------------------------------------------
     | ASSIGN USER
     --------------------------------------------------------- */
    public function reporter(Request $request, $id)
    {
        $request->validate(['reporter' => 'required|exists:users,id']);

        $p = Project::findOrFail($id);
        $old = $p->reporter;

        $p->reporter = $request->reporter;
        $p->save();

        $this->logHistory($id, 'assigned', "Assigned to User #{$request->reporter}");

        return response()->json(['status' => true]);
    }

    public function assign(Request $request, $id)
    {
        $request->validate(['assignee' => 'required|exists:users,id']);

        $p = Project::findOrFail($id);
        $old = $p->assignee;

        $p->assignee = $request->assignee;
        $p->save();

        $this->logHistory($id, 'assigned', "Assigned to User #{$request->assignee}");

        return response()->json(['status' => true]);
    }

    /* ---------------------------------------------------------
     | STATUS CHANGE
     --------------------------------------------------------- */
    public function changeStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Project::STATUS_LABELS)),
        ]);

        $p = Project::findOrFail($id);
        $old = $p->status;

        $p->status = $validated['status'];
        $p->save();

        $this->logHistory($id, 'status_change', "Status changed from $old → {$p->status}");

        return response()->json(['status' => true]);
    }

    public function changePrority(Request $request, $id)
    {
        $validated = $request->validate([
            'priority' => 'required|in:'.implode(',', ['low', 'high', 'medium']),
        ]);

        $p = Project::findOrFail($id);
        $old = $p->priority;

        $p->priority = $validated['priority'];
        $p->save();
        $p->history()->create([
            'status' => 'priority_change',
            'notes' => "priority changed from $old → {$p->priority}",
            'changed_by' => Auth::id(),
        ]);
        // $this->logHistory($id, "priority_change", "priority changed from $old → {$p->priority}");

        return response()->json(['status' => true]);
    }

    public function toggleHold(Request $request, $id)
    {
        $request->validate([
            'is_hold' => 'required|boolean',
            'reason' => 'nullable|string',
        ]);

        $p = Project::findOrFail($id);
        if ($request->is_hold) {
            $p->update([
                'is_on_hold' => true,
                'hold_reason' => $request->reason,
            ]);

            // Optional history
            $p->history()->create([
                'status' => 'hold',
                'notes' => $request->reason,
                'changed_by' => Auth::id(),
            ]);
        } else {

            $p->history()->create([
                'status' => 'unhold',
                'changed_by' => Auth::id(),
            ]);
            $p->update([
                'is_on_hold' => false,
                'hold_reason' => null,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
    public function editStep(Request $request, $id)
    {
        $validated = $request->validate([
            'steps'   => 'required|array|min:1',
            'steps.*' => 'required|string',
        ]);

        $project = Project::findOrFail($id);

        $oldCurrent = $project->current_step;
        $oldNext    = $project->next_step ?? [];

        $steps = array_values($validated['steps']); // normalize

        // ✅ Always set current + next from submitted steps
        $project->current_step = $steps[0];
        $project->next_step    = array_slice($steps, 1);

        $project->save();

        // ✅ Clean & accurate history
        $project->history()->create([
            'status'     => 'step_pipeline_change',
            'notes'      =>
                'Steps updated. '
                . 'Current: '
                . ($oldCurrent ?? 'None')
                . ' → '
                . $project->current_step
                . ' | Next: '
                . implode(', ', (array) $oldNext)
                . ' → '
                . implode(', ', $project->next_step),
            'changed_by' => Auth::id(),
        ]);

        return response()->json([
            'status'        => true,
            'current_step' => $project->current_step,
            'next_step'    => $project->next_step,
        ]);
    }



    public function completeCurrentStep(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $oldCurrent = $project->current_step;
        $steps = $project->next_step ?? [];

        // Ensure steps is an array
        if (! is_array($steps)) {
            $steps = [];
        }

        if (count($steps) > 0) {

            // Remove first step (current one)
            array_shift($steps);

            // Set next step as current (if exists)
            $project->current_step = $steps[0] ?? null;

            // Update remaining steps
            $project->next_step = $steps;
        } else {
            // No more steps left
            $project->current_step = null;
            $project->next_step = [];
        }

        $project->save();

        // History log
        $project->history()->create([
            'status' => 'current_step_change',
            'notes' => 'Current step changed from '
                .($oldCurrent ?? 'None')
                .' → '
                .($project->current_step ?? 'Completed'),
            'changed_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => true,
            'current_step' => $project->current_step,
            'next_step' => $project->next_step,
        ]);
    }

    /* ---------------------------------------------------------
     | DOCUMENT UPLOAD (POLYMORPHIC)
     --------------------------------------------------------- */
    public function attachDocument(Request $request, $id)
    {

        $request->validate([
            'file' => 'required|file|max:20000',
            'type' => 'nullable|string|max:255',
        ]);
        $project = Project::findOrFail($id);
        $path = $request->file('file')->store("documents/projects/{$id}", 'public');

        $doc = new Document();
        $doc->entity_type = Project::class;
        $doc->entity_id   = $id;
        $doc->project_id  = $id;
        $doc->type        = $request->type ?? 'other';
        $doc->file_name   = basename($path);
        $doc->file_path   = $path;
        $doc->mime_type   = $request->file('file')->getMimeType();
        $doc->size        = $request->file('file')->getSize();
        $doc->uploaded_by = Auth::id();
        $doc->save();
        
        if ($project->hasAttribute($request->type)) {
            $project->{$request->type} = $doc->id;
        }
        $project->save();

        $this->logHistory($id, 'document_uploaded', "Uploaded document {$doc->file_name}");

        return response()->json(['status' => true, 'doc' => $doc, 'url' => $path]);
    }
    public function attachPhotos(Request $request, $id)
    {
        $rules = [
            'files'   => 'required|array',
            'files.*' => 'required|file|max:51200',
            'type'        => 'nullable|string',
            'project_id'  => 'nullable|exists:projects,id',
            'description' => 'nullable|string',
            'tags'        => 'nullable|string', // comma-separated
            'entity_type' => 'nullable|string', // optional override
            'entity_id'   => 'nullable|integer'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $v->errors()
            ], 422);
        }
        foreach ($request->file('files') as $file) {
            if (! $file->isValid()) {
                return response()->json([
                    'status' => false,
                    'errors' => ['upload' => $file->getErrorMessage()]
                ], 422);
            }
        }
        $uploaded = [];
        $project = Project::findOrFail($id);

        $site_photos = is_array($project->site_photos)
            ? $project->site_photos
            : [];

        foreach ($request->file('files') as $file) {

            $path = $file->store("documents/projects/{$id}/photos", 'public');

            $doc = Document::create([
                'entity_type' => Project::class,
                'entity_id'   => $id,
                'project_id'  => $id,
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'mime_type'   => $file->getClientMimeType(),
                'size'        => $file->getSize(),
                'type'        => $request->type ?? 'site_photos',
                'description' => $request->description,
                'tags'        => $request->tags ? array_map('trim', explode(',', $request->tags)) : null,
                'uploaded_by' => Auth::id(),
            ]);

            $uploaded[] = array_merge($doc->toArray(), [
                'file_path' => asset('storage/'.$doc->file_path)
            ]);

            $site_photos[] = $doc->id;
        }
        // dd($site_photos);
        $project->site_photos = $site_photos;
        $project->save();

        $this->logHistory($id, 'site_photos_uploaded', "Uploaded Site Photoes");

        return response()->json(['status' => true, 'docs' => $uploaded]);
    }

    public function detachDocument(Request $request, $id)
    {
        $request->validate([
            'document_id' => 'nullable|exists:documents,id',
        ]);
        $doc = Document::find($request->document_id);
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $project = Project::find($id);
        if ($project->hasAttribute($doc->type)) {
            $project->{$doc->type} = null;
        }
        $doc->delete();
        $project->save();

        $this->logHistory($id, 'document_deleted', "Uploaded document {$doc->type}");

        return response()->json(['status' => true]);
    }

    public function detachPhotos(Request $request, $id)
    {
        $doc = Document::findOrFail($id);

        $project = Project::findOrFail($doc->project_id);
        $site_photos = $project->site_photos;
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }
        if (($key = array_search($doc->id, $site_photos)) !== false) {
            unset($site_photos[$key]);
            $site_photos = array_values($site_photos); // reindex
        }
        $project->site_photos = $site_photos;
        $project->save();
        $doc->delete();

        return response()->json(['status' => true, 'message' => 'Deleted']);
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
            'status' => Project::find($projectId)->status ?? 'new',
            'changed_by' => Auth::id(),
            'notes' => $notes,
        ]);
    }
}
