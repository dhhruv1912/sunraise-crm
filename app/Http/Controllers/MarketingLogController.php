<?php

namespace App\Http\Controllers;

use App\Models\MarketingLog;
use Illuminate\Http\Request;

class MarketingLogController extends Controller
{
    public function index()
    {
        return MarketingLog::all();
    }

    public function show($id)
    {
        return MarketingLog::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'marketing_data_id' => 'nullable|integer',
            'message' => 'nullable|string',
            'assignee' => 'nullable|integer',
        ]);

        return MarketingLog::create($data);
    }

    public function update(Request $request, $id)
    {
        $log = MarketingLog::findOrFail($id);
        $log->update($request->all());
        return $log;
    }

    public function destroy($id)
    {
        MarketingLog::destroy($id);
        return response()->json(['message' => 'Marketing log deleted']);
    }
}
