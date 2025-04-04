<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show login page
    public function loginPage()
    {
        return view('login');
    }

    // Handle admin login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('admin.products.index');
        }
        return redirect()->back()->with('error', 'Invalid login credentials');
    }

    // Logout admin
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
