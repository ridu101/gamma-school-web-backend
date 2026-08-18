<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    // Public + Admin list
    public function index(Request $request)
    {
        $query = Routine::query();

        if ($request->filled('class')) {
            $query->where('class_name', $request->class);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        $routines = $query
            ->orderBy('sort_order')
            ->orderBy('time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $routines,
        ]);
    }

    // Public dropdown options
    public function options()
    {
        $classes = Routine::where('is_active', true)
            ->select('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        $days = Routine::where('is_active', true)
            ->select('day')
            ->distinct()
            ->pluck('day');

        return response()->json([
            'success' => true,
            'data' => [
                'classes' => $classes,
                'days' => $days,
            ],
        ]);
    }

    // Public routine search
    public function search(Request $request)
    {
        $request->validate([
            'class' => 'required|string',
            'day' => 'required|string',
        ]);

        $routines = Routine::where('is_active', true)
            ->where('class_name', $request->class)
            ->where('day', $request->day)
            ->orderBy('sort_order')
            ->orderBy('time')
            ->get([
                'id',
                'class_name',
                'day',
                'time',
                'subject',
                'teacher',
                'room',
            ]);

        return response()->json([
            'success' => true,
            'data' => $routines,
        ]);
    }

    // Admin create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
            'day' => 'required|string|max:50',
            'time' => 'required|string|max:100',
            'subject' => 'required|string|max:150',
            'teacher' => 'required|string|max:150',
            'room' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $routine = Routine::create([
            'class_name' => $validated['class_name'],
            'day' => $validated['day'],
            'time' => $validated['time'],
            'subject' => $validated['subject'],
            'teacher' => $validated['teacher'],
            'room' => $validated['room'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Routine created successfully',
            'data' => $routine,
        ], 201);
    }

    // Admin update
    public function update(Request $request, Routine $routine)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
            'day' => 'required|string|max:50',
            'time' => 'required|string|max:100',
            'subject' => 'required|string|max:150',
            'teacher' => 'required|string|max:150',
            'room' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $routine->update([
            'class_name' => $validated['class_name'],
            'day' => $validated['day'],
            'time' => $validated['time'],
            'subject' => $validated['subject'],
            'teacher' => $validated['teacher'],
            'room' => $validated['room'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Routine updated successfully',
            'data' => $routine,
        ]);
    }

    // Admin delete
    public function destroy(Routine $routine)
    {
        $routine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Routine deleted successfully',
        ]);
    }
}