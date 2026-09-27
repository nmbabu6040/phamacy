<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if (Auth::attempt($credentials, $request->boolean("remember"))) {
            $request->session()->regenerate();
            ActivityLog::record("login", "Auth", auth()->user()->name . " logged in.");
            return redirect()->intended(route("admin.dashboard"));
        }

        return back()->withErrors(["email" => "Invalid email or password."])->onlyInput("email");
    }

    public function logout(Request $request)
    {
        ActivityLog::record("logout", "Auth", auth()->user()->name . " logged out.");
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route("login");
    }
}
