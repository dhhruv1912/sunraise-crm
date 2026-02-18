<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Models\Project;
use App\Models\QuoteMaster;
use Illuminate\Support\Facades\Cache;

class LeadAssignService
{
    /**
     * Assign a lead.
     *
     * @param array $options e.g. ['preferred' => 3, 'kw' => 2.7]
     * @return int|null user id
     */
    public static function assign(array $options = [])
    {
        $preferred = $options['preferred'] ?? null;
        $kw = $options['kw'] ?? null;
        $role = $options['role'] ?? 'marketing'; // default role to pick

        // 1) preferred user if valid and has marketing role
        if ($preferred) {
            $u = User::find($preferred);
            if ($u && self::userHasRole($u, $role)) return $u->id;
        }

        // 2) build candidate list: users with 'marketing' role (adapt to your RBAC)
        $candidates = User::whereHas('roles', function($q) use ($role){
            $q->where('name', $role);
        })->get();

        // fallback: if you don't use roles table, use a column e.g. role_key
        if ($candidates->isEmpty()) {
            $candidates = User::where('role_key', 'marketing')->get();
        }

        if ($candidates->isEmpty()) return null;

        // 3) KW/skill matching: prefer users who've handled similar packages
        if ($kw) {
            // find the quote_master package nearest to kw
            try {
                $package = QuoteMaster::orderByRaw("ABS(kw - ?) ASC", [$kw])->first();
            } catch (\Throwable $e) {
                $package = null;
            }

            if ($package) {
                // find users who had projects with similar kw or used same module brand
                $userCounts = [];
                foreach ($candidates as $u) {
                    $count = Project::where('assignee', $u->id)
                        ->where(function($q) use ($package, $kw){
                            $q->where('kw', '>=', ($package->kw * 0.9))
                              ->where('kw', '<=', ($package->kw * 1.1));
                        })->count();
                    $userCounts[$u->id] = $count;
                }
                // pick the user with highest count (experience). If all 0 -> fallback to least open leads
                arsort($userCounts);
                $best = array_key_first($userCounts);
                if ($userCounts[$best] > 0) {
                    Cache::put('lead_last_assigned_user', $best, now()->addHours(6));
                    return $best;
                }
            }
        }

        // 4) workload balancing: least open leads
        $counts = [];
        foreach ($candidates as $u) {
            $counts[$u->id] = Lead::where('assigned_to', $u->id)
                                ->whereNotIn('status', ['converted','dropped'])
                                ->count();
        }
        asort($counts);
        $assigned = (int) array_key_first($counts);
        if ($assigned) {
            Cache::put('lead_last_assigned_user', $assigned, now()->addHours(6));
            return $assigned;
        }

        // 5) round-robin fallback
        $ids = $candidates->pluck('id')->toArray();
        $last = Cache::get('lead_last_assigned_user');
        $idx = 0;
        if ($last) {
            $pos = array_search($last, $ids);
            $idx = ($pos === false) ? 0 : ($pos + 1) % count($ids);
        }
        $assigned = $ids[$idx] ?? $ids[0];
        Cache::put('lead_last_assigned_user', $assigned, now()->addHours(6));
        return $assigned;
    }

    protected static function userHasRole($user, $role)
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }
        return (property_exists($user, 'role_key') && $user->role_key === $role);
    }
}
