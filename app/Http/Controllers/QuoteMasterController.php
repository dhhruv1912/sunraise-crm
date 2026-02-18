<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuoteMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuoteMasterController extends Controller
{
    public function index()
    {
        return view('page.quote-master.index');
    }

    public function ajaxWidgets()
    {
        $total = DB::table('quote_master')->count();

        $avgKw = DB::table('quote_master')->avg('kw');
        $avgValue = DB::table('quote_master')->avg('value');

        return view('page.quote-master.widgets', [
            'total'     => $total,
            'avgKw'     => round($avgKw, 2),
            'avgValue'  => round($avgValue),
        ]);
    }

    public function kwPriceChart()
    {
        $data = DB::table('quote_master')
            ->whereNotNull('kw')
            ->whereNotNull('payable')
            ->orderBy('kw')
            ->get(['kw', 'payable']);

        return response()->json([
            'labels' => $data->pluck('kw'),
            'values' => $data->pluck('payable'),
        ]);
    }


    /* ================= LIST ================= */
    public function ajaxList(\Illuminate\Http\Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $q = DB::table('quote_master');

        if ($request->filled('kw_min')) {
            $q->where('kw', '>=', $request->kw_min);
        }

        if ($request->filled('kw_max')) {
            $q->where('kw', '<=', $request->kw_max);
        }

        if ($request->filled('price_min')) {
            $q->where('payable', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $q->where('payable', '<=', $request->price_max);
        }

        if ($request->filled('module')) {
            $q->where('module', 'like', '%' . $request->module . '%');
        }

        // return response()->json(
        //     $q->orderBy('kw')->get()
        // );
        $total = (clone $q)->count();
        $data = $q
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        $canEdit = Gate::allows('quote.master.edit');
        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
            ],
            "canEdit" => $canEdit
        ]);
    }


    public function create()
    {
        return view('page.quote-master.form', [
            'mode' => 'create',
            'row'  => null
        ]);
    }

    public function edit($id)
    {
        $row = DB::table('quote_master')->find($id);
        abort_if(!$row, 404);

        return view('page.quote-master.form', [
            'mode' => 'edit',
            'row'  => $row
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::table('quote_master')->insert($data);

        return response()->json([
            'message' => 'Quote package created successfully'
        ]);
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $id)
    {
        $data = $this->validateData($request);

        DB::table('quote_master')->where('id', $id)->update($data);

        return response()->json([
            'message' => 'Quote package updated successfully'
        ]);
    }

    public function destroy($id)
    {
        DB::table('quote_master')->where('id', $id)->delete();

        return response()->json([
            'message' => 'Quote package deleted successfully'
        ]);
    }

    public function getQuoteMaster($id)
    {
        $qm = QuoteMaster::findOrFail($id);

        return response()->json([
            'sku'            => $qm->sku,
            'module'         => $qm->module,
            'kw'             => $qm->kw,
            'module_count'   => $qm->module_count,
            'value'          => $qm->value,
            'taxes'          => $qm->taxes,
            'metering_cost'  => $qm->metering_cost,
            'mcb_ppa'        => $qm->mcb_ppa,
            'payable'        => $qm->payable,
            'subsidy'        => $qm->subsidy,
            'projected'      => $qm->projected,
        ]);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'sku'           => 'nullable|string|max:255',
            'module'        => 'required|string|max:255',
            'kw'            => 'required|numeric|min:0',
            'module_count'  => 'nullable|integer|min:0',
            'value'         => 'required|numeric|min:0',
            'taxes'         => 'nullable|numeric|min:0',
            'metering_cost' => 'nullable|numeric|min:0',
            'mcb_ppa'       => 'nullable|numeric|min:0',
            'subsidy'       => 'nullable|numeric|min:0',
            'payable'       => 'required|numeric|min:0',
        ]);
    }
}
