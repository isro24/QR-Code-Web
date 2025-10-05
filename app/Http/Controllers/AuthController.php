<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

   public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->is_admin) {
                $request->session()->regenerate();
                    return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout(); 
                return back()->withErrors([
                    'email' => 'Akses hanya untuk admin.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
