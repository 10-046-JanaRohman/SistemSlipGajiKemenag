<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nip' => ['required_without:email'],
            'email' => ['required_without:nip', 'email'],
            'password' => ['required'],
            'remember' => ['nullable', 'boolean'],
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

        $expiresAt = $request->boolean('remember') ? now()->addDays(30) : now()->addHours(12);
        $token = $user->createToken('react-token', ['*'], $expiresAt)->plainTextToken;

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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        ResetPasswordNotification::createUrlUsing(function (User $user, string $token): string {
            return rtrim(config('app.frontend_url'), '/')
                .'/reset-password?token='.urlencode($token)
                .'&email='.urlencode($user->email);
        });

        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Tautan pengaturan ulang password telah dikirim ke email Anda.'
                : 'Email tidak terdaftar atau tautan reset tidak dapat dibuat.',
        ], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'success' => $status === Password::PASSWORD_RESET,
            'message' => $status === Password::PASSWORD_RESET
                ? 'Password berhasil diubah. Silakan masuk menggunakan password baru.'
                : 'Tautan reset password tidak valid atau telah kedaluwarsa.',
        ], $status === Password::PASSWORD_RESET ? 200 : 422);
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
