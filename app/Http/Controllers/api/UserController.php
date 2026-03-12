<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends Controller implements HasMiddleware
{
    
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware('role:admin', except: ['getLatestActivity','getAllActivity','updatePhotoProfile']),
        ];
    }

    public function index()
    {
        $user = User::all();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,student'],
            'nis' => ['nullable', 'string', 'unique:users,nis'],
            'class_id' => ['nullable', 'exists:school_classes,id'],
            'profile_photo_path' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'nis' => $request->nis,
            'class_id' => $request->class_id,
            'profile_photo_path' => $request->profile_photo_path,
        ]);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }


    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $id],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', 'string', 'in:admin,student'],
            'nis' => ['nullable', 'string', 'unique:users,nis,' . $id],
            'class_id' => ['nullable', 'exists:school_classes,id'],
            'profile_photo_path' => ['nullable', 'string'],
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

 
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function getLatestActivity()
    {
        $user = FacadesAuth::user();
        $activity = Attendance::select([
                'id', 
                'status', 
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as date")
            ])
            ->where('student_id', $user->id)
            ->where('status', 'hadir')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    public function getAllActivity()
    {
        $user = FacadesAuth::user();
        $activity = Attendance::select([
                'id', 
                'status', 
                'photo_path',
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as date")
            ])
            ->where('student_id', $user->id)
            ->where('status', 'hadir')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    public function updatePhotoProfile(Request $request){

        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        $user = FacadesAuth::user();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $imageName = time() . '_' . $user->id . '.webp';
            
            $image = Image::read($file);
            $image->scale(width: 600); 
            $encoded = $image->toWebp(65);
            
            Storage::disk('public')->put('profile/' . $imageName, (string) $encoded);

            $path = 'profile/' . $imageName;

            $currentUser = User::find($user->id);

            if ($currentUser->profile_photo_path) {
                Storage::disk('public')->delete($currentUser->profile_photo_path);
            }

            $currentUser->update([
                'profile_photo_path' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
            ]);

        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 400);
    }
}
