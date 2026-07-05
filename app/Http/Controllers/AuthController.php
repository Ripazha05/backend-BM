<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi salah.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'customer',
        ]);

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // --- SELESAIKAN MASALAH: TAMBAHKAN FUNGSI BARU INI DI BAWAH REGISTER ---
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validasi input dari form React
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' // Batas maks 2MB
        ]);

        $data = $request->only(['name', 'phone', 'address']);

        // Logika handling file gambar avatar jika ada yang diunggah
        if ($request->hasFile('avatar')) {
            // Hapus file avatar lama dari server agar storage tidak penuh
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan gambar baru ke folder storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Update data user di tabel database
        $user->update($data);

        return response()->json([
            'message' => 'Profil dan foto berhasil diperbarui!',
            'user' => $user
        ]);
    }
}
