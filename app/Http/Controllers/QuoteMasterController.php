<?php

namespace App\Http\Controllers;

use App\Models\QuoteMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class QuoteMasterController extends Controller
{
    protected $fields = [
        'sku','module','kw','module_count','value','taxes',
        'metering_cost','mcb_ppa','payable','subsidy','projected','meta'
    ];

    // Main blade
    public function index()
    {
        return view('page.quote_master.list');
    }

    // AJAX list (JSON paginated)
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $search = $request->get('search', null);

        $query = QuoteMaster::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('kw', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($data);
    }

    // Create form
    public function create()
    {
        return view('page.quote_master.form');
    }

    // Store
    public function store(Request $request)
    {
        $rules = [
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
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'sku','module','kw','module_count','value','taxes',
            'metering_cost','mcb_ppa','payable','subsidy','projected'
        ]);

        // Meta handling
        $meta = $this->buildMetaFromRequest($request);
        $data['meta'] = $meta;

        // If SKU is empty auto-generate simple one
        if (empty($data['sku'])) {
            $brand = explode(' ', $data['module'])[0] ?? 'MOD';
            $data['sku'] = strtoupper(Str::slug($brand . '-' . $data['kw'] . '-MC-' . $data['module_count'], '-'));
        }

        QuoteMaster::create($data);

        return redirect()->route('quote_master.index')->with('success', 'Quote Master record created successfully.');
    }

    // Edit form
    public function edit($id)
    {
        $data = QuoteMaster::findOrFail($id);
        return view('page.quote_master.form', compact('data'));
    }

    // Update (form)
    public function update(Request $request, $id)
    {
        $rules = [
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
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $record = QuoteMaster::findOrFail($id);

        $data = $request->only([
            'sku','module','kw','module_count','value','taxes',
            'metering_cost','mcb_ppa','payable','subsidy','projected'
        ]);
        $data['meta'] = $this->buildMetaFromRequest($request);

        $record->update($data);

        return redirect()->route('quote_master.index')->with('success', 'Quote Master record updated successfully.');
    }

    // Inline update (AJAX) - accepts partial fields
    public function updateInline(Request $request, $id)
    {
        $record = QuoteMaster::findOrFail($id);

        // only accept allowed fields
        $update = $request->only([
            'sku','module','kw','module_count','value','taxes',
            'metering_cost','mcb_ppa','payable','subsidy','projected'
        ]);

        // if meta was sent as object/map
        if ($request->has('meta')) {
            $update['meta'] = $request->get('meta');
        }

        $record->update($update);

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully',
            'data' => $record->fresh(),
        ]);
    }

    // Delete (AJAX)
    public function delete(Request $request)
    {
        $id = $request->id;
        QuoteMaster::where('id', $id)->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }

    // Export CSV
    public function export()
    {
        $fileName = "quote_master_export_" . date("Y-m-d") . ".csv";
        $all = QuoteMaster::all()->toArray();

        if (empty($all)) {
            return redirect()->back()->with('error', 'No records to export.');
        }

        $columns = array_keys($all[0]);

        $callback = function () use ($all, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($all as $row) {
                // ensure meta is json string when exporting
                if (isset($row['meta']) && is_array($row['meta'])) {
                    $row['meta'] = json_encode($row['meta']);
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ];

        return response()->stream($callback, 200, $headers);
    }

    // Import CSV -> updateOrCreate by SKU (updates values)
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'Please upload a CSV file.');
        }

        $file = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($file);

        if (!$header) {
            return redirect()->back()->with('error', 'Invalid CSV.');
        }

        while ($row = fgetcsv($file)) {
            $rowData = array_combine($header, $row);

            // normalize keys to model fields
            $payload = [];
            foreach ($this->fields as $f) {
                if (isset($rowData[$f])) {
                    // meta keep as JSON decode if provided
                    if ($f === 'meta' && !empty($rowData[$f])) {
                        $payload[$f] = json_decode($rowData[$f], true) ?? null;
                    } else {
                        $payload[$f] = $rowData[$f];
                    }
                }
            }

            if (!empty($payload['sku'])) {
                QuoteMaster::updateOrCreate(['sku' => $payload['sku']], $payload);
            } else {
                // if no SKU, create new
                QuoteMaster::create($payload);
            }
        }

        fclose($file);

        return redirect()->back()->with('success', 'Import completed.');
    }

    // helper to build meta from arrays in request
    protected function buildMetaFromRequest(Request $request)
    {
        $meta = null;
        $keys = $request->input('meta_key', []);
        $vals = $request->input('meta_value', []);
        if (is_array($keys) && count($keys)) {
            $metaArr = [];
            foreach ($keys as $i => $k) {
                $key = trim($k);
                $val = $vals[$i] ?? null;
                if ($key !== '') {
                    $metaArr[$key] = $val;
                }
            }
            $meta = $metaArr;
        }
        return $meta;
    }
}
