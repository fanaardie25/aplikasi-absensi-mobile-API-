<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 1. CEK STATUS AKTIF
            if ($user->is_active !== true) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda sudah tidak aktif'
                ], 403);
            }

            // 2. CEK APAKAH SUDAH DI-PLOTTING KE KELAS
            if (is_null($user->class_id)) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum terdaftar di kelas manapun. Hubungi Admin'
                ], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'data' => $user
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah.'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);

    }
}
