<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CompanyController extends Controller
{
    public function choosePage()
    {
        $user = Auth::user();
        $companies = $user->company_access ?? [];
        return view('auth.company-select', compact('companies'));
    }

    public function selectCompany(Request $request)
    {
        $request->validate(['company' => 'required|string']);

        $user = Auth::user();
        $access = $user->company_access ?? [];

        if (!in_array($request->company, $access)) {
            return back()->withErrors(['company' => 'You do not have access to the selected company.']);
        }

        session(['active_company' => $request->company]);

        // redirect to dashboard for selected company
        return redirect()->route('dashboard.' . $request->company);
    }
}
