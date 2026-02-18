<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /* ============================================================
     | LIST PAGE
     ============================================================ */
    public function index()
    {
        // Your known document types
        $types = [
            'aadhar', 'pan', 'light_bill', 'invoice', 'layout', 'photos', 'other',
        ];

        return view('page.documents.list', compact('types'));
    }

    /* ============================================================
     | AJAX LIST
     ============================================================ */
    public function ajaxList(Request $request)
    {
        $perPage = (int) ($request->per_page ?? 20);

        $query = Document::query()
            ->with([
                'uploader',
                // 'entity'
            ]);

        /* SEARCH */
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        /* FILTERS */
        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }

        if ($request->filled('filter_user')) {
            $query->where('uploaded_by', $request->filter_user);
        }

        if ($request->filled('filter_project')) {
            $query->where('entity_type', 'project')
                ->where('entity_id', $request->filter_project);
        }

        if ($request->filled('filter_from')) {
            $query->whereDate('created_at', '>=', $request->filter_from);
        }

        if ($request->filled('filter_to')) {
            $query->whereDate('created_at', '<=', $request->filter_to);
        }

        $data = $query->orderBy('id', 'desc')->paginate($perPage);

        /* Enhance items for UI */
        $data->getCollection()->transform(function ($doc) {
            $doc->uploaded_by_name = $doc->uploader ? $doc->uploader->name : null;
            $doc->url = $doc->url; // accessor in model
            $doc->human_size = $doc->human_size;

            return $doc;
        });

        return response()->json($data);
    }

    /* ============================================================
     | UPLOAD ONE OR MULTIPLE FILES
     ============================================================ */
    public function upload(Request $request)
    {
        $rules = [
            'files' => 'required',
            'files.*' => 'file|max:51200', // 50MB max
            'type' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string', // comma-separated
            'entity_type' => 'nullable|string', // optional override
            'entity_id' => 'nullable|integer',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $v->errors(),
            ], 422);
        }

        $uploaded = [];

        /* Determine Entity Type (default: project) */
        if ($request->customer_id) {
            $entityType = Customer::class;
            $entityId = $request->customer_id;
            $project_id = $request->project_id;
        } elseif ($request->project_id) {
            $entityType = Project::class;
            $entityId = $request->project_id;
            $project_id = $request->project_id;
        } else {
            $project_id = $request->project_id ?? null;
        }

        foreach ($request->file('files') as $file) {
            $path = $file->store('documents', 'public');

            $doc = Document::create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,

                'project_id' => $project_id,

                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'type' => $request->type ?? 'other',
                'description' => $request->description,
                'tags' => $request->tags ? explode(',', $request->tags) : null,
                'uploaded_by' => Auth::id(),
            ]);
            $doc['file_path'] = asset('storage/'.$doc->file_path);
            $uploaded[] = $doc;
        }

        return response()->json([
            'status' => true,
            'message' => 'Uploaded successfully',
            'data' => $uploaded,
        ], 200);
    }

    public function uploadCustomer(Request $request)
    {
        $data = $request->validate([
            'entity_id' => 'required|integer',
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description' => 'nullable|string',
        ]);

        // remove old document of same type (replace)
        Document::where('entity_type', Customer::class)
            ->where('entity_id', $data['entity_id'])
            ->where('type', $data['type'])
            ->delete();

        $file = $request->file('file');

        $path = $file->store(
            'documents/customers',
            'public'
        );

        $doc = Document::create([
            'entity_type' => Customer::class,
            'entity_id' => @$data['entity_id'],
            'type' => @$data['type'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => @$data['description'],
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json([
            'id' => $doc->id,
            'url' => Storage::disk('public')->url($doc->file_path),
            'mime' => $doc->mime_type,
            'name' => $doc->file_name,
        ]);

    }

    public function uploadProject(Request $request)
    {
        $data = $request->validate([
            'entity_id' => 'required|integer',
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description' => 'nullable|string',
        ]);

        // remove old document of same type (replace)
        Document::where('entity_type', Project::class)
            ->where('entity_id', $data['entity_id'])
            ->where('type', $data['type'])
            ->delete();

        $file = $request->file('file');

        $path = $file->store(
            'documents/projects',
            'public'
        );

        $doc = Document::create([
            'entity_type' => Project::class,
            'entity_id' => $data['entity_id'],
            'type' => $data['type'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => $data['description'],
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json([
            'id' => $doc->id,
            'url' => Storage::disk('public')->url($doc->file_path),
            'mime' => $doc->mime_type,
            'name' => $doc->file_name,
        ]);

    }

    /* ============================================================
     | VIEW SINGLE DOCUMENT (JSON)
     ============================================================ */
    public function view(Request $request, $id)
    {
        $doc = Document::with(['uploader', 'project'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $doc->id,
                'file_name' => $doc->file_name,
                'type' => $doc->type,
                'description' => $doc->description,
                'size' => $doc->size,
                'human_size' => $doc->human_size,
                'mime_type' => $doc->mime_type,
                'uploaded_by' => $doc->uploaded_by_name ?? null,
                'url' => $doc->url,
                'tags' => $doc->tags,
                'project_id' => $doc->project_id,
                'entity_type' => $doc->entity_type,
                'entity_id' => $doc->entity_id,
            ],
        ]);
    }

    /* ============================================================
     | DOWNLOAD FILE STREAM
     ============================================================ */
    public function download($id)
    {
        $doc = Document::findOrFail($id);

        if (! Storage::disk('public')->exists($doc->file_path)) {
            abort(404, 'File not found');
        }

        return response()->streamDownload(function () use ($doc) {
            echo Storage::disk('public')->get($doc->file_path);
        }, $doc->file_name, [
            'Content-Type' => $doc->mime_type ?? 'application/octet-stream',
        ]);
    }

    /* ============================================================
     | DELETE DOCUMENT
     ============================================================ */
    public function delete(Request $request, $id)
    {
        $doc = Document::findOrFail($id);

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    /* ============================================================
     | ATTACH TO PROJECT
     ============================================================ */
    public function attachToProject(Request $request, $id)
    {
        $request->validate(['project_id' => 'required|exists:projects,id']);

        $doc = Document::findOrFail($id);

        $doc->entity_type = 'project';
        $doc->entity_id = $request->project_id;
        $doc->project_id = $request->project_id; // backwards compatibility
        $doc->save();

        return response()->json(['status' => true, 'message' => 'Attached']);
    }

    /* ============================================================
     | DETACH FROM PROJECT
     ============================================================ */
    public function detachFromProject(Request $request, $id)
    {
        $doc = Document::findOrFail($id);

        $doc->entity_type = null;
        $doc->entity_id = null;
        $doc->project_id = null;
        $doc->save();

        return response()->json(['status' => true, 'message' => 'Detached']);
    }

    /* ============================================================
     | EXPORT CSV
     ============================================================ */
    public function export(Request $request)
    {
        $query = Document::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('project_id')) {
            $query->where('entity_type', 'project')
                ->where('entity_id', $request->project_id);
        }

        $rows = $query->orderBy('id', 'desc')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'file_name' => $d->file_name,
                'type' => $d->type,
                'entity_type' => $d->entity_type,
                'entity_id' => $d->entity_id,
                'uploaded_by' => $d->uploaded_by,
                'size' => $d->size,
                'url' => $d->url,
                'created_at' => $d->created_at,
            ];
        })->toArray();

        $fileName = 'documents_export_'.date('Y-m-d').'.csv';

        $response = new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, array_keys($rows[0] ?? [
                'id', 'file_name', 'type', 'entity_type', 'entity_id', 'uploaded_by', 'size', 'url', 'created_at',
            ]));
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename={$fileName}");

        return $response;
    }

    /* ============================================================
     | SEARCH PROJECTS (ATTACHMENT UI)
     ============================================================ */
    public function searchProjects(Request $request)
    {
        $q = trim($request->q);

        if (! $q) {
            return response()->json([]);
        }
        $projects = Project::with(['customer'])
            ->where('project_code', 'like', "%{$q}%")
            ->orWhereHas('customer', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'label' => $p->project_code,
                    'sub' => $p->customer?->name,
                    'extra' => $p->customer?->mobile,
                    // 'lead'  => $p->lead,
                ];
            });

        return response()->json($projects);
    }
}
