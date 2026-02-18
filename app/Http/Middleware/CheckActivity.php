<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $user = Session::get('staff');
        // Check if the user is blocked
        // if ($user) {
        //     // Redirect to dashboard or any other route
        //     return redirect()->route('Dashboard')->with('error', 'You are blocked from accessing the site.');
        // }

        return $next($request);
    }
}
