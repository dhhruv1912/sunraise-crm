<?php

namespace App\Http\Controllers;

use App\Models\QuoteMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuoteMasterController extends Controller
{
    /**
     * Allowed fields for mass update/import.
     */
    protected $fields = [
        'sku','module','kw','module_count',
        'value','taxes','metering_cost',
        'mcb_ppa','payable','subsidy','projected','meta'
    ];

    /* -------------------------------------------------------
     | LIST PAGE (Blade)
     ------------------------------------------------------- */
    public function index()
    {
        return view('page.quote_master.list');
    }

    /* -------------------------------------------------------
     | AJAX LIST (pagination + search)
     ------------------------------------------------------- */
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->per_page ?? 20;

        $query = QuoteMaster::query();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%$search%")
                  ->orWhere('module', 'like', "%$search%")
                  ->orWhere('kw', 'like', "%$search%");
            });
        }

        return response()->json(
            $query->orderBy('id', 'desc')->paginate($perPage)
        );
    }

    /* -------------------------------------------------------
     | CREATE FORM
     ------------------------------------------------------- */
    public function create()
    {
        return view('page.quote_master.form');
    }

    /* -------------------------------------------------------
     | STORE
     ------------------------------------------------------- */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        // Auto SKU (if empty)
        if (empty($data['sku'])) {
            $brand = explode(' ', $data['module'])[0] ?? 'MOD';
            $data['sku'] = strtoupper(
                Str::slug("{$brand}-{$data['kw']}-MC-{$data['module_count']}", '-')
            );
        }

        $data['meta'] = $this->buildMetaFromRequest($request);

        QuoteMaster::create($data);

        return redirect()->route('quote_master.index')
            ->with('success', 'Record created successfully.');
    }

    /* -------------------------------------------------------
     | EDIT FORM
     ------------------------------------------------------- */
    public function edit($id)
    {
        $data = QuoteMaster::findOrFail($id);
        return view('page.quote_master.form', compact('data'));
    }

    /* -------------------------------------------------------
     | UPDATE
     ------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $record = QuoteMaster::findOrFail($id);

        $data = $this->validatePayload($request, $id);
        $data['meta'] = $this->buildMetaFromRequest($request);

        $record->update($data);

        return redirect()->route('quote_master.index')
            ->with('success', 'Record updated successfully.');
    }

    /* -------------------------------------------------------
     | INLINE UPDATE (AJAX)
     ------------------------------------------------------- */
    public function updateInline(Request $request, $id)
    {
        $record = QuoteMaster::findOrFail($id);

        $update = $request->only($this->fields);

        if ($request->has('meta')) {
            $update['meta'] = $request->meta;
        }

        $record->update($update);

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully',
            'data' => $record->fresh(),
        ]);
    }

    /* -------------------------------------------------------
     | DELETE (AJAX)
     ------------------------------------------------------- */
    public function delete(Request $request)
    {
        QuoteMaster::where('id', $request->id)->delete();
        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    /* -------------------------------------------------------
     | EXPORT CSV
     ------------------------------------------------------- */
    public function export()
    {
        $fileName = 'quote_master_export_' . date('Ymd_His') . '.csv';
        $rows = QuoteMaster::orderBy('id', 'desc')->get();

        if ($rows->isEmpty()) {
            return back()->with('error', 'No records to export.');
        }

        $columns = array_keys($rows->first()->toArray());

        $response = new StreamedResponse(function () use ($rows, $columns) {
            $h = fopen('php://output', 'w');
            fputcsv($h, $columns);

            foreach ($rows as $r) {
                $arr = $r->toArray();
                if (is_array($arr['meta'])) {
                    $arr['meta'] = json_encode($arr['meta']);
                }
                fputcsv($h, $arr);
            }

            fclose($h);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=$fileName");

        return $response;
    }

    /* -------------------------------------------------------
     | IMPORT CSV
     ------------------------------------------------------- */
    public function import(Request $request)
    {
        if (! $request->hasFile('file')) {
            return back()->with('error', 'Upload a CSV file.');
        }

        $fp = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($fp);

        while ($row = fgetcsv($fp)) {
            $rowData = array_combine($header, $row);

            $payload = [];
            foreach ($this->fields as $f) {
                if (isset($rowData[$f])) {
                    $payload[$f] = ($f === 'meta')
                        ? json_decode($rowData[$f], true)
                        : $rowData[$f];
                }
            }

            if (! empty($payload['sku'])) {
                QuoteMaster::updateOrCreate(['sku' => $payload['sku']], $payload);
            } else {
                QuoteMaster::create($payload);
            }
        }

        fclose($fp);

        return back()->with('success', 'Import completed.');
    }

    /* -------------------------------------------------------
     | VALIDATION HELPER
     ------------------------------------------------------- */
    protected function validatePayload(Request $request, $id = null)
    {
        return $request->validate([
            'sku' => 'nullable|string',
            'module' => 'required|string',
            'kw' => 'required|numeric',
            'module_count' => 'required|integer',
            'value' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'metering_cost' => 'nullable|numeric',
            'mcb_ppa' => 'nullable|numeric',
            'payable' => 'nullable|numeric',
            'subsidy' => 'nullable|numeric',
            'projected' => 'nullable|numeric',
        ]);
    }

    /* -------------------------------------------------------
     | META BUILDER
     ------------------------------------------------------- */
    protected function buildMetaFromRequest(Request $request)
    {
        $keys = $request->input('meta_key', []);
        $vals = $request->input('meta_value', []);

        $meta = [];
        foreach ($keys as $i => $k) {
            $k = trim($k);
            $v = $vals[$i] ?? null;
            if ($k !== '') {
                $meta[$k] = $v;
            }
        }

        return $meta ?: null;
    }
}
