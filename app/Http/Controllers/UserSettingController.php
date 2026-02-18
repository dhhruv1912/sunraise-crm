<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings;

class UserSettingController extends Controller
{
    public function index()
    {
        return view('page.profile.settings');
    }

    public function ajaxSettings(Request $request)
    {
        $user = Auth::user();
        $group = $request->get('group');

        $prefix = "user_{$user->id}_{$group}_";

        $settings = Settings::where('name', 'like', $prefix.'%')
            ->orderBy('name')
            ->get();

        return view('page.profile.group', [
            'settings' => $settings,
            'title' => ucfirst($group),
            'description' => 'Personal preferences'
        ]);
    }

    public function save(Request $request)
    {
        $user = Auth::user();

        foreach ($request->except('_token') as $key => $value) {

            if (!str_starts_with($key, "user_{$user->id}_")) {
                continue; // 🔐 security guard
            }

            Settings::where('name', $key)->update([
                'value' => is_array($value)
                    ? json_encode($value)
                    : $value
            ]);
        }

        return response()->json(['ok'=>true]);
    }
}
