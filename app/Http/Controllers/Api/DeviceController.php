<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    /**
     * Dapatkan daftar perangkat milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $devices = $user->devices;
        return response()->json($devices);
    }

    /**
     * Dapatkan detail satu perangkat (termasuk setting).
     */
    public function show($id)
    {
        $user = Auth::user();
        $device = $user->devices()->where('device_id', $id)->first();

        if (!$device) {
            return response()->json(['error' => 'Perangkat tidak ditemukan atau Anda tidak memiliki akses.'], 403);
        }

        return response()->json($device);
    }

    /**
     * Pairing perangkat baru menggunakan mac_address (atau device key).
     */
    public function pair(Request $request)
    {
        $request->validate([
            'mac_address' => 'required|string',
            'name' => 'nullable|string'
        ]);

        $user = Auth::user();

        // Cek apakah device sudah terdaftar di sistem
        $device = Device::where('mac_address', $request->mac_address)->first();

        if ($device) {
            // Jika device sudah ada, cek apakah sudah dimiliki orang lain sebagai master
            $hasMaster = $device->users()->wherePivot('role', 'master')->exists();
            if ($hasMaster) {
                // Periksa apakah user ini sudah diundang (punya role)
                $existingRole = $device->users()->where('user_id', $user->id)->first();
                if ($existingRole) {
                    return response()->json(['message' => 'Anda sudah terhubung dengan perangkat ini.'], 200);
                }
                return response()->json(['error' => 'Perangkat ini sudah memiliki Master. Minta Master untuk mengundang Anda dari menu Akses Keluarga.'], 403);
            }
        } else {
            // Buat device baru jika belum ada di database
            $device = Device::create([
                'mac_address' => $request->mac_address,
                'name' => $request->name ?? 'Jemuran Cerdas',
            ]);
        }

        // Hubungkan user dengan device sebagai master
        $user->devices()->attach($device->id, ['role' => 'master']);

        return response()->json([
            'message' => 'Perangkat berhasil dipasangkan!',
            'device' => $device
        ], 201);
    }

    /**
     * Update setting perangkat (Threshold, Mode, dll).
     */
    public function updateSetting(Request $request, $id)
    {
        $user = Auth::user();
        $device = $user->devices()->where('device_id', $id)->first();

        if (!$device) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $device->update($request->only([
            'is_auto_mode',
            'ldr_threshold',
            'rain_threshold',
            'manual_position',
            'name'
        ]));

        return response()->json(['status' => 'success', 'device' => $device]);
    }

    /**
     * Undang anggota keluarga menggunakan email.
     */
    public function inviteMember(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);
        $user = Auth::user();
        $device = $user->devices()->where('device_id', $id)->wherePivot('role', 'master')->first();

        if (!$device) {
            return response()->json(['error' => 'Hanya Master yang bisa mengundang.'], 403);
        }

        $member = User::where('email', $request->email)->first();
        if (!$member) {
            return response()->json(['error' => 'Pengguna dengan email tersebut tidak ditemukan.'], 404);
        }

        if ($device->users()->where('user_id', $member->id)->exists()) {
            return response()->json(['error' => 'Pengguna sudah memiliki akses ke perangkat ini.'], 400);
        }

        $device->users()->attach($member->id, ['role' => 'member']);
        return response()->json(['status' => 'success', 'message' => 'Anggota keluarga berhasil diundang.']);
    }

    /**
     * Hapus akses anggota keluarga.
     */
    public function removeMember($id, $memberId)
    {
        $user = Auth::user();
        $device = $user->devices()->where('device_id', $id)->wherePivot('role', 'master')->first();

        if (!$device) {
            return response()->json(['error' => 'Hanya Master yang bisa menghapus akses.'], 403);
        }

        $device->users()->detach($memberId);
        return response()->json(['status' => 'success', 'message' => 'Akses anggota keluarga dicabut.']);
    }

    /**
     * Dapatkan daftar anggota untuk perangkat tertentu.
     */
    public function getMembers($id)
    {
        $user = Auth::user();
        $device = $user->devices()->where('device_id', $id)->first();

        if (!$device) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        // Return users with their role on this device
        $members = $device->users()->select('users.id', 'users.name', 'users.email', 'device_user.role', 'device_user.created_at')->get();
        return response()->json($members);
    }
}
