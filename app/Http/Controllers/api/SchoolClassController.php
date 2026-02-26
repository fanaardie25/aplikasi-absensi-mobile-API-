<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SchoolClassController extends Controller implements HasMiddleware
{ 

    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    public function index()
    {
        $class = SchoolClass::all();

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'grade' => ['required', 'integer'],
            'major' => ['required', 'string'],
            'sequence' => ['required', 'string'],
            'class_teacher' => ['required', 'string'],
            'academic_year' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $class = SchoolClass::create($data);

        return response()->json([
            'success' => true,
            'data' => $class
        ], 201);
    }

    public function show($id)
    {
        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    public function update(Request $request, $id)
    {
        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found'
            ], 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'grade' => ['sometimes', 'integer'],
            'major' => ['sometimes', 'string'],
            'sequence' => ['sometimes', 'string'],
            'class_teacher' => ['sometimes', 'string'],
            'academic_year' => ['sometimes', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $class->update($data);

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    public function destroy($id)
    {
        $class = SchoolClass::find($id);

        if (!$class) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found'
            ], 404);
        }

        $class->delete();

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully'
        ]);
    }
}
