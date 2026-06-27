<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeviceKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $macAddress = $request->header('X-MAC-ADDRESS') ?? $request->input('mac_address');
        
        if (!$macAddress) {
            return response()->json(['error' => 'Akses Ditolak (MAC Address tidak ditemukan)'], 401);
        }

        // Simpan mac_address ke request untuk dipakai di controller
        $request->merge(['mac_address' => $macAddress]);
        
        return $next($request);
    }
}
