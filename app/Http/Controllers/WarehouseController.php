<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('page.warehouse.index');
    }

    public function list(Request $request)
    {
        $query = Warehouse::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('city', 'like', "%{$request->search}%");
        }

        return response()->json([
            'data' => $query->latest()->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'pincode' => 'nullable',
            'location' => 'nullable',
            'cords' => 'nullable',
        ]);

        Warehouse::create($data);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return Warehouse::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $location = Warehouse::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'code' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'pincode' => 'nullable',
            'location' => 'nullable',
            'cords' => 'nullable',
        ]);

        $location->update($data);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        Warehouse::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
