<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        return view('page.documents.index');
    }

    public function ajaxWidgets()
    {
        $today = Carbon::today();
        $totalSize = Document::sum('size');

        return view('page.documents.widgets', [
            'total' => Document::count(),
            'customers' => Document::where('entity_type', Customer::class)->count(),
            'projects' => Document::where('entity_type', Project::class)->count(),
            'recent' => Document::whereDate('created_at', '>=', $today->subDays(7))->count(),
            'size' => $this->formatBytes($totalSize),
        ]);
    }

    public function ajaxAdvancedWidgets()
    {
        /* ---------- MISSING KYC ---------- */

        $customersWithDocs = DB::table('documents')
            ->select('entity_id')
            ->where('entity_type', Customer::class)
            ->whereIn('type', [
                'aadhar_card',
                'pan_card',
                'passport_size_photo',
            ])
            ->groupBy('entity_id')
            ->pluck('entity_id')
            ->toArray();

        $missingKyc = DB::table('customers')
            ->whereNotIn('id', $customersWithDocs)
            ->count();

        /* ---------- RECENT UPLOADS ---------- */

        $recent = \App\Models\Document::with('uploader')
            ->latest()
            ->take(5)
            ->get();

        /* ---------- LARGE FILES ---------- */

        $largeFiles = \App\Models\Document::where('size', '>', 5 * 1024 * 1024)
            ->count();

        return view('page.documents.widgets_advanced', [
            'missingKyc' => $missingKyc,
            'recent' => $recent,
            'largeFiles' => $largeFiles,
        ]);
    }

    /* =========================
     * AJAX LIST
     * ========================= */
    public function ajaxList(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 12);

        $q = Document::query()
            ->with(['uploader'])
            ->latest();

        /* ---------- FILTERS ---------- */

        if ($request->entity_type) {
            $q->where('entity_type', $request->entity_type);
        }

        if ($request->type) {
            $q->where('type', $request->type);
        }

        if ($request->search) {
            $s = $request->search;
            $q->where('file_name', 'like', "%{$s}%");
        }

        /* ---------- PAGINATION ---------- */

        $total = (clone $q)->count();

        $rows = $q
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'file_name' => $d->file_name,
                    'type' => $d->type,
                    'entity' => Str::replaceFirst('App\Models\\', '', $d->entity_type),
                    'entity_id' => $d->entity_id,
                    'mime' => $d->mime_type,
                    'url' => Storage::disk('public')->url($d->file_path),
                    'uploaded_by' => optional($d->uploader)->fname,
                    'created_at' => optional($d->created_at)->format('d M Y'),
                ];
            });

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
            'canEdit' => Gate::allows('project.documents.edit')
        ]);
    }

    /* =========================
     * VIEW SINGLE DOCUMENT
     * ========================= */
    public function view(Document $document)
    {
        return view('page.documents.view', [
            'document' => $document,
        ]);
    }

    /* =========================
     * DELETE DOCUMENT (ADMIN)
     * ========================= */
    public function delete(Document $document)
    {
        // optional: permission check here
        // abort_unless(auth()->user()->role === 1, 403);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document deleted',
        ]);
    }

    public function uploadCustomer(Request $request)
    {
        // dd($request)
        $rules = [
            'entity_id' => 'required|integer',
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description' => 'nullable|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $v->errors(),
            ], 422);
        }

        // remove old document of same type (replace)
        Document::where('entity_type', Customer::class)
            ->where('entity_id', $request->entity_id)
            ->where('type', $request->type)
            ->delete();

        $file = $request->file('file');

        $path = $file->store(
            'documents/customers',
            'public'
        );

        $doc = Document::create([
            'entity_type' => Customer::class,
            'entity_id' => @$request->entity_id,
            'type' => @$request->type,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => @$request->description,
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json([
            'id' => $doc->id,
            'url' => Storage::disk('public')->url($doc->file_path),
            'mime' => $doc->mime_type,
            'name' => $doc->file_name,
        ]);

    }

    public function upload(Request $request)
    {
        // dd($request)
        $rules = [
            'entity_id' => 'nullable|integer',
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description' => 'nullable|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $v->errors(),
            ], 422);
        }

        // remove old document of same type (replace)
        Document::where('entity_type', Customer::class)
            ->where('entity_id', $request->entity_id)
            ->where('type', $request->type)
            ->delete();

        $file = $request->file('file');

        $path = $file->store(
            'documents/customers',
            'public'
        );

        $doc = Document::create([
            'entity_type' => null,
            'entity_id' => @$request->entity_id,
            'type' => @$request->type,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => @$request->description,
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
        if ($request->multiple == 'true') {
            $rules = [
                'file' => 'required',
                'file.*' => 'file|max:51200', // 50MB max
                'type' => 'nullable|string',
                'description' => 'nullable|string',
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
            $entityType = Project::class;
            $entityId = $request->entity_id;

            foreach ($request->file('file') as $file) {
                $path = $file->store('documents/projects', 'public');

                $doc = Document::create([
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
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
        } else {
            $rules = [
                'entity_id' => 'required|integer',
                'type' => 'required|string',
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'description' => 'nullable|string',
            ];

            $v = Validator::make($request->all(), $rules);
            if ($v->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $v->errors(),
                ], 422);
            }

            // remove old document of same type (replace)
            Document::where('entity_type', Project::class)
                ->where('entity_id', $request->entity_id)
                ->where('type', $request->type)
                ->delete();

            $file = $request->file('file');

            $path = $file->store(
                'documents/projects',
                'public'
            );

            $doc = Document::create([
                'entity_type' => Project::class,
                'entity_id' => $request->entity_id,
                'type' => $request->type,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'description' => $request->description,
                'uploaded_by' => Auth::id(),
            ]);

            ProjectHistory::create([
                'project_id' => $request->entity_id,
                'action' => 'document_uploaded',
                'message' => "Document '{$request->type}' uploaded",
                'changed_by' => Auth::id(),
            ]);

            return response()->json([
                'id' => $doc->id,
                'url' => Storage::disk('public')->url($doc->file_path),
                'mime' => $doc->mime_type,
                'name' => $doc->file_name,
            ]);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
