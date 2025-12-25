<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Panel;
use App\Models\PanelMovement;
use Illuminate\Support\Facades\Auth;

class PanelMovementController extends Controller
{
    public function move(Request $request)
    {
        $request->validate([
            'panel_ids'      => 'required|array|min:1',
            'to_warehouse_id'=> 'required|integer',
            'note'           => 'nullable|string',
        ]);

        foreach ($request->panel_ids as $pid) {

            $panel = Panel::findOrFail($pid);
            $oldWarehouse = $panel->warehouse_id;

            $panel->warehouse_id = $request->to_warehouse_id;
            $panel->save();

            PanelMovement::create([
                'panel_id'          => $panel->id,
                'action'            => 'moved',
                'from_warehouse_id' => $oldWarehouse,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'performed_by'      => Auth::id(),
                'note'              => $request->note,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Panels moved successfully.'
        ]);
    }

    public function history($id)
    {
        $logs = PanelMovement::where('panel_id', $id)
            ->orderBy('happened_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $logs]);
    }
}
