<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Menu
{
    public static function is($segment1, $segment2 = null)
    {
        // Check if request is available (not during config loading)
        if (!app()->bound('request')) {
            return false;
        }

        try {
            $request = app('request');
            if (!$request instanceof Request) {
                return false;
            }
            
            $s1 = $request->segment(1);
            $s2 = $request->segment(2) ?? '';
            if (is_array($segment2)) {
                return $s1 === $segment1 && in_array($s2, $segment2);
            }
            $segment2 = $segment2 ?? '';

            return $s1 === $segment1 && $s2 === $segment2;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function open($segment1)
    {
        // Check if request is available (not during config loading)
        if (!app()->bound('request')) {
            return false;
        }

        try {
            $request = app('request');
            if (!$request instanceof Request) {
                return false;
            }
            
            return $request->segment(1) === $segment1;
        } catch (\Exception $e) {
            return false;
        }
    }
}
