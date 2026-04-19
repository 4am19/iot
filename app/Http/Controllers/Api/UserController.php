<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Daftar semua user (untuk admin: semua, member: hanya dirinya sendiri).
     */
    public function index()
    {
        // Gunakan CASE WHEN agar kompatibel MySQL, SQLite, dan PostgreSQL
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($users);
    }

    /**
     * Tambah anggota baru (hanya admin yang boleh).
     */
    public function store(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            return response()->json(['error' => 'Hanya admin yang boleh menambah anggota.'], 403);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role'     => ['nullable', Rule::in(['admin', 'member'])],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'] ?? 'member',
        ]);

        return response()->json(['status' => 'success', 'user' => $user], 201);
    }

    /**
     * Update data anggota.
     */
    public function update(Request $request, $id)
    {
        $user        = User::findOrFail($id);
        $currentUser = Auth::user();

        // Member hanya boleh edit dirinya sendiri; admin boleh edit siapa saja
        if (!$currentUser?->isAdmin() && $currentUser?->id !== $user->id) {
            return response()->json(['error' => 'Tidak punya izin.'], 403);
        }

        // Cegah satu-satunya admin diturunkan jadi member
        if ($user->role === 'admin' && $request->input('role') === 'member') {
            if (User::where('role', 'admin')->count() <= 1) {
                return response()->json(['error' => 'Tidak dapat mengubah satu-satunya admin menjadi member.'], 422);
            }
        }

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => ['nullable', Rule::in(['admin', 'member'])],
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:4';
        }

        $validated = $request->validate($rules);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['role']) && $currentUser?->isAdmin()) {
            $user->role = $validated['role'];
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    /**
     * Hapus anggota (hanya admin).
     */
    public function destroy($id)
    {
        $user        = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser?->isAdmin()) {
            return response()->json(['error' => 'Hanya admin yang boleh menghapus akun.'], 403);
        }

        // Cegah admin menghapus admin terakhir
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return response()->json(['error' => 'Tidak bisa menghapus satu-satunya admin.'], 422);
        }

        // Admin tidak bisa hapus dirinya sendiri
        if (Auth::guard('web')->id() === $user->id) {
            return response()->json(['error' => 'Tidak bisa menghapus akun sendiri saat sedang login.'], 403);
        }

        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun berhasil dihapus.']);
    }

    /**
     * Ganti password diri sendiri.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['error' => 'Password lama tidak sesuai.'], 422);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}
