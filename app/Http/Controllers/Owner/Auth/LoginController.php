<?php

namespace App\Http\Controllers\Owner\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Owner\LoginRequest;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function login()
    {
        $owner = Auth::guard('owner')->user();
        
        if(Auth::guard('web')->check()){
            return redirect()->route('dashboard');
        }
        if (Auth::guard('owner')->check()) {
            if($owner->store){
                return redirect()->route('dashboard');
            }
            return redirect()->route('type');
        }

        return Inertia::render('Owner/Auth/Login');
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
    
        if (Auth::guard('owner')->attempt($credentials, $remember)) {
            // Authentication passed
            return redirect()->route('type');
        }
    
        // Authentication failed, return with error message
        return redirect()->route('owner.login')->withErrors([
            'message' => __('auth.failed')
        ]);
    }
    

    // Logout 
    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('owner.login');
    }
}
