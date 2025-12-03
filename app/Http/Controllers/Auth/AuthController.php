<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        // Determine whether identity is email or mobile
        $field = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $credentials = [$field => $request->identity, 'password' => $request->password];

        $remember = (bool) $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // If using email verification requirement
            // if ($user->email && !$user->email_verified_at) {
            //     Auth::logout();
            //     return back()->withErrors(['email' => 'Please verify your email before logging in.']);
            // }

            // If user has single company access set, store it; if multiple, go to company select
            $access = $user->company_access ?? [];

            if (count($access) === 1) {
                session(['active_company' => $access[0]]);
                return redirect()->intended(route('dashboard'));
            }

            if (count($access) > 1) {
                return redirect()->route('company.select');
            }

            // No company set: optional fallback
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['identity' => 'Invalid credentials.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['nullable','email','max:255', Rule::unique('users','email')],
            'mobile'   => ['nullable','string','max:20', Rule::unique('users','mobile')],
            'password' => 'required|string|min:6|confirmed',
            'company_access' => 'nullable|array',
            'company_access.*' => 'string',
        ]);

        $user = User::create([
            'fname' => $request->name, // adapt to your column names (you use fname/lname)
            'lname' => $request->lname ?? '',
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'company_access' => $request->company_access ?? ['sunraise'], // default
            'status' => 1,
            'role' => $request->role ?? 2,
        ]);

        // send verification if email present
        if ($user->email) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()->route('login')->with('success','Registration successful. Please verify email (if provided).');
    }

    public function logout()
    {
        Auth::logout();
        session()->forget('active_company');
        return redirect()->route('login');
    }
}
