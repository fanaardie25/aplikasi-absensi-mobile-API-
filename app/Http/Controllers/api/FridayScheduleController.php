<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FridaySchedule;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class FridayScheduleController extends Controller implements HasMiddleware
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
        $schedules = FridaySchedule::with('classes')->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'date'        => ['required', 'date', 'unique:friday_schedules,date'],
            'description' => ['nullable', 'string'],
            'class_ids'   => ['required', 'array'],
            'class_ids.*' => ['exists:school_classes,id'],
        ]);

        try {
            DB::beginTransaction();

            $schedule = FridaySchedule::create([
                'date'        => $request->date,
                'description' => $request->description,
            ]);

            $schedule->classes()->attach($request->class_ids);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Friday schedule created successfully',
                'data'    => $schedule->load('classes')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $schedule = FridaySchedule::with('classes')->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
                ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function destroy($id)
    {
        $schedule = FridaySchedule::find($id);
        
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
                ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! $schedule = FridaySchedule::find($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found',
            ], 404);
        }

        $validated = $request->validate([
            'date'        => ['sometimes', 'date', 'unique:friday_schedules,date,' . $id],
            'description' => ['nullable', 'string'],
            'class_ids'   => ['sometimes', 'array'],
            'class_ids.*' => ['exists:school_classes,id'],
        ]);


        $classIds = $validated['class_ids'] ?? null;
        unset($validated['class_ids']);

        try {
            DB::transaction(function () use ($schedule, $validated, $classIds) {
                $schedule->update($validated);

                if ($classIds) {
                    $schedule->classes()->sync($classIds);
                }
            });


            return response()->json([
                'success' => true,
                'message' => 'Friday schedule updated successfully',
                'data'    => $schedule->load('classes'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
