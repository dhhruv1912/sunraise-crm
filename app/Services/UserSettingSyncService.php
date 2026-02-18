<?php
namespace App\Services;

use App\Models\Settings;

class UserSettingSyncService
{
    public static function sync($user)
    {
        $defaults = Settings::where('name', 'like', 'user_default_%')->get();

        foreach ($defaults as $def) {

            $userKey = str_replace(
                'user_default_',
                "user_{$user->id}_",
                $def->name
            );

            Settings::firstOrCreate(
                ['name' => $userKey],
                [
                    'label'   => $def->label,
                    'type'    => $def->type,
                    'default' => $def->default,
                    'value'   => $def->default,
                    'option'  => $def->option,
                    'attr'    => $def->attr
                ]
            );
        }
    }
}
