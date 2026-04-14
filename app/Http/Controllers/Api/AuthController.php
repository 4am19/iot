<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt authentication using web guard (session stateful)
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'user' => Auth::guard('web')->user(),
                'message' => 'Login berhasil'
            ]);
        }

        return response()->json([
            'error' => 'Kredensial salah atau tidak ditemukan.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function check(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::guard('web')->check(),
            'user' => Auth::guard('web')->user()
        ]);
    }
}
