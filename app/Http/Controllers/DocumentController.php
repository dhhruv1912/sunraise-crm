<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    // Show UI page
    public function index()
    {
        // types list can be extended or loaded from DB/settings
        $types = [
            'aadhar','pan','light_bill','invoice','layout','photos','other'
        ];

        return view('page.documents.list', compact('types'));
    }

    // AJAX list for datatable / custom UI
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $query = Document::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('file_name', 'like', "%{$q}%")
                   ->orWhere('type', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }

        if ($request->filled('filter_user')) {
            $query->where('uploaded_by', $request->filter_user);
        }

        if ($request->filled('filter_project')) {
            $query->where('project_id', $request->filter_project);
        }

        if ($request->filled('filter_from')) {
            $query->whereDate('created_at', '>=', $request->filter_from);
        }
        if ($request->filled('filter_to')) {
            $query->whereDate('created_at', '<=', $request->filter_to);
        }

        $data = $query->orderBy('id', 'desc')->paginate($perPage);

        // add uploader name & url
        $data->getCollection()->transform(function ($item) {
            $item->uploaded_by_name = $item->uploader ? $item->uploader->name : null;
            $item->url = $item->url;
            return $item;
        });

        return response()->json($data);
    }

    // Upload endpoint (single or multiple)
    public function upload(Request $request)
    {
        $rules = [
            'files' => 'required',
            'files.*' => 'file|max:51200', // max 50MB each
            'type' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'errors'=>$validator->errors()], 422);
        }

        $uploaded = [];
        foreach ($request->file('files') as $file) {
            $path = $file->store('documents', 'public');
            $doc = Document::create([
                'entity_type' => $request->project_id ? 'project' : "",
                'entity_id'   => $request->project_id ?? null,
                'project_id' => $request->project_id ?? null,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'type' => $request->type ?? null,
                'description' => $request->description ?? null,
                'uploaded_by' => auth()->id() ?? null,
                'tags' => $request->tags ? explode(',', $request->tags) : null,
            ]);
            $uploaded[] = $doc;
        }

        return response()->json(['status'=>true,'message'=>'Uploaded','data'=>$uploaded], 201);
    }

    // View single (JSON for modal)
    public function view(Request $request, $id)
    {
        $doc = Document::with('uploader','project')->findOrFail($id);
        $doc->url = asset('storage/' . $doc->file_path);
        // $doc->url2 = Storage::disk('public')->key($doc->file_path);
        $doc->human_size = $doc->human_size;
        return response()->json(['status'=>true,'data'=>$doc]);
    }

    // Download file (stream)
    public function download($id)
    {
        $doc = Document::findOrFail($id);
        $disk = Storage::disk('public');

        if (!$disk->exists($doc->file_path)) {
            abort(404);
        }

        return response()->streamDownload(function() use ($disk, $doc) {
            echo $disk->get($doc->file_path);
        }, $doc->file_name, [
            'Content-Type' => $doc->mime_type ?: 'application/octet-stream'
        ]);
    }

    // Delete (ajax)
    public function delete(Request $request, $id)
    {
        $doc = Document::findOrFail($id);

        // remove file from disk
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return response()->json(['status'=>true,'message'=>'Deleted']);
    }

    // Attach to existing project (ajax)
    public function attachToProject(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);

        $doc = Document::findOrFail($id);
        $doc->project_id = $request->project_id;
        $doc->save();

        return response()->json(['status'=>true,'message'=>'Attached']);
    }

    // Detach from project
    public function detachFromProject(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $doc->project_id = null;
        $doc->save();

        return response()->json(['status'=>true,'message'=>'Detached']);
    }

    // Export CSV for current filter (optional)
    public function export(Request $request)
    {
        $query = Document::query();
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('project_id')) $query->where('project_id', $request->project_id);

        $rows = $query->orderBy('id','desc')->get()->map(function($d) {
            return [
                'id' => $d->id,
                'file_name' => $d->file_name,
                'type' => $d->type,
                'project_id' => $d->project_id,
                'uploaded_by' => $d->uploaded_by,
                'size' => $d->size,
                'url' => $d->url,
                'created_at' => $d->created_at,
            ];
        })->toArray();

        $fileName = 'documents_export_'.date('Y-m-d').'.csv';

        $response = new StreamedResponse(function() use ($rows) {
            $handle = fopen('php://output','w');
            fputcsv($handle, array_keys($rows[0] ?? ['id','file_name','type','project_id','uploaded_by','size','url','created_at']));
            foreach ($rows as $r) fputcsv($handle, $r);
            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv');
        $response->headers->set('Content-Disposition','attachment; filename='.$fileName);
        return $response;
    }

    public function searchProjects(Request $request)
    {
        $q = trim($request->get('q'));

        if (!$q) {
            return response()->json([]);
        }

        $projects = Project::where('project_code', 'LIKE', "%$q%")
            ->orWhere('customer_name', 'LIKE', "%$q%")
            ->orWhere('mobile', 'LIKE', "%$q%")
            ->with('lead')
            ->limit(15)
            ->get()
            ->map(function($p){
                return [
                    'id' => $p->id,
                    'label' => $p->project_code,
                    'sub' => $p->customer_name,
                    'extra' => $p->mobile,
                    'lead' => $p->lead,
                ];
            });

        return response()->json($projects);
    }
}
