<?php

namespace App\Http\Controllers;

use App\Models\SessionLog;
use Illuminate\Http\Request;

class SessionLogsController extends Controller
{
    public function index()
    {
        return SessionLog::all();
    }

    public function show($id)
    {
        return SessionLog::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'staffId' => 'required|integer',
            'location' => 'nullable|string|max:50',
            'message' => 'nullable|string',
            'lendmark' => 'nullable|string|max:255',
            'device' => 'nullable|string|max:100',
            'ip' => 'nullable|string|max:45',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        return SessionLog::create($data);
    }

    public function update(Request $request, $id)
    {
        $log = SessionLog::findOrFail($id);
        $log->update($request->all());
        return $log;
    }

    public function destroy($id)
    {
        SessionLog::destroy($id);
        return response()->json(['message' => 'Session log deleted']);
    }
}
