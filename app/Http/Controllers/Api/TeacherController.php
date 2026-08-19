<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    // ==========================================
    // FORMAT TEACHER DATA
    // ==========================================

    private function formatTeacher(Teacher $teacher)
    {
        return [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'designation' => $teacher->designation,
            'department' => $teacher->department,
            'email' => $teacher->email,
            'phone' => $teacher->phone,
            'bio' => $teacher->bio,

            // Full image URL for frontend
            'photo' => $teacher->photo
                ? request()->getSchemeAndHttpHost() .
                    '/storage/' .
                    $teacher->photo
                : null,

            // Original database path
            'photo_path' => $teacher->photo,

            'is_active' => (bool) $teacher->is_active,
            'created_at' => $teacher->created_at,
            'updated_at' => $teacher->updated_at,
        ];
    }

    // ==========================================
    // PUBLIC TEACHER LIST
    // ==========================================

    public function index()
    {
        $teachers = Teacher::latest()
            ->get()
            ->map(function ($teacher) {
                return $this->formatTeacher($teacher);
            });

        return response()->json([
            'success' => true,
            'data' => $teachers,
        ]);
    }

    // ==========================================
    // PUBLIC SINGLE TEACHER
    // ==========================================

    public function show(Teacher $teacher)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatTeacher($teacher),
        ]);
    }

    // ==========================================
    // ADMIN CREATE TEACHER
    // ==========================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'designation' => 'nullable|string|max:255',

            'department' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255|unique:teachers,email',

            'phone' => 'nullable|string|max:50',

            'bio' => 'nullable|string',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',

            'is_active' => 'nullable|boolean',
        ]);

        $photoPath = null;

        // Image Upload
        if ($request->hasFile('photo')) {
            $photoPath = $request
                ->file('photo')
                ->store('teachers', 'public');
        }

        $teacher = Teacher::create([
            'name' => $validated['name'],

            'designation' =>
                $validated['designation'] ?? null,

            'department' =>
                $validated['department'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'phone' =>
                $validated['phone'] ?? null,

            'bio' =>
                $validated['bio'] ?? null,

            'photo' => $photoPath,

            'is_active' =>
                $request->has('is_active')
                    ? $request->boolean('is_active')
                    : true,
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Teacher created successfully',

            'data' =>
                $this->formatTeacher($teacher),
        ], 201);
    }

    // ==========================================
    // ADMIN UPDATE TEACHER
    // ==========================================

    public function update(
        Request $request,
        Teacher $teacher
    ) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'designation' => 'nullable|string|max:255',

            'department' => 'nullable|string|max:255',

            'email' =>
                'nullable|email|max:255|unique:teachers,email,' .
                $teacher->id,

            'phone' => 'nullable|string|max:50',

            'bio' => 'nullable|string',

            'photo' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',

            'is_active' => 'nullable|boolean',
        ]);

        $photoPath = $teacher->photo;

        // New image selected
        if ($request->hasFile('photo')) {

            // Delete old image
            if (
                $teacher->photo &&
                Storage::disk('public')
                    ->exists($teacher->photo)
            ) {
                Storage::disk('public')
                    ->delete($teacher->photo);
            }

            // Upload new image
            $photoPath = $request
                ->file('photo')
                ->store('teachers', 'public');
        }

        $teacher->update([
            'name' => $validated['name'],

            'designation' =>
                $validated['designation'] ?? null,

            'department' =>
                $validated['department'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'phone' =>
                $validated['phone'] ?? null,

            'bio' =>
                $validated['bio'] ?? null,

            'photo' => $photoPath,

            'is_active' =>
                $request->has('is_active')
                    ? $request->boolean('is_active')
                    : $teacher->is_active,
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Teacher updated successfully',

            'data' =>
                $this->formatTeacher(
                    $teacher->fresh()
                ),
        ]);
    }

    // ==========================================
    // ADMIN DELETE TEACHER
    // ==========================================

    public function destroy(Teacher $teacher)
    {
        // Delete image file
        if (
            $teacher->photo &&
            Storage::disk('public')
                ->exists($teacher->photo)
        ) {
            Storage::disk('public')
                ->delete($teacher->photo);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Teacher deleted successfully',
        ]);
    }
}