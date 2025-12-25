<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemCategory;

class ItemCategoryController extends Controller
{
    public function index()
    {
        return view('page.item_categories.index');
    }

    public function list(Request $request)
    {
        $q = ItemCategory::query();

        if (!empty($request->search)) {
            $q->where('name', 'like', '%' . $request->search . '%');
        }

        $data = $q->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        ItemCategory::create($request->only('name', 'description'));

        return response()->json(['success' => true, 'message' => 'Category created']);
    }

    public function edit($id)
    {
        $cat = ItemCategory::findOrFail($id);

        return response()->json(['success' => true, 'data' => $cat]);
    }

    public function update(Request $request, $id)
    {
        $cat = ItemCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        $cat->update($request->only('name', 'description'));

        return response()->json(['success' => true, 'message' => 'Category updated']);
    }

    public function delete($id)
    {
        ItemCategory::where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
}
