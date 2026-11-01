<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if ($request->password !== config('admin.password')) {
            return back()->withErrors([
                'password' => 'Invalid password.',
            ]);
        }

        session([
            'admin_authenticated' => true,
        ]);

        return redirect()->route('admin.stripe.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()->route('admin.login');
    }
    public function showLogin()
    {
        return view('admin.login');
    }


}
