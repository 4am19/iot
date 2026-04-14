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
        $setting = \App\Models\DeviceSetting::first();
        if ($setting && $setting->device_key) {
           $key = $request->header('X-API-KEY') ?? $request->input('api_key');
           if ($key !== $setting->device_key) {
               return response()->json(['error' => 'Akses Perangkat Ditolak (API Key Invalid)'], 401);
           }
        }
        return $next($request);
    }
}
