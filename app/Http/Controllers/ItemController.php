<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::orderBy('name')->get();
        return view('page.items.index', compact('categories'));
    }

    public function list(Request $request)
    {
        $q = Item::with('category');

        if (!empty($request->search)) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if (!empty($request->category_id)) {
            $q->where('category_id', $request->category_id);
        }

        $items = $q->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required|string|max:150',
            'sku'         => 'nullable|string|max:150',
            'model'       => 'nullable|string|max:150',
            'watt'        => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'category_id', 'name', 'sku', 'model',
            'watt', 'description'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return response()->json(['success' => true, 'message' => 'Item created']);
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'category_id' => 'required',
            'name'        => 'required|string|max:150',
            'sku'         => 'nullable|string|max:150',
            'model'       => 'nullable|string|max:150',
            'watt'        => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'category_id', 'name', 'sku', 'model',
            'watt', 'description'
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return response()->json(['success' => true, 'message' => 'Item updated']);
    }

    public function delete($id)
    {
        $item = Item::findOrFail($id);

        if ($item->image) Storage::disk('public')->delete($item->image);

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item deleted']);
    }
}
