<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Panel;
use Illuminate\Support\Facades\Auth;
use App\Models\PanelMovement;

class PanelSaleController extends Controller
{
    public function sell(Request $request)
    {
        $request->validate([
            'panel_ids'   => 'required|array|min:1',
            'customer_id' => 'required|integer',
            'sold_at'     => 'nullable|date',
            'note'        => 'nullable|string',
        ]);

        $soldAt = $request->sold_at ?: now();

        foreach ($request->panel_ids as $pid) {
            $panel = Panel::findOrFail($pid);

            $prevWarehouse = $panel->warehouse_id;

            $panel->status = 'sold';
            $panel->customer_id = $request->customer_id;
            $panel->sold_at = $soldAt;
            $panel->warehouse_id = null;
            $panel->save();

            PanelMovement::create([
                'panel_id'          => $pid,
                'action'            => 'sold',
                'from_warehouse_id' => $prevWarehouse,
                'to_warehouse_id'   => null,
                'customer_id'       => $request->customer_id,
                'performed_by'      => Auth::id(),
                'note'              => $request->note,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function return(Request $request)
    {
        $request->validate([
            'panel_ids'      => 'required|array|min:1',
            'warehouse_id'   => 'required|integer',
            'note'           => 'nullable|string',
        ]);

        foreach ($request->panel_ids as $pid) {

            $panel = Panel::findOrFail($pid);
            $prevCustomer = $panel->customer_id;

            $panel->status = 'in_stock';
            $panel->customer_id = null;
            $panel->warehouse_id = $request->warehouse_id;
            $panel->sold_at = null;
            $panel->save();

            PanelMovement::create([
                'panel_id'          => $pid,
                'action'            => 'returned',
                'from_warehouse_id' => null,
                'to_warehouse_id'   => $request->warehouse_id,
                'customer_id'       => $prevCustomer,
                'performed_by'      => Auth::id(),
                'note'              => $request->note,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
