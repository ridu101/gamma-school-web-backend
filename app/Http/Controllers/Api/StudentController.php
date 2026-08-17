<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Get all students
    public function index()
    {
        $students = Student::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    // Create a new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|unique:students,student_id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'class' => 'required|string|max:100',
            'section' => 'nullable|string|max:50',
            'roll' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ], 201);
    }

    // Get a single student
    public function show(Student $student)
    {
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    // Update a student
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|string|unique:students,student_id,' . $student->id,
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'class' => 'sometimes|string|max:100',
            'section' => 'nullable|string|max:50',
            'roll' => 'sometimes|string|max:50',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    // Delete a student
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }
}