<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nip' => ['required_without:email'],
            'email' => ['required_without:nip', 'email'],
            'password' => ['required'],
        ], [
            'nip.required_without' => 'NIP atau Email harus diisi.',
            'email.required_without' => 'NIP atau Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password harus diisi.',
        ]);

        $credentials = $request->only('password');

        if ($request->filled('email')) {
            $credentials['email'] = $request->email;
        } else {
            $credentials['nip'] = $request->nip;
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'NIP/Email atau password salah.'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('react-token')->plainTextToken;

        return response()->json([
            'success'=>true,
            'message'=>'Login berhasil.',
            'token'=>$token,
            'user'=>$user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Logout berhasil.'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success'=>true,
            'data'=>$request->user()
        ]);
    }

    public function profil(Request $request)
    {
        $user = $request->user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'pegawai' => $pegawai,
            ]
        ]);
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'current_password.required' => 'Password lama harus diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.required' => 'Password baru harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $user = $request->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
