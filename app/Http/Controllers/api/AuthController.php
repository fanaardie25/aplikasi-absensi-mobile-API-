<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'login.required' => 'NIS atau Email wajib diisi',
        ]);

        // 2. CARI USER BERDASARKAN EMAIL ATAU NIS
        $user = User::where('email', $request->login)
                    ->orWhere('nis', $request->login)
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIS/Email atau password salah.'
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sudah tidak aktif. Hubungi Admin.'
            ], 403);
        }

        if (is_null($user->class_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum terdaftar di kelas manapun. Hubungi Admin.'
            ], 403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'access_token' => $token,
            'data' => $user 
        ]);
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
