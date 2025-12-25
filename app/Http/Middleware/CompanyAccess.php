<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyAccess
{
    public function handle(Request $request, Closure $next, ...$allowedCompanies)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        // User company access array
        $userCompanies = collect($user->company_access)->toArray();

        // Is active company selected?
        $activeCompany = session('active_company');

        if (! $activeCompany) {
            return redirect()->route('company.select')
                    ->with('error', 'Please select a company');
        }

        // If allowedCompanies is empty → middleware allows all companies
        if (! empty($allowedCompanies)) {
            $allowedCompanies = array_map('strtolower', $allowedCompanies);
            $active = strtolower($activeCompany);

            if (! in_array($active, $allowedCompanies)) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not allowed to access this section.');
            }
        }

        return $next($request);
    }
}
