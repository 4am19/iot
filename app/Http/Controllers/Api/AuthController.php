<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

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
            $user = Auth::guard('web')->user();
            
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Login berhasil'
            ]);
        }

        return response()->json([
            'error' => 'Kredensial salah atau tidak ditemukan.'
        ], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            AuditLog::create([
                'user_id' => Auth::guard('web')->id(),
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

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
