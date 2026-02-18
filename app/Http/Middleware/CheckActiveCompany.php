<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If session has active_company and user has access, allow
        if (session()->has('active_company')) {
            $company = session('active_company');
            $access = $user->company_access ?? [];

            if (in_array($company, $access)) {
                return $next($request);
            }

            // invalid session company -> clear and force selection
            session()->forget('active_company');
            return redirect()->route('company.select');
        }

        // No active company set: if single company, set and continue; if multiple, redirect to select.
        $access = $user->company_access ?? [];

        if (count($access) === 1) {
            session(['active_company' => $access[0]]);
            return $next($request);
        }

        // 0 or >1 companies => force company select
        return redirect()->route('company.select');
    }
}
