<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('dashboard');
        }

        return view('auth.login');
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->has('remember');

        if (Auth::attempt($credentials, $rememberMe)) {
            $user = Auth::user();

            return redirect()->route('dashboard')->with('success', 'You have Successfully logged in!!');
        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'inactive' => 'You have entered invalid credentials',
            ]);

        }
    }

}
