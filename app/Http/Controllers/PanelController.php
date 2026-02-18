<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Panel;
use App\Models\Item;
use App\Models\Warehouse;

class PanelController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('page.panels.index', compact('items', 'warehouses'));
    }

    public function list(Request $request)
    {
        $query = Panel::query();

        if ($request->search) {
            $q = $request->search;
            $query->where('serial_number', 'like', "%$q%")
                ->orWhere('batch_no_copy', 'like', "%$q%");
        }

        if ($request->item_id) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('id', 'desc')->paginate(25),
        ]);
    }

    public function show($id)
    {
        $panel = Panel::with(['item', 'batch', 'movements', 'attachments'])->findOrFail($id);

        return view('page.panels.modal.show-panel', compact('panel'));
    }

    public function delete($id)
    {
        $panel = Panel::findOrFail($id);
        $panel->delete();

        return response()->json(['success' => true]);
    }
}
