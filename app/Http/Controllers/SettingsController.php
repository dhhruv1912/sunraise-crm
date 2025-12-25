<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Load settings page by module.
     * URL: /settings/{module}
     */
    public function load(Request $request, $module, $userId = null)
    {
        // Load all settings that start with "<module>_"
        $settings = Settings::where('name', 'like', "{$module}_%");
        if ($userId != null) {
            $settings = $settings->where("user_id",$userId);
        }
        $settings = $settings->orderBy('id')->get();

        return view('page.settings', compact('settings', 'module'));
    }

    /**
     * Show one employee (Edit Modal Load).
     */
    public function show($id)
    {
        $user = Settings::find($id);

        if (! $user) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Get a single setting by name.
     * URL: /settings/get/{name}
     */
    public function get(Request $request, $name)
    {
        $setting = Settings::where('name', $name)->first();

        if (! $setting) {
            return response()->json(['status' => 0, 'message' => 'Setting not found'], 404);
        }

        return response()->json(['status' => 1, 'data' => $setting]);
    }

    /**
     * Create or update setting metadata (label, type, options, attr).
     * URL: POST /settings/save
     */
    public function save(Request $request)
    {
        $isNew = empty($request->id);

        $rules = [
            'setting_name' => 'required',
            'setting_type' => 'required',
            'setting_label' => 'required',
        ];

        if ($isNew) {
            $rules['setting_name'] .= '|unique:setting,name';
        } else {
            $rules['setting_name'] .= '|unique:setting,name,'.$request->id;
        }

        // AJAX-safe validation (no redirect)
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create or update
        $setting = $isNew ? new Settings : Settings::find($request->id);

        if (! $setting) {
            return response()->json(['status' => false, 'message' => 'Setting not found'], 404);
        }

        // New – generate module_name
        if ($isNew) {
            $setting->name = strtolower($request->setting_module.'_'.$request->setting_name);
        }

        $setting->label = $request->setting_label;
        $setting->type = $request->setting_type;
        $setting->attr = $request->setting_attr ?? '';
        $setting->option = $request->setting_option ?? '';

        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'Setting Saved!',
        ], 200);
    }

    /**
     * Update the value of a setting (checkbox/json/file/simple).
     * URL: POST /settings/save-value/{name}
     */
    public function saveValue(Request $request, $name)
    {
        $setting = Settings::where('name', $name)->first();

        if (! $setting) {
            return response()->json(['status' => false, 'message' => 'Setting not found'], 404);
        }

        $type = (int) $setting->type;

        // JSON TYPE (8)
        if ($type === 8) {
            $json = [];
            if ($request->value) {
                // Expect {"key": "value"}
                $json = json_decode($request->value, true);
                if (! is_array($json)) {
                    $json = [];
                }
            }
            $setting->value = json_encode($json);
        }

        // FILE TYPE (6)
        elseif ($type === 6 && $request->hasFile('file')) {
            $path = $request->file('file')->store("settings/{$name}", 'public');
            $setting->value = $path;
        }

        // CHECKBOX TYPE (4)
        elseif ($type === 4) {
            // value contains JSON array ["x","y"]
            // $arr = json_decode($request->value, true);
            $arr = $request->value;
            if (! is_array($arr)) {
                $arr = [];
            }
            $setting->value = json_encode($arr);
        }

        // NORMAL INPUT
        else {
            $setting->value = $request->value ?? '';
        }

        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'Value updated',
            'data' => ['value' => $setting->value],
        ]);
    }

    /**
     * Delete setting
     */
    public function delete($id)
    {
        $setting = Settings::find($id);
        if (! $setting) {
            return response()->json(['status' => false, 'message' => 'Setting not found'], 404);
        }

        $setting->delete();

        return response()->json(['status' => true, 'message' => 'Setting deleted']);
    }

    /**
     * Reorder settings
     */
    public function reorder(Request $request)
    {
        foreach ($request->order as $id => $sort) {
            Settings::where('id', $id)->update(['sort' => $sort]);
        }

        return response()->json(['status' => true]);
    }

    /**
     * Export all settings (as JSON)
     */
    public function export()
    {
        return response()->json(Settings::all());
    }

    /**
     * Import settings from JSON
     */
    public function import(Request $request)
    {
        if (! $request->file('file')) {
            return response()->json(['status' => false, 'message' => 'File missing'], 422);
        }

        $json = json_decode(file_get_contents($request->file('file')), true);

        if (! is_array($json)) {
            return response()->json(['status' => false, 'message' => 'Invalid format'], 422);
        }

        foreach ($json as $item) {
            Settings::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }

        return response()->json(['status' => true, 'message' => 'Imported successfully']);
    }
}
